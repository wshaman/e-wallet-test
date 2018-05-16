<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 16.05.17
 * Time: 23:38
 */

namespace Engine\engine;


use Engine\backend\models\ErrorLog;
use Engine\tools\traits\ConfigTrait;

class App
{
    use ConfigTrait;
    /** @var  DB */
    public $db;
    /** @var  Responder */
    public $respoder;

    private $_headers = [];

    private $_my_identity='APP';

    private $errors = [];

    private $lastLogId = null;

    public function getLastLogId()
    {
        return $this->lastLogId;
    }
    /**
     * @param mixed $my_identity
     *
     * @return App
     */
    public function setMyIdentity($my_identity, $quiet=false)
    {
        $this->_my_identity = $my_identity;
        if(!$quiet && $this->is_cli()){
            $this->log("Hello from ". $this->_my_identity);
        }
        return $this;
    }

    public function setError($err)
    {
        $this->errors[] = $err;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function clearErrors()
    {
        $this->errors = [];
    }



    public function is_cli()
    {
        if ( defined('STDIN') ) { return true; }

        if ( php_sapi_name() === 'cli' ) { return true; }

        if ( array_key_exists('SHELL', $_ENV) ) { return true; }

        if ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0)
        {
            return true;
        }

        if ( !array_key_exists('REQUEST_METHOD', $_SERVER) )
        {
            return true;
        }

        return false;
    }



    private function setHeaders()
    {
        if (!$this->is_cli()){
            header('Access-Control-Allow-Origin: *');
            header('Content-type: application/json');
            header("Access-Control-Allow-Methods: GET,POST,PUT,OPTIONS");
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        }
    }

    /**
     * @deprecated
     * @param string $msg
     */
    public function logEnd($msg='OK')
    {
        echo "...\033[01;31m[{$msg}]\033[0m";
    }

    private function _writeErrorLog($msg, $data=null)
    {
        $this->lastLogId = (new ErrorLog())->create([
            'message' => $msg,
            'data' => $data && is_array($data) ? json_encode($data) : null
        ]);
    }

    public function log($msg, $type='LOG', $log_data=null)
    {
        $is_error = (in_array($type, ['ERR', 'ERROR']));
        $dt = date('c');
        switch ($type){
            case 'ERR':
            case 'ERROR' : $clr_start = "\033[01;31m"; break;
            case 'WARN' : $clr_start = "\033[01;33m"; break;
            case 'OK' : $clr_start = "\033[01;32m"; break;
            default : $clr_start = "\033[01;37m";
        }

        switch ($type){
            case 'ERR':
            case 'ERROR' : $type = 'ERR '; break;
            case 'WARN' : $type = 'WARN'; break;
            case 'OK' : $type = 'OK  '; break;
            default : $type = 'LOG ';
        }

        if($this->is_cli()) {
            $clr_end = "\033[0m";
            echo "[{$dt}][\033[01;36m{$this->_my_identity}\033[0m][{$clr_start}{$type}{$clr_end}] : $msg " . (empty($log_data) ? '' : json_encode($log_data)) . PHP_EOL;
            if ($is_error) {
                debug_print_backtrace();
            }
        }
        if($log_data){
            $this->_writeErrorLog($msg, $log_data);
        }
    }

    public function __construct()
    {

        $this->setHeaders();
        $db_config = $this->readConfig('db');
        $this->db = new DB($db_config['username'], $db_config['password'],
            $db_config['dbname'], $db_config['type'], $db_config['host']);
        $this->respoder = new Responder();
        $this->respoder->echoExit(true);
    }

    public function ping()
    {
        $f = fopen($this->_get_ping_file_name(), 'w');
        fprintf($f, time());
        fclose($f);
    }

    public function check_ping()
    {
        return file_get_contents($this->_get_ping_file_name());
    }

    private function _get_ping_file_name()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'app_ping';
        @mkdir($dir, 0777, true);
        $fname = $dir . DIRECTORY_SEPARATOR . $this->_my_identity;
        return $fname;
    }
}
