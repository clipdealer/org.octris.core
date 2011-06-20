#!/usr/bin/env bash

#**
# Requirement file for project. This file is read by the "check" utility, to determine
# if all requirements are fulfilled.
#
# @octdoc       h:etc/requires
# @copyright    copyright (c) 2011 by Harald Lapp
# @author       Harald Lapp <harald@octris.org>
#**

PHP_VERSION=""

test_php() {
    if [ "`which php`" = "" ]; then
        echo "no PHP found in search path"
    fi
    
    if [ ""]
}

test_php