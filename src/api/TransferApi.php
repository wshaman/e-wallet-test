<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 5/17/18
 * Time: 4:03 PM
 */

namespace Ewallet\api;


use Engine\tools\BaseApi;
use Ewallet\api\traits\DbEasify;
use Ewallet\exceptions\InternalError;
use Ewallet\exceptions\WrongRequest;
use Ewallet\models\Client;
use Ewallet\models\CoinRate;
use Ewallet\models\Transaction;

class TransferApi extends BaseApi
{
    use DbEasify;

    public function SendMethodPost()
    {
        $c = new Client();
        $token = $this->fromAnyRequired('token');
        $amount = $this->fromAnyRequired('amount');
        $receiver_id = $this->fromAnyRequired('receiver');
        if (!is_integer($amount)) {
            throw new WrongRequest("Amount must be integer");
        }

        $c->transactionBegin();
        $sender = $this->getClientByToken(strval($token));
        $receiver = $this->getClient(intval($receiver_id));
        $coin_in = $this->getCoinById($sender['coin_id']);
        $coin_out = $this->getCoinById($receiver['coin_id']);
        $c->update(["\"amount\" =\"amount\" - {$amount}" => Client::WHERE_PLAIN_TYPE], ['id' => $sender['id']]);
        if ($sender['amount'] < $amount) {
            $c->transactionRollback();
            throw new WrongRequest("Not enough");
        }

        $sender = $this->getClientByToken(strval($token));
        if ($sender['amount'] < 0) {
            $c->transactionRollback();
            throw new WrongRequest("Not enough");
        }
        if ($coin_in['id'] == $coin_out['id']) {
            $amount_out = $amount;
        } else {
            $rate_in = (new CoinRate())->selectRateForToday(intval($sender['coin_id']));
            $rate_out = (new CoinRate())->selectRateForToday(intval($receiver['coin_id']));
            if (!$rate_in || !$rate_out) {
                $c->transactionRollback();
                throw new WrongRequest("No exchange rate for this today");
            }
            $amount_out = $amount * 1;
        }
        $r = (new Transaction())->log([
            'sender_id' => null,
            'receiver_id' => $receiver,
            'send_amount' => $amount,
            'receive_amount' => $amount_out,
            'send_rate' => 1,
            'receive_rate' => 1,
            'send_precision'=> $coin_in['precision'],
            'receive_precision'=> $coin_out['precision'],
        ]);
        if (!$r){
            $c->transactionRollback();
            throw new InternalError("Can't save transaction!");
        }
        $c->transactionCommit();
    }

    public function FillMethodPost()
    {
        $amount = $this->fromAnyRequired('amount');
        $receiver = $this->fromAnyRequired('receiver');
        $client = $this->getClient(intval($receiver));
        if (!is_integer($amount)) {
            throw new WrongRequest("Amount must be integer");
        }
        $c = new Client();
        $c->transactionBegin();
        $c->update(["\"amount\" =\"amount\" + {$amount}" => Client::WHERE_PLAIN_TYPE], ['id' => $client['id']]);

        $coin = $this->getCoinById($client['coin_id']);
        (new Transaction())->log([
            'sender_id' => null,
            'receiver_id' => $receiver,
            'send_amount' => $amount,
            'receive_amount' => $amount,
            'send_rate' => 1,
            'receive_rate' => 1,
            'send_precision'=> $coin['precision'],
            'receive_precision'=> $coin['precision'],
        ]);
        $client = $this->getClient(intval($receiver));
        $c->transactionCommit();
        return $client['amount'];
    }
}