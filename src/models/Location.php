<?php

namespace Ewallet\models;


use Engine\tools\BaseModel;

class Location extends BaseModel
{
    public $table = 'location';

    public function resolveId($city, $country) : int
    {
        $data = ['city'=> $city, 'country'=>$country];
        $exists = $this->findOne($data);
        if ($exists) return $exists['id'];
        return $this->create($data);
    }
}