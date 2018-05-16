<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 14.11.16
 * Time: 10:44
 */

namespace Engine\engine;
use Engine\exceptions\NoDBConnectedException;
use PDO;

final class DB
{
    /** @var null|PDO  */
    protected static $pdo = null;
    const DB_POSTGRES   = 'pgsql';
    const DB_MYSQL      = 'mysql';

    /**
     * DB constructor.
     * @param null $user
     * @param null $password
     * @param null $dbname
     * @param null $type
     * @param string $host
     */
    public function __construct($user=null, $password=null, $dbname=null, $type=null, $host='127.0.0.1') {
        if(self::$pdo){
            return true;
        }
        if(is_null($type)) $type = self::DB_POSTGRES;
        $dsn = "{$type}:dbname={$dbname};host=${host}";
        $opt  = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT            => 10,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => TRUE,
        );
        self::$pdo = new PDO($dsn, $user, $password, $opt);
    }

    /**
     * @return null|PDO
     * @throws NoDBConnectedException
     */
    public function pdo()
    {
        if(!self::$pdo){
            throw new NoDBConnectedException();
        }
        return self::$pdo;
    }

    protected function __clone() {}

}
