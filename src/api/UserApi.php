<?php


namespace Ewallet\api;


use Engine\tools\BaseApi;
use Engine\tools\C;
use Ewallet\api\traits\DbEasify;
use Ewallet\exceptions\WrongRequest;
use Ewallet\models\Client;
use Ewallet\models\Location;

class UserApi extends BaseApi
{
    use DbEasify;
    protected $return_type = C::RETURN_JSON;
    public $defaultMethod = 'auth';

    public function RegisterMethodPost()
    {
        $name = $this->fromAnyRequired('name');
        $code = $this->fromAnyRequired('wallet');
        $city = $this->fromAnyRequired('city');
        $country = $this->fromAnyRequired('country');
        $coin = $this->getCoinByCode($code);
        $loc_id = (new Location())->resolveId($city, $country);
        $data = [
            'fullname' => $name,
            'location_id' => $loc_id,
            'coin_id' => $coin['id'],
        ];
        $exists = (new Client())->findOne($data);
        if ($exists) {
            throw new WrongRequest("User already exists.");
        }
        $data['amount'] = 0;
        return (new Client())->create($data);
    }

    public function AuthMethodPost()
    {
        //Dummy! No auth, return #id as token.
        $user = $this->fromAnyRequired('user');
        $r = $this->getClient($user);
        return $r['id'];
    }
}