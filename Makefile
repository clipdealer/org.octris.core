# vim:set noexpandtab:
#

#****h* Makefile
# NAME
#	Makefile
# FUNCTION
#	Makefile for handling targets only available for core project
# COPYRIGHT
#	copyright (c) 2006-2010 by Harald Lapp
# AUTHOR
#	Harald Lapp <harald.lapp@gmail.com>
#****
#

include Makefile.core

target = ""

#****t* Makefile/new
# NAME
#	make new
# FUNCTION
#	create and initialize a new project
#****
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
	@../../tools/org.octris.core/project/init.sh $(project)
