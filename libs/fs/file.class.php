<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\fs {
	/**
	 * File object.
	 *
	 * @octdoc		c:fs/file
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class file implements \IteratorAggregate
	/**/
	{
		/**
		 * File handling flags:
		 * 
		 * * T_READ_TRIM_NEWLINE -- Remove trailing newline characters.
		 * * T_DELETE_ON_CLOSE -- Whether to delete file when object is deconstructed.
		 * 
		 * @octdoc	d:file/T_READ_TRIM_NEWLINE
		 */
		const T_READ_TRIM_NEWLINE = 1;
		const T_DELETE_ON_CLOSE   = 2;
		/**/

		/**
		 * File opening mode.
		 *
		 * @octdoc  p:file/$open_mode
		 * @var     string
		 */
		private $open_mode = '';
		/**/

		/**
		 * File modes and read/write bit mapping:
		 *
		 * bit 1 - reading is allowed
		 * bit 2 - writing is allowed
		 *
		 * @octdoc  p:file/$modes
		 * @var     array
		 */
		private static $modes = array(
			'r'	 => 1, 'r+' => 3,
			'w'  => 2, 'w+' => 3,
			'a'  => 2, 'a+' => 3,
			'x'  => 2, 'x+' => 3,
			'c'  => 2, 'c+' => 3
		);
		/**/

		/**
		 * File handle.
		 *
		 * @octdoc  p:file/$fh
		 * @var     resource
		 */
		private $fh = null;
		/**/
		
		/**
		 * If reading from file is possible.
		 *
		 * @octdoc  p:file/$can_read
		 * @var     bool
		 */
		private $can_read = false;
		/**/
		
		/**
		 * If writing to file is possible.
		 *
		 * @octdoc  p:file/$can_write
		 * @var     bool
		 */
		private $can_write = false;
		/**/

		/**
		 * If file is opened in binary mode.
		 *
		 * @octdoc  p:file/$is_binary
		 * @var     bool
		 */
		private $is_binary = false;
		/**/

		/**
		 * File handling flags.
		 * 
		 * @octdoc	p:file/$flags
		 * @var 	int
		 */
		private $flags = 0;
		/**/

		/**
		 * Meta data available for the s
		 *
		 * @octdoc  p:file/$meta
		 * @var     array
		 */
		protected $meta = array();
		/**/

		/**
		 * Constructor. Takes either a name of file to read/write or a stream-resource. The 
		 * second parameter will be ignored, if the first parameter is a stream-resource. If
		 * the first parameter is a string, it is considered to be a filename. The constructor
		 * checks, if the file.
		 *
		 * @octdoc  m:file/__construct
		 * @param 	string|resource 			$file 			Stream resource or filename.
		 * @param 	string 						$open_mode 		File open mode.
		 * @param 	int 						$flags 			Additional flags to set.
		 */
		public function __construct($file, $open_mode = 'r', $flags = 0)
		/**/
		{
		    if (is_resource($file)) {
		    	$this->meta = stream_get_meta_data($file);

		    	$this->setProperties($this->meta['mode']);

		    	$this->fh = $file;
		    } elseif (is_string($file)) {
		    	$this->setProperties($open_mode);

		    	if (!($this->fh = @fopen($file, $open_mode))) {
		    		$info = error_get_last();

		    		throw new \Exception($info['message'], $info['type']);
		    	} else {
			    	$this->meta = stream_get_meta_data($this->fh);
		    	}
		    }

		    if (($flags & self::T_DELETE_ON_CLOSE) == self::T_DELETE_ON_CLOSE && !$this->isLocal()) {
		    	// remove 'delete on close' flag, if file is not local
		    	$flags = $flags ^ self::T_DELETE_ON_CLOSE;

		    	trigger_error("remote file cannot be deleted");
		    }

		    $this->flags = $flags;
		}

		/**
		 * Destructor closes open file handle.
		 *
		 * @octdoc  m:file/__destruct
		 */
		public function __destruct()
		/**/
		{
			if (($this->flags & self::T_DELETE_ON_CLOSE) == self::T_DELETE_ON_CLOSE) {
				$path = parse_url($this->meta['uri'], PHP_URL_PATH);

			    fclose($this->fh);

			    if (file_exists($path)) unlink($path);
			} else {
			    fclose($this->fh);
			}
		}

		/**
		 * Return file URI if file object is casted to a string.
		 *
		 * @octdoc  m:file/__toString
		 * @return 	string 										URI of file.
		 */
		public function __toString()
		/**/
		{
		    return $this->meta['uri'];
		}

		/**
		 * Return a new fileiterator instance for iterating file contents. Note, that this will open
		 * another file handle to the file in read-only mode.
		 *
		 * @octdoc  m:file/getIterator
		 * @return 	\org\octris\core\fs\fileiterator 			Instance of fileiterator.
		 */
		public function getIterator()
		/**/
		{
			return new \org\octris\core\fs\fileiterator($this->meta['uri'], $this->flags);
		}

		/**
		 * Set file properties according to open mode: whether it's opened in binary mode or not,
		 * where it's possible to read from and / or write to file.
		 *
		 * @octdoc  m:file/setProperties
		 */
		private function setProperties($mode)
		/**/
		{
			$tmp = $mode;

	    	if (strpos('bt', substr($mode, -1)) !== false) {
	    		$this->is_binary = true;
	    		$mode = substr($mode, 0, -1);
	    	}

	    	if (!isset(self::$modes[$mode])) {
	    		throw new \Exception("Invalid file mode '$tmp'");
	    	} else {
	    		$this->can_read  = (bool)(self::$modes[$mode] & 1);
	    		$this->can_write = (bool)(self::$modes[$mode] & 2);
	    	}
		}

		/**
		 * Returns whether it is possible to read from the file.
		 *
		 * @octdoc  m:file/canRead
		 * @return 	bool 										Returns true in case of reading from file is allowed.
		 */
		public function canRead()
		/**/
		{
		    return $this->can_read;
		}

		/**
		 * Returns whether it is possible to write to the file.
		 *
		 * @octdoc  m:file/canWrite
		 * @return 	bool 										Returns true in case of writing to file is allowed.
		 */
		public function canWrite()
		/**/
		{
		    return $this->can_write;
		}

		/**
		 * Set blocking mode for file.
		 *
		 * @octdoc  m:file/setBlocking
		 */
		public function setBlocking($mode)
		/**/
		{
		    stream_set_blocking($this->fh, $mode);
		}

		/**
		 * Set a callback that will be called for every line read / written. The callback takes two parameters
		 *
		 * @octdoc  m:file/setCallback
		 */
		public function setCallback(callable $callback)
		/**/
		{
		    
		}

		/**
		 * Create a temporary file.
		 *
		 * @octdoc  m:file/createTempFile
		 */
		public static function createTempFile($prefix, $dir = null)
		/**/
		{
		    $file = tempnam((is_null($dir) ? sys_get_temp_dir() : $dir), $prefix);

		    return new self($file, 'w', true);
		}

		/**
		 * Test if a specified file exists.
		 *
		 * @octdoc  m:file/isExist
		 * @param 	string 				$file 					File to test.
		 * @return 	bool 										Returns true if file exists.
		 */
		public static function isExist($file)
		/**/
		{
		    return file_exists($file);
		}

		/**
		 * Check if file is local or not.
		 *
		 * @octdoc  m:file/isLocal
		 * @return 	bool  										Returns true if file is local.
		 */
		public function isLocal()
		/**/
		{
		    return stream_is_local($this->fh);
		}

		/**
		 * Test whether file is seekable.
		 *
		 * @octdoc  m:file/isSeekable
		 * @return 	bool 										Returns true if file is seekable.
		 */
		public function isSeekable()
		/**/
		{
  			return $this->meta['seekable'];
		}

		/**
		 * Read from file.
		 *
		 * @octdoc  m:file/read
		 * @param 	int 				$len 					Optional number of bytes to read from file.
		 * @return 	string 										Read bytes.
		 */
		public function read($len = null)
		/**/
		{
			$row = fgets($this->fh, $len);

			if (($this->flags & self::T_READ_TRIM_NEWLINE) == self::T_READ_TRIM_NEWLINE) {
				rtrim($row, "\n\r");
			}

			return $row;
		}

		/**
		 * Write to file.
		 *
		 * @octdoc  m:file/write
		 */
		public function write($str, $len = null)
		/**/
		{
		    fwrite($this->fh, $str, $len);
		}

		/**
		 * 
		 *
		 * @octdoc  m:file/eof
		 */
		public function eof()
		/**/
		{
			return feof($this->fh);
		}

		/**
		 * Force writing of all buffered output to file.
		 *
		 * @octdoc  m:file/flush
		 */
		public function flush()
		/**/
		{
		    fflush($this->fh);
		}

		/**
		 * 
		 *
		 * @octdoc  m:file/lock
		 */
		public function lock($operation)
		/**/
		{
			if (stream_supports_lock($this->fh)) {
			    flock($this->fh, $operation);
			}
		}

		/**
		 * 
		 *
		 * @octdoc  m:file/passthru
		 */
		public function passthru()
		/**/
		{
		    fpassthru($this->fh);
		}

		/**
		 * 
		 *
		 * @octdoc  m:file/stat
		 */
		public function stat()
		/**/
		{
		    return fstat($this->fh);
		}

		/**
		 * 
		 *
		 * @octdoc  m:file/tell
		 */
		public function tell()
		/**/
		{
		    return ftell($this->fh);
		}

		/**
		 * 
		 *
		 * @octdoc  m:file/truncate
		 */
		public function truncate($size)
		/**/
		{
		    ftruncate($this->fh, $size);
		}
	}
}