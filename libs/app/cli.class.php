<?php

namespace org\octris\core\app {
    class cli extends \org\octris\core\app {
    }

    // enable validation for superglobals
    $_SERVER  = new \org\octris\core\validate\wrapper($_SERVER);
    $_ENV     = new \org\octris\core\validate\wrapper($_ENV);
    
    unset($_POST);
    unset($_REQUEST);
    unset($_COOKIE);
}

