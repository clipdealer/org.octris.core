#!/usr/bin/env bash

#**
# Bash completion script.
#
# @octdoc       h:etc/bash-complete.sh
# @copyright    copyright (c) 2011 by Harald Lapp
# @author       Harald Lapp <harald@octris.org>
#**

if [ "$OCTRIS_BASE" = "" ]; then
    echo "OCTRIS_BASE is not set!"
#    exit 1
fi

function _core {
    echo ""
    echo "help"
    echo "create"
}

complete -F _core octris