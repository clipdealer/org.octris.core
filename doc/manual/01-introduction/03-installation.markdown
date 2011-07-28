Installation
============

Installing the core framework files
-----------------------------------

The OCTRiS framework and the applications developed using the framework have 
to follow a fixed directory layout. Normally one would install the framework
and the applications developed with it in a home directory of either the 
developer or -- on the production server -- in a special user the application
is running as.

In the following we use the user *harald* as example and install everything in 
the home-directory of this user:

    cd /home/harald
    
We need to create a directory in which we want to install the framework and
applications. Good names are for example *www* or *projects*:

    mkdir www
    
This directory is called the **base** directory. We now have to set an environment 
variable. The OCTRiS framework makes use of this variable to determine the base 
directory of it's environment:

    export OCTRIS_BASE=/home/harald/www

You should write this into one of the configuration files that get loaded, when
you open a terminal or login into your shell for example the `.profile` or the
`.bashrc` of the user you are installing in.

Now change the directory to `www`:

    cd www
    
We should now be in the newly created *www* directory. Here we have to create a 
directory of the name `work`. Note, that the framework requires this directory 
name for the stuff we are going to put in it. You may not change the name of the
directory, otherwise the framework will not work:

    mkdir work

This is our **workspace**. The framework source package needs to get here and all
application source packages will be created here. Download the latest release
of the OCTRiS framework into this directory and extract it:

    tar xfz octris-x.x.x.tgz

If you have done everything correctly you should now have a directory path,
which looks like the following:

    /home/harald/www/work/org.octris.core
                
Next *cd* into the directory *org.octris.core*. This directory is called the
**working directory** of a project. For the next installation step we need to
have *GNU make* installed. Each application developed using the OCTRiS framework 
and the framework itself has at least one Makefile in it's root directory. Just 
execute:

    make

You will get an usage information in form of a list of the possible targets. To
complete the installation of the framework you have to enter:

    make install
    
This command will create several additional directories and symlinks required
for the installation. When the task finished successfully you will have 
a directory structure similar to the following:

![core tree](../figures/tree_core.png)
    
Symbolic links are visualized in the example as *->* and directories have the
suffix */*. Note that the `make` target created most of the directories and all 
of the symbolic links in the example above. Note also, that some created 
directories are empty like for example *data* and *host*. These will get 
filled later when installing the first application.

### Summary -- important directories

This is just an overview of the most important directories we created in this
chapter:

+-----------------------+-------------------------------------------+
| **base**              | /home/harald/www/                         |
+-----------------------+-------------------------------------------+
| **workspace**         | /home/harald/www/work/                    |
+-----------------------+-------------------------------------------+
| **working directory** | /home/harald/www/work/org.octris.core/    |
+-----------------------+-------------------------------------------+

Keep in mind that

*   the *base* directory needs to be configured in the *OCTRIS_BASE* environment
    variable _and_ as environment variable of the webservers (virtual) host 
    configuration of an application. The latter is a new information for you,
    it will be explained later in this chapter.

*   every application has it's own *working directory* inside the *workspace*

PHP configuration
-----------------

You need a PHP installation according to chapter 1.2. requirements with all 
required extensions enabled.

The next step is to configure PHP for the OCTRiS framework. PHP needs to find 
the PHP libraries to be used in the framework and it's applications, therefore 
we have to adjust the *include_path* setting in the php.ini:

    include_path="/home/harald/www/libs/;..."

The path to *.../www/libs/* is the only additional path we need in the 
*include_path* setting, because during *make install* all PHP library pathes
will be made available in this directory through a symbolic link.

Installing an application
-------------------------

All applications based on the OCTRiS framework need to have a working directory
below */home/harald/www/work/*. All applications have the same installation
procedure by changing into the working directory and typing:

    make install
    
You can download the example project from github -- org.octris.example -- as 
first sample application. After following the steps described above, you 
should have a directory structure similar to the following:

![example tree](../figures/tree_example.png)

Note that the directories, that where empty just after installing the core
framework, now have content in it in form of subdirectories and symbolic
links to the example application. You may now have an idea about the strict 
directory layout used by the framework and it's applications.

The PHP *include_path* does not need to be changed, because all libraries of
the new installed application are now sym-linked to the globale libs-directory
configured in the php.ini and therefore is automatically available for all other
installed applications and the framework.

Web Server configuration
------------------------

Next step is the configuration of the webserver for the host of the example
application. I am not going into detail here, because setting up a webserver
can get rather complex. The OCTRiS framework is already tested and deployed
with LightTPD and nginx using PHP over FastCGI, but it should not be a problem 
to configure an Apache WebServer for it either using mod\_php or PHP over
FastCGI.

The following are the important things that need to be configured through the
webserver:

*   Create a (virtual) host for the application

*   The *document root* of the host must point to:

        /home/harald/www/host/org.octris.example/
        
*   The host must of course "know" how to handle PHP files. The default page
    for the host should be `index.php`

*   There needs to be a way to configure environment variables for a webserver
    host visible to the PHP application. You need to configure the following
    environment variables:

    +---------------+-----------------------+
    | Variable      | Value                 |
    +===============+=======================+
    | OCTRIS_BASE   | /home/harald/www/     |
    +---------------+-----------------------+
    | OCTRIS_APP    | org.octris.example    |
    +---------------+-----------------------+
    | OCTRIS_DEBUG  | 1                     |
    +---------------+-----------------------+
    
    **Note** 
    
    The environment variable *OCTRIS_DEBUG* should only be set in development
    and testing environments, because it might enable debugging output and 
    disable caching etc. On a production server you can either leave this 
    variable out or set it to "0".

When your webserver is properly configured, you should be able to visit the host
by entering the URL of the host in a browser.

And that's it!
