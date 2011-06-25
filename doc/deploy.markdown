% Deployment strategy
% Harald Lapp (<harald@octris.org>)
% July, 2011

Ideas
=====

*   green / blue deployment

Configuration files
===================

Each module has a /etc/ directory in it's root folder. One can store an arbitrary amount of configurarion files for 
arbitrary use cases in this directory. The directory is individual of the /etc/ directory located under the framework
root directory. A configuration file in the modules directory should be linked.

*   etc is an own directory, the configuration files are edited by hand in it -- there should be only
    sample configuration files in the repository


