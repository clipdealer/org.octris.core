# vim:set noexpandtab:

#**
# Makefile for handling targets only available for core project.
#
# @octdoc       h:./Makefile
# @copyright    copyright (c) 2006-2011 by Harald Lapp
# @author       Harald Lapp <harald@octris.org>
#**

include Makefile.core

project = "org.octris.core"
target = ""

#**
# Create and initialize a new project.
#
# @octdoc       t:Makefile/new
#**
new:
	@if [ "$(project)" = "org.octris.core" ]; then \
		echo ""; \
		echo "please specify a project as commandline parameter"; \
		echo ""; \
		echo "eg.:"; \
		echo "       make new project=com.example..."; \
		echo ""; \
		exit 1; \
	elif [ -d ../$(project) ]; then \
		echo ""; \
		echo "project '$(project)' already exists!"; \
		echo "please specify an other project as commandline parameter"; \
		echo ""; \
		echo "eg.:"; \
		echo "       make new project=com.example..."; \
		echo ""; \
		exit 1; \
	fi	
	@../../tools/org.octris.core/project/create.php -p $(project)
