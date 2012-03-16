<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\tpl {
    /**
     * Implements static methods for auto-escaping functionality.
     *
     * Related articles:
     *
     * * https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet
     *
     * @octdoc      c:tpl/escape
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class escape
    /**/
    {
    	/**
    	 * Escape content to put into HTML context to prevent XSS attacks.
    	 *
    	 * @octdoc  m:escape/escapeHtml
    	 */
    	public static function escapeHtml($str)
    	/**/
    	{
    	    
    	}
    }
}
