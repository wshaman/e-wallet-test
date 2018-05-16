<?php

namespace Engine\tools\traits;

use Engine\tools\F;

/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 15.05.17
 * Time: 16:11
 */

trait ConfigTrait
{

    private static $_config;

    protected function readConfig($part=null, $term=null)
    {
        if(!self::$_config){
            self::$_config = require(BASE_PATH. '/config/config.php');
        }
        $resp = ($part) ? F::array_get(self::$_config, $part, []) : self::$_config;
        return ($term) ? F::array_get($resp, $term) : $resp;
    }
}
