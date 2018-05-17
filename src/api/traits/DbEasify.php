<?php

namespace Ewallet\api\traits;


use Ewallet\exceptions\WrongRequest;
use Ewallet\models\Client;
use Ewallet\models\Coin;

trait DbEasify
{
    public function getCoinByCode(string $code)
    {
        $c = (new Coin())->findOne(['code' => $code]);
        if (!$c) {
            throw new WrongRequest("No coin {$code} found");
        }
        return $c;
    }

    public function getCoinById(string $id)
    {
        $c = (new Coin())->findOne(['id' => $id]);
        if (!$c) {
            throw new WrongRequest("No coin {$id} found");
        }
        return $c;
    }

    public function getClient(int $id)
    {
        $c = (new Client())->findOne(['id' => $id]);
        if (!$c) {
            throw new WrongRequest("No client {$id} found");
        }
        return $c;
    }


    public function getClientByToken(string $token)
    {
        $c = (new Client())->findOne(['id' => $token]);
        if (!$c) {
            throw new WrongRequest("No client #{$token} found");
        }
        return $c;
    }
}