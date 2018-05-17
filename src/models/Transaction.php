<?php

namespace Ewallet\models;


use Engine\tools\BaseModel;

class Transaction extends BaseModel
{
    public $table = 'transaction';
    public $pk = 'hash';

    public function log(array $data)
    {
        $ts = microtime(true);
        $hash = md5(json_encode($data)) . substr(md5("{$ts}"), 5);
        $data['hash'] = $hash;
        return $this->create($data);
    }

}