<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 5/15/18
 * Time: 1:55 PM
 */

namespace Ewallet\api;


use Engine\tools\BaseApi;
use Engine\tools\C;

class RegisterApi extends BaseApi
{
    protected $return_type = C::RETURN_JSON;
    public $defaultMethod = 'index';

    public function IndexMethodPost()
    {
        return 'OK';
    }
}