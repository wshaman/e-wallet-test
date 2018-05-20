<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 5/19/18
 * Time: 10:16 PM
 */

namespace Ewallet\admin;


use Engine\tools\BaseApi;
use Ewallet\models\Client;
use Ewallet\models\Coin;
use Ewallet\models\Location;
use Ewallet\models\Transaction;

class TransferApi extends BaseApi
{
    public $defaultMethod = 'log';
    private $_clients = [];

    private function _getClientById($id)
    {
        if (!array_key_exists($id, $this->_clients)) {
            $cl = (new Client())->findOne(['id' => $id]);
            $this->_clients[$id] = $cl;
        }
        return $this->_clients[$id];
    }

    public function LogMethodAny()
    {
        $uid = $this->fromAnyRequired('uid');
        $date_from = $this->fromAny('date_from');
        $date_to = $this->fromAny('date_to');
        $page = $this->fromAny('page', 1);
        $page = intval($page) - 1;
        $response = ['total' => 0, 'value_usd' => 0, 'value_coin' => 0, 'rows' => []];
        $limit = 100;
        $client = (new Client())->findOne(['id' => $uid]);
        $coins = (new Coin())->findAll([], 'id');
        $locations = (new Location())->findAll([], 'id');
        $usd_coin = (new Coin())->findOne(['code' => 'USD']);
        //@todo: For partman check if limits are in 1 month and use specific table.
        //@todo: Validate all this stuff
        $wheres = " WHERE (sender_id = :uid OR receiver_id =:uid)";
        $paginate = '';
        $params = [':uid' => $uid];
        if ($date_from) {
            $wheres .= " AND created_at >= :date_from";
//            $params[':date_from'] = strtotime($date_from);
            $params[':date_from'] = $date_from;
        }
        if ($date_to) {
            $wheres .= " AND created_at < :date_to";
            $params[':date_to'] = $date_to;
        }
        $tr = new Transaction();
        $count = $tr->query("SELECT COUNT(*) as cnt FROM {$tr->table} {$wheres}", $params);
        $count = $count[0]['cnt'];
        $response['total'] = $count;
        if ($count > $limit) {
            $paginate = ' LIMIT :limit';
            $params[':limit'] = $limit;
            if ($page > 0) {
                $paginate .= ' OFFSET :offset';
                $params[':offset'] = $page * $limit;
            }
        }
        $sums = $tr->query("SELECT SUM(receive_amount * receive_rate_usd / receive_rate_coin) as sum_usd, 
        SUM(receive_amount) as sum_coin FROM {$tr->table} {$wheres}", $params);
        $response['value_usd'] = $sums[0]['sum_usd'] * $coins[$client['coin_id']]['precision'] / $usd_coin['precision'];
        $response['value_coin'] = $sums[0]['sum_coin'] / $coins[$client['coin_id']]['precision'];


        $items = $tr->query("SELECT a.* FROM {$tr->table} AS a {$wheres} {$paginate}", $params);
        foreach ($items as $k => $item) {
            $sender = 'system';
            $s_coin = null;
            $receiver = null;
            if ($item['sender_id']) {
                $s = $this->_getClientById($item['sender_id']);
                $loc = $locations[$s['location_id']];
                $s_coin = $coins[$s['coin_id']]['code'];
                $sender = "{$s['fullname']} from {$loc['city']}, {$loc['country']}";
            }
            $r = $this->_getClientById($item['receiver_id']);

            $loc = $locations[$r['location_id']];
            $r_coin = $coins[$r['coin_id']];
            $receiver = "{$r['fullname']} from {$loc['city']}, {$loc['country']}";

            $response['rows'][] = [
                'hash' => $item['hash'],
                'sender' => $sender,
                'sender_coin' => $s_coin,
                'receiver' => $receiver,
                'receiver_coin' => $r_coin['code'],
                'amount' => $item['receive_amount'] / $r_coin['precision']
            ];
        }
        return $response;
    }
}