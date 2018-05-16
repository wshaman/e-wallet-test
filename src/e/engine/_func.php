<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 16.05.17
 * Time: 23:42
 */
function shutdown() {
    $error = error_get_last();
    if ($error['type'] === E_ERROR) {
        \Engine\engine\E::$app->respoder->error('Fatal Error: '.$error['message']);
    }
}
