#!/usr/bin/env php
<?php

/*
 * This file is part of the '{{$directory}}' package.
 *
 * (c) {{$author}} <{{$email}}>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{$namespace}}\{{$module}} {
    /**
     * {{$description}}
     *
     * @octdoc      h:tools/{{$module}}
     * @copyright   copyright (c) {{$year}} by {{$company}}
     * @author      {{$author}} <{{$email}}>
     */
    /**/

    $_ENV['OCTRIS_APP'] = '{{$namespace}}';

    // include core cli application library
    require_once('org.octris.core/app/cli.class.php');
    
    // run application
    app\main::getInstance()->process();
}
