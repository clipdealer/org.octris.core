#!/usr/bin/env php
<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\doc {
    /**
     * Render ebnf of template compiler.
     *
     * @octdoc      h:doc/ebnf
     * @copyright   copyright (c) 2014 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    /**/

    require_once('org.octris.core/app/cli.class.php');
    
    $grammar = new \org\octris\core\tpl\compiler\grammar();
    print $grammar->getEBNF();
}
