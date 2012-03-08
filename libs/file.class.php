<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core {
	/**
	 * File object.
	 *
	 * @octdoc		c:core/file
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
	class file implements \Iterator, \SeekableIterator
	/**/
	{
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
		 * Whether to delete the file, when it get's closed (deconstructed).
		 *
		 * @octdoc  p:file/$delete_on_close
		 * @var     bool
		 */
		private $delete_on_close = false;
		/**/

		/**
		 * 
		 *
		 * @octdoc  p:file/$current
		 * @var     
		 */
		protected $current;
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
		 * @param 	bool 						$delete 		Whether to delete file, when object is get's deconstructed.
		 */
		public function __construct($file, $open_mode = 'r', $delete = false)
		/**/
		{
		    if (is_resource($file)) {
		    	$info = stream_get_meta_data($file);

		    	$this->setFlags($info['mode']);

		    	$this->fh = $file;
		    } elseif (is_string($file)) {
		    	$this->setFlags($open_mode);

		    	if (!($this->fh = @fopen($file, $open_mode))) {
		    		$info = error_get_last();

		    		throw new \Exception($info['message'], $info['type']);
		    	}
		    }

		    if ($this->isLocal()) {
		    	$this->delete_on_close = $delete;	
		    }
		}

		/**
		 * Destructor closes open file handle.
		 *
		 * @octdoc  m:file/__destruct
		 */
		public function __destruct()
		/**/
		{
			if ($this->delete_on_close) {
				$info = stream_get_meta_info(this->fh);
				$path = parse_url($info['uri'], PHP_URL_PATH);

			    fclose($this->fh);

			    if (file_exists($path)) unlink($path);
			} else {
			    fclose($this->fh);
			}
		}

		/**
		 * Set file flags according to open mode.
		 *
		 * @octdoc  m:file/setFlags
		 */
		private function setFlags($mode)
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
		 * 
		 *
		 * @octdoc  m:file/getBasename
		 */
		public function getBasename()
		/**/
		{
		    return basename($)
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
		 * Read from file.
		 *
		 * @octdoc  m:file/read
		 * @param 	int 				$len 					Optional number of bytes to read from file.
		 * @return 	string 										Read bytes.
		 */
		public function read($len = null)
		/**/
		{
			return fgets($this->fh, $len);
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
		 * @octdoc  m:file/seek
		 */
		public function seek($offset, $flag = SEEK_SET)
		/**/
		{
	    	fseek($this->fh, $offset, $flag);
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

		/** iterator interfaces **/

        /**
         * Return current row of file.
         *
         * @octdoc  m:file/current
         * @return  string 													Current row of file.
         */
        public function current()
        /**/
        {
        }

        /**
         * Return number of current row.
         *
         * @octdoc  m:file/key
         * @return  int 													Number of current row.
         */
        public function key()
        /**/
        {
        }

        /**
         * Rewind file to beginning.
         *
         * @octdoc  m:file/rewind
         */
        public function rewind()
        /**/
        {
        }

        /**
         * Fetch next row.
         *
         * @octdoc  m:file/next
         */
        public function next()
        /**/
        {
        }

        /**
         * Check if eof is reached.
         *
         * @octdoc  m:file/valid
         * @return  bool                                                    Returns true, if eof is not reached.
         */
        public function valid()
        /**/
        {
            return $this->collection->isValid($this->position);
        }

        /**
         * Seek to row.
         *
         * @octdoc  m:file/seek
         * @param   int         $row                                   		Row of file to seek to.
         */
        public function seek($row)
        /**/
        {
        }		
	}
}
