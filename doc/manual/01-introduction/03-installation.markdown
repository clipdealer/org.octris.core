Installation
============

Preparations
------------

The OCTRiS framework and the applications developed using the framework have 
to follow a fixed directory layout. Normally one would install the framework
and the applications developed with it in a home directory of either the 
developer or -- on the production server -- in a special user the application
is running as.

In the following we use the user *harald* and install everything in the 
home-directory of this user:

    cd /home/harald
    
We need to create a directory in which we want to install the framework and
applications. Good names are for example *www* or *projects*:

    mkdir www
    
We now have to set an environment variable:

    export OCTRIS_BASE=/home/harald/www

You should write this into one of the configuration files that get loaded, when
you open a terminal or login into your shell for example the `.profile` or the
`.bashrc` of the user you are installing in. This environment variable is 
required for every console based tool of the framework.

Change the directory to `www`:

    cd www
    
After the `cd` we are now in the newly created *www* directory. Here we have
to create a directory, whichs name is `work`. Note, that the framework requires
this directory name for the stuff we are going to put in it. Other directory 
names are not allowed:

    mkdir work; cd work

This is our workspace. The framework source package needs to get here and all
application source packages will be created here. Download the latest release
of the OCTRiS framework into this directory and extract it:

    tar xfz octris...tgz

If you have done everything correctly you should now have a directory tree,
which looks like the following:

    /home/harald/www/work/org.octris.core
    
              
            
