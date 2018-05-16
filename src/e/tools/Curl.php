<?php

/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 25.11.16
 * Time: 18:10
 */

namespace Engine\tools;

class Curl
{
    private $ch;

    private $ignore_cert = false;
    private $_ua = 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0';
    private $_options = [];
    private $_skip_default = false;
    private $url;
    private $get_params = [];

    /**
     * @param mixed $url
     * @return Curl
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Sets curlopt
     * @param array $options
     * @return Curl
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * @param bool $skip_default
     * @return Curl
     */
    public function setSkipDefault($skip_default)
    {
        $this->_skip_default = $skip_default;
        return $this;
    }

    public function setGetParams($params)
    {
        $this->get_params = array_merge($this->get_params, $params);
    }

    /**
     * @param boolean $ignore_cert
     * @return Curl
     */
    public function setIgnoreCert($ignore_cert)
    {
        $this->ignore_cert = $ignore_cert;
        return $this;
    }

    private function _url()
    {
        if($this->get_params){
            $params = http_build_query($this->get_params);
            return $this->url . '?' . $params;
        }
        return $this->url;
    }

    private function _curl($options)
    {
        if(!$this->url){
            throw new \HttpException(400, 'No url given for curl query');
        }

        $this->ch = curl_init($this->_url());
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->_ua);
        if($this->ignore_cert){
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt_array($this->ch, $options);
        $resp = curl_exec($this->ch);
        return $resp;
    }

    public function getCurlVar()
    {
        return $this->ch;
    }

    public function get($params=[])
    {
        if($params){
            $this->setGetParams($params);
        }

        $set_options = [];
        if(!$this->_skip_default) {
            $set_options[CURLOPT_RETURNTRANSFER] = true;
        }
        foreach ($this->_options as $k => $option) {
            $set_options[$k] = $option;
        }
        return $this->_curl($set_options);
    }

    /**
     * @param array $params POST fields params
     * @param bool $as_array Use json_encode on data instead of http_build_query
     * @return mixed
     */
    public  function post($params=[], $as_array=true)
    {
        $set_options = [];
        if(!$this->_skip_default){
            $set_options[CURLOPT_RETURNTRANSFER] = true;
            $set_options[CURLOPT_POST] = true;
        }
        if(is_array($params)){
            $params = ($as_array) ? json_encode($params) : http_build_query($params) ;
        }
        $options = $this->_options;
        $options[CURLOPT_POSTFIELDS] = $params;
        foreach ($options as $k => $option) {
            $set_options[$k] = $option;
        }
        return $this->_curl($set_options);
    }
}
