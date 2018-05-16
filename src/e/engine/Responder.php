<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 14.11.16
 * Time: 15:56
 */

namespace Engine\engine;

use Engine\tools\C;

class Responder
{
    private $_final = false;

    /**
     * @param boolean $final
     * @return Responder
     */
    public function echoExit($final)
    {
        $this->_final = $final;
        return $this;
    }

    private function isBit(&$sum, &$check)
    {
        return ($sum & $check >0);
    }

    private function _pack($p, $options=null){
        if(!$options) $options = C::RETURN_JSON;
        $data = null;
        if(($options == C::RETURN_JSON)){
            $data = json_encode($p);
        }
        else if(($options == C::RETURN_XML)){
            $data = 'XML NOT IMPLEMENTED YET';
        }
        else if(($options == C::RETURN_PLAIN)){
            $data = $p['api_message'];
        }
        else if(($options == C::RETURN_AS_IS)){
            $data = $p;
        }
        if($data === null) {
            $data = 'Can not answer this request';
        }
        if($this->_final){
            echo $data;
            exit(0);
        }
        else
            return $data;
    }

    public function error($msg, $code=null, $options=null)
    {
        return $this->_pack([
            'error' => $msg,
            'status' => C::ERROR,
            'code' => $code
        ], $options);
    }

    public function ok($msg=[], $options=null)
    {
        return $this->_pack([
           'response' => $msg,
           'status' => C::OK
        ], $options);
    }

    public function wrongParamsError($msg=null)
    {
        return $this->error($msg, C::ERROR_CODE_WRONG_PARAMS);
    }
}
