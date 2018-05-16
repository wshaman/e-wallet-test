<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 5/15/18
 * Time: 6:04 PM
 */

namespace Ewallet\api;


use Engine\tools\BaseApi;
use Ewallet\exceptions\WrongRequest;
use Ewallet\models\Coin;
use Ewallet\models\CoinRate;

class CoinApi extends BaseApi
{

    public function UpdateRateMethodPost()
    {
        $code = $this->fromAnyRequired('code');
        $base = $this->fromAnyRequired('base');
        $quote = $this->fromAnyRequired('quote');
        $date = $this->fromAnyRequired('date');
        $c = (new Coin())->findOne(['code' => $code]);
        if (!$c) {
            throw new WrongRequest("No coin {$code} found");
        }
        if (!is_integer($base) or !is_integer($quote)) {
            throw new WrongRequest("Base and Quote must be integers");
        }
        if ($code === 'USD') {
            throw new WrongRequest("LOL");
        }
        $cr = new CoinRate();
        $exists = $cr->findOne([
            'coin_id' => $c['id'],
            'valid_from' => $date
        ]);
        $data['coin_id'] = $c['id'];
        $data['coin_rate'] = $quote;
        $data['usd_rate'] = $base;
        $data['valid_from'] = $date;
        if ($exists) {
            $res = $cr->update($data, ['id' => $exists['id']]);
        } else {
            $res = $cr->create($data);
        }
        return $res;
    }
}