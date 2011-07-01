Overview
========

The OCTRiS framework is an BSD licensed open source PHP framework. It's 
purpose is to provide an "lightweight" alternative to other PHP frameworks. 
Lightweight in the sense, that it does not provide classes and / or 
implementations for all possible use-cases. 

The purpose of this framework is not to provide a run-everywhere / build-
everything framework. In this manner it's in some cases very specialized 
and normally requires a custom built of PHP, because of several extensions
that have to be enabled.

The framework was written in the mind, that the applications built with it
are run on a dedicated server, where the developer of the application can
affect what's installed on the server, e.g.: PHP version, enabled extensions,
web-server etc. If you intended to use this framework to develop applications
that should run (almost) everywhere especially on shared hosts, read no 
further -- you probably will be out of luck with this framework.

Before you start developing applications, you should have a look at chapter
1.2, the requirements, to make sure you can fulfill all requirements 
regarding the server the application is intended to run on.

Questions & answers
-------------------

**Why yet another PHP framework?**

Because i wanted to have a framework available, that works exactly how
i would expect and like a framework to work.
    
