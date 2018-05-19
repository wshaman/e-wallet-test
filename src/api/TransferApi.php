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
        $send_by_recevier_wallet = $this->fromAny('send_by_recevier_wallet', 0);
        if (!is_integer($amount)) {
            throw new WrongRequest("Amount must be integer");
        }

        $c->transactionBegin();
        $sender = $this->getClientByToken(strval($token));
        $receiver = $this->getClient(intval($receiver_id));
        $coin_sender = $this->getCoinById($sender['coin_id']);
        $coin_receiver = $this->getCoinById($receiver['coin_id']);
        if ($coin_sender['id'] == $coin_receiver['id']) {
            $amount_out = $amount;
            $rate_receiver = $rate_sender = ['coin_rate' => 1, 'usd_rate' => 1];
        } else {
            $rate_sender = (new CoinRate())->selectRateForToday(intval($sender['coin_id']));
            $rate_receiver = (new CoinRate())->selectRateForToday(intval($receiver['coin_id']));
            if (!$rate_sender || !$rate_receiver) {
                $c->transactionRollback();
                throw new WrongRequest("No exchange rate for this today");
            }
            $exchange_coef = (
                ($coin_receiver['precision'] * $rate_sender['coin_rate'] * $rate_receiver['usd_rate']) /
                ($coin_sender['precision'] * $rate_receiver['coin_rate'] * $rate_sender['usd_rate'])
            );
            if ($send_by_recevier_wallet > 0) {
                //@nb: assume $amount in this case is $amount_out, but not $amount
                $amount_out = $amount;
                $amount = $amount_out / $exchange_coef;
                $amount = intval(round($amount));
            } else {
                $amount_out = $amount * $exchange_coef;
                $amount_out = intval(round($amount_out));
            }
        }
        $c->update(["\"amount\" =\"amount\" - {$amount}" => Client::WHERE_PLAIN_TYPE], ['id' => $sender['id']]);
        if ($sender['amount'] < $amount) {
            $c->transactionRollback();
            throw new WrongRequest("Not enough");
        }
        //@nb: to be sure
        $sender = $this->getClientByToken(strval($token));
        if ($sender['amount'] < 0) {
            $c->transactionRollback();
            throw new WrongRequest("Not enough");
        }
        $c->update(["\"amount\" =\"amount\" + {$amount_out}" => Client::WHERE_PLAIN_TYPE], ['id' => $receiver_id]);
        $r = (new Transaction())->log([
            'sender_id' => $sender['id'],
            'receiver_id' => $receiver_id,
            'send_amount' => $amount,
            'receive_amount' => $amount_out,
            'send_rate_coin' => $rate_sender['coin_rate'],
            'send_rate_usd' => $rate_sender['usd_rate'],
            'receive_rate_coin' => $rate_receiver['coin_rate'],
            'receive_rate_usd' => $rate_receiver['usd_rate'],
        ]);
        if (!$r) {
            $c->transactionRollback();
            throw new InternalError("Can't save transaction!");
        }
        $c->transactionCommit();
        return $r;
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

        (new Transaction())->log([
            'sender_id' => null,
            'receiver_id' => $receiver,
            'send_amount' => $amount,
            'receive_amount' => $amount,
            'send_rate_coin' => 1,
            'send_rate_usd' => 1,
            'receive_rate_coin' => 1,
            'receive_rate_usd' => 1,
        ]);
        $client = $this->getClient(intval($receiver));
        $c->transactionCommit();
        return $client['amount'];
    }
}