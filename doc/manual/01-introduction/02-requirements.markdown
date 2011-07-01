Requirements
============

Development and production server
---------------------------------

It's higly recommended to use this framework in a UN\*X environment, like 
for example Linux and Mac OS X, because the framework makes use of lot's
of tools and system-characteristics found on these systems. The framework
is developed and tested using Mac OS X and Linux, so you should not have
any problems to use it on either of these operating systems. 

It's not recommended to use this framework with Windows, even though it 
should work using tools emulating a UN\*X-like environment for example 
Cygwin. Because windows environment is untested, the manual will concentrate
only on Linux and Mac OS X for requirements and everywhere else where system
relevant informations are provided.

The following software needs to be available on both development and 
production servers:

*   PHP 5.3.x

    The OCTRiS framework will always be compatible with the latest stable 
    release of PHP. The minimal version number should be -- as of now,
    July 2011 -- PHP 5.3.6.
    
*   GNU make

PHP and extensions
------------------

The following PHP extensions are required. Without them, the framework might 
not work properly or even not at all:

*   gettext
*   intl
*   mbstring

The following PHP extensions are highly recommended, but not required to be 
installed. The core framework will work perfectly without them:

*   mcrypt
*   readline
