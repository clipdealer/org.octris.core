# vim:set noexpandtab:

#
# This file is part of the '{{$directory}}' package.
#
# (c) {{$author}} <{{$email}}>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#

#**
# Makefile for {{$directory}}.
#
# @octdoc       h:{{$module}}/Makefile
# @copyright    copyright (c) {{$year}} by {{$company}}
# @author       {{$author}} <{{$email}}>
#**

CURSYMDIR:=$(shell pwd)

project = "{{$directory}}"

#**
# The target help displays general information and available makefile targets.
#
# @octdoc       t:Makefile/help
#**
help::
	@echo "usage: make build"

#**
# Target for executing build-tool.
#
# @octdoc       t:Makefile/build
#**
build:
	@$(CURSYMDIR)/../../tools/org.octris.core/project/build.php -p $(project)
