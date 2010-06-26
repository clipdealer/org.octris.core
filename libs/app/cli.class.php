<?php

namespace org\octris\core\app {
    class cli extends \org\octris\core\app {
    }

    // enable validation for superglobals
    $_SERVER  = new wrapper($_SERVER);
    $_ENV     = new wrapper($_ENV);
}

