<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 5/20/18
 * Time: 12:30 AM
 */

namespace Ewallet\admin;


use Engine\tools\BaseApi;
use Ewallet\models\Client;

class ClientApi extends BaseApi
{
    public function ListMethodAny()
    {
        $query = "SELECT a.id, CONCAT(a.fullname, ' from ', b.country,', ', b.city, ' | ', c2.code) as name
FROM client AS a
  LEFT JOIN coin c2 on a.coin_id = c2.id
  LEFT JOIN location b ON a.location_id=b.id;";
        $rows = (new Client())->query($query);
        return $rows;
    }
}