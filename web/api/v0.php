<?php

// @nb: all errors should be displayed by shutdown() function, not put into output;
//ini_set('display_errors', 0);

define('BASE_PATH', dirname(dirname(__DIR__)));
define('URI_KEY', 'req');

require BASE_PATH."/vendor/autoload.php";
use Engine\engine\E;
E::init();
E::serve('Ewallet\\api');
