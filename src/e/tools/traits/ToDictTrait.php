<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 18.05.17
 * Time: 13:34
 */

namespace Engine\tools\traits;

use Engine\engine\E;
use Engine\tools\F;

trait ToDictTrait
{
    private function _toDict(&$array, $key_field='id')
    {
        if(null == $key_field) $key_field = 'id';
        $keys = array_keys($array);
        $res = [];
        foreach ($keys as $key){
            $a = F::array_get($array, [$key, $key_field]);
            if(!$a) continue;
            $res[$array[$key][$key_field]] = $array[$key];
            unset($array[$key]);
        }
        $array = $res;
        unset($res);
    }

    protected function _getDictFomQuery(string $q, $key_field='id')
    {
        $wallets = E::$app->db->pdo()->query($q);
        $rows = $wallets->fetchAll();
        $this->_toDict($rows, $key_field);
        return $rows;
    }
}
