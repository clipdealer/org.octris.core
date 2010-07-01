# vim:set noexpandtab:
#

#****h* Makefile
# NAME
#	Makefile
# FUNCTION
#	base makefile for handling targets only available from core project
# COPYRIGHT
#	copyright (c) 2006-2010 by Harald Lapp
# AUTHOR
#	Harald Lapp <harald.lapp@gmail.com>
#
#****
#

include Makefile.base

target = ""

# create new project
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

# reinstall all projects
reinstall:
	@if [ "$(devel)" = "" ]; then \
		echo ""; \
		echo "please specify whether to install in development or production environment"; \
		echo ""; \
		echo "eg.:"; \
		echo "       make reinstall devel=1          // development environment"; \
		echo "       make reinstall devel=0          // production environment"; \
		echo ""; \
		exit 1; \
	fi
	@if [ "$(devel)" = "0" ]; then \
		if [ "$(target)" = "" ]; then \
			echo ""; \
			echo "deploy target is required when executed in production environment"; \
			echo ""; \
			echo "eg.:"; \
			echo "       make reinstall devel=0 target=live"; \
			echo ""; \
			exit 1; \
		fi; \
	elif [ "$(devel)" != "1" ]; then \
		echo ""; \
		echo "unknown value for 'devel'"; \
		echo ""; \
		exit 1; \
	fi
	@../../tools/org.octris.core/project/reinstall.sh $(CURSYMDIR)/../../ $(devel) $(target)
