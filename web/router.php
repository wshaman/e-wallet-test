<?php

// @nb: all errors should be displayed by shutdown() function, not put into output;
//ini_set('display_errors', 0);
if (file_exists(__DIR__ . $_SERVER['REQUEST_URI'])){
    return false;
}
define('BASE_PATH', dirname(__DIR__));
define('URI_KEY', 'req');
require BASE_PATH . "/vendor/autoload.php";
use Engine\engine\E;
E::init();
E::serve('Ewallet');
