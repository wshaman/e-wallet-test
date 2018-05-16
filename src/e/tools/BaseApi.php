<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 14.11.16
 * Time: 16:23
 */

namespace Engine\tools;


use Engine\exceptions\WrongParamException;
use Engine\tools\traits\ConfigTrait;

class BaseApi
{

    /** @var  string */
    public $msg_result;

    /** @var string Use if no method given */
    public $defaultMethod = 'index';

    /** @var array An array, containing $_REQUEST */
    protected $_data = [];
    protected $_get = [];
    protected $_post = [];
    /**  @var array uri after resolving api/method */
    protected $_request;

    protected $_method;

    protected $return_type = C::RETURN_JSON;

    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_CREATED = 201;


    /**
     * @var string|array List methods, which requires auth check
     * '*' marks ANY call
     * null means NONE
     * [] - list all methods, which requires auth check
     */
    protected $checkAuth = '*';

    public function index()
    {
        return 'No default action is set for ' . get_called_class();
    }

    protected function fromGet($key, $def = null)
    {
        return F::array_get($this->_get, $key, $def);
    }

    protected function fromPost($key, $def = null)
    {
        return F::array_get($this->_post, $key, $def);
    }

    protected function fromAny($key, $def = null)
    {
        $g = $this->fromGet($key);
        return $g ? $g : $this->fromPost($key, $def);
    }

    protected function isPost()
    {
        return strtolower($this->_method) == 'post';
    }

    protected function isGet()
    {
        return strtolower($this->_method) == 'get';
    }

    protected function fromAnyRequired($key)
    {
        $g = $this->fromAny($key);
        if (!$g){
            throw new WrongParamException("Required param `{$key}` is missing");
        }
        return $g;
    }

    public function __construct($data, $request)
    {
        if ($this->return_type === null) $this->return_type = C::RETURN_XML;
        $this->_method = $_SERVER['REQUEST_METHOD'];
        $this->_data = $data;
        $this->_get = $_GET;
        $this->_post = $_POST;
        $b = json_decode(file_get_contents("php://input"), true) ?: [];
        if ($b) {
            $this->_post = array_merge($this->_post, $b);
        }
        unset($this->_get[URI_KEY]);
        $this->_request = $request;
    }

    public function getReturnType()
    {
        return $this->return_type;
    }

    protected function getUserClientFromReq()
    {
        $ext_uid = F::escape_str($this->getClientParam('uid'));
        $client_id = (new Client())->getId();
        return [$ext_uid, $client_id];
    }

    private function _checkAuth($method)
    {
//        switch ($this->checkAuth){
//            case '*' : $check = true; break;
//            case null: $check = false; break;m
//            default: $check = in_array($method, $this->checkAuth);
//        }
//        if($check){
//            $o = new OAuth2();
//            if($o->auth()){
//                return true;
//            } else {
//                return false;
//            }
//        }
        return true;
    }

    public function before($method)
    {
        if (!$this->_checkAuth($method)) {
            $this->msg_result = 'Auth required';
            return false;
        }
        return true;
    }

    /**
     * Return param fro requiested uri AFTER class/method
     * @param int $id
     * @return mixed
     * @throws WrongParamException
     */
    protected function getURIParam($id = 0)
    {
        if (!isset($this->_request[$id])) {
            throw new WrongParamException('Not enough parameters given');
        }
        return $this->_request[$id];
    }

    /**
     * Returns param from POST/GET array request
     * @param $key
     * @param bool $allow_empty
     * @return mixed
     * @throws WrongParamException
     */
    protected function getClientParam($key, $allow_empty = false)
    {
        if (!isset($this->_data[$key])) {
            if ($allow_empty) return null;
            throw new WrongParamException('Not found required param: ' . $key);
        }
        return $this->_data[$key];
    }


    protected function _setHeaderCode($code)
    {
        http_response_code($code);
    }

    protected function _ol_error($message, $code = 400)
    {
        return [
            'error' => ['code' => $code, 'message' => $message]
        ];
    }

}
