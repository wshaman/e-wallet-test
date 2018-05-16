<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 16.05.17
 * Time: 23:23
 */

namespace Engine\engine;


use Engine\tools\F;
use \Error;
use \Exception;


class E
{
    /** @var  App */
    static $app;

    public static function init()
    {
        self::$app = new App();
        require __DIR__ . "/_func.php";
        register_shutdown_function('shutdown');
    }


    public static function serve($psrClassPrefix = "Engine\\api")
    {
        /** @nb: Get requested API method and params */
        $matches = [];
        if (preg_match("/^PHP\s+([^\s]+)\s+.*Server$/i",
            F::array_get($_SERVER, 'SERVER_SOFTWARE', ''), $matches)) {
            $_REQUEST[URI_KEY] = preg_replace("~^/~", '', $_SERVER['PHP_SELF']);
        }
        $req = explode('/', F::array_get($_REQUEST, URI_KEY));
        $req = array_map(function ($i) {
            return preg_replace('~[^-_\d\w\.]~', '', $i);
        }, $req);
        $r_class = array_shift($req);
        $r_method = array_shift($req);
        if ($req) $req = array_values($req);

        if (strpos($r_method, '-') !== false) {
            $p = explode('-', $r_method);
            $r_method = array_shift($p);
            while ($_ = array_shift($p)) $r_method .= ucfirst($_);
        }
        $className = "{$psrClassPrefix}\\" . ucfirst($r_class) . 'Api';
        $httpMethod = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
        $methodName = ($r_method) ? F::simplify_string($r_method) . 'Method' . $httpMethod : null;

        if (!isset($r_class)) {
            self::$app->respoder->wrongParamsError('No class was given');
        }

        if (class_exists($className)) {
            try {
                $data = $_REQUEST;
                unset($data['req']);
                /** @var \Engine\tools\BaseApi $classObj */
                $classObj = new $className($data, $req);
            } catch (Error $e) {
                self::$app->respoder->wrongParamsError('No such class found: ' . $r_class);
                die;
            } catch (Exception $e) {
                self::$app->respoder->error($e->getMessage(), $e->getCode());
                die;
            }
        } else {
            self::$app->respoder->wrongParamsError('No such class found: ' . $r_class);
            die;
        }
        if (!$methodName) $methodName = $classObj->defaultMethod;
        if (!method_exists($classObj, $methodName)) {
            $methodName = preg_replace("/{$httpMethod}$/", "Any", $methodName);
        }

        if (method_exists($classObj, $methodName)) {
            try {
                $pre = $classObj->before($methodName);
                if (!$pre) {
                    self::$app->respoder->error($classObj->msg_result);
                }
                $v = $classObj->$methodName();
                self::$app->respoder->ok($v, $classObj->getReturnType());
            } catch (\PDOException $e) {
                self::$app->respoder->error(explode("\n", $e->getMessage())[0], $e->getCode());
            } catch (\Throwable $e) {
                self::$app->respoder->error($e->getMessage(), $e->getCode());
            }
        } else {
            self::$app->respoder->wrongParamsError("No such method found: '{$r_class}/{$r_method}' for http: '{$httpMethod}' request");
        }
    }
}
