<?php

namespace Ewallet\api;


use Engine\tools\BaseApi;
use Ewallet\api\traits\DbEasify;
use Ewallet\exceptions\WrongRequest;
use Ewallet\models\Coin;
use Ewallet\models\CoinRate;

class CoinApi extends BaseApi
{
    use DbEasify;

    public function UpdateRateMethodPost()
    {
        $code = strtoupper($this->fromAnyRequired('code'));
        $base = $this->fromAnyRequired('base');
        $quote = $this->fromAnyRequired('quote');
        $date = $this->fromAnyRequired('date');

        if (!is_integer($base) or !is_integer($quote)) {
            throw new WrongRequest("Base and Quote must be integers");
        }
        if ($code === 'USD') {
            throw new WrongRequest("LOL");
        }
        $coin = $this->getCoinByCode($code);
        $cr = new CoinRate();
        $exists = $cr->findOne([
            'coin_id' => $coin['id'],
            'valid_from' => $date
        ]);
        $data['coin_id'] = $coin['id'];
        $data['coin_rate'] = $quote;
        $data['usd_rate'] = $base;
        $data['valid_from'] = $date;
        if ($exists) {
            $res = $cr->update($data, ['id' => $exists['id']]);
        } else {
            $res = $cr->create($data);
        }
        return 'OK';
    }
}