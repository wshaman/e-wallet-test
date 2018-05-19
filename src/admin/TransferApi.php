<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 5/19/18
 * Time: 10:16 PM
 */

namespace Ewallet\admin;


use Engine\tools\BaseApi;

class TransferApi extends BaseApi
{
    public $defaultMethod='log';

    public function LogMethodAny()
    {
        $uid = $this->fromAnyRequired('uid');
        $date_from = $this->fromAny('date_from');
        $date_to = $this->fromAny('date_to');
    }
}