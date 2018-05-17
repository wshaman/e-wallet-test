<?php

namespace Ewallet\models;


use Engine\tools\BaseModel;

class CoinRate extends BaseModel
{
    public $table = 'coin_rate';

    public function selectRateForToday(int $coin_id)
    {
        $sql = 'SELECT * FROM coin_rate WHERE coin_id=:coin_id AND valid_from <= current_date ORDER BY valid_from DESC LIMIT 1;';
        $rows = $this->query($sql);
        if ($rows) return $rows[0];
        return null;
    }

}