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
	class file
	/**/
	{
		/**
		 * The mode, the file was opened in.
		 *
		 * @octdoc  p:file/$open_mode
		 * @var     string
		 */
		private $open_mode;
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
		 */
		public function __construct($file, $open_mode = 'r')
		/**/
		{
		    if (is_resource($file)) {

		    } elseif (is_string($file)) {
		    	if (!is_file($file)) {
			    	$dir = dirname($file);


		    	}

		    	$this->open_mode = $open_mode;
		    }
		}
	}
}
