<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 20.05.17
 * Time: 1:45
 */

namespace Engine\tools;


use Engine\engine\E;
use Engine\tools\F;
use Engine\tools\traits\ToDictTrait;

abstract class BaseModel
{
    use ToDictTrait;
    protected $table;
    protected $pk = 'id';
    const WHERE_PLAIN_TYPE = '__plaIn__';
    const ON_CONFLICT   = 'ON CONFLICT';

    const DO_NOTHING    = 'NOTHING';


    public function query(string $sql, $params=[], $keyfield=null)
    {
        $this->_checkAssigns($params);
        $q = E::$app->db->pdo()->prepare($sql);
        $q->execute($params);
        $data = $q->fetchAll();
        if($keyfield) $this->_toDict($data, $keyfield);
        return $data;
    }

    public function update($field, $where)
    {
        $fields = [];
        $wheres = [];
        $assigned= [];
        foreach($field as $key => $value){
            if ($value == self::WHERE_PLAIN_TYPE){
                continue;
            } else {
                $fields[] = $key . '=:' . $key;
                $assigned[':' . $key] = $value;
            }
        }
        if(is_array($where)){
            foreach($where as $key=>$value){
                if ($value == self::WHERE_PLAIN_TYPE){
                    $wheres[] = $key;
                } else {
                    $wheres[] = $key . '=:' . $key;
                    $assigned[':' . $key] = $value;
                }
            }
            $wheres = implode(', ', $wheres);
        } else $wheres = $where;

        $fields = implode(', ', $fields);
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$wheres};";
        $q = E::$app->db->pdo()->prepare($sql);
        $this->_checkAssigns($assigned);
        $q->execute($assigned);
        return $q->fetch();
    }

    public function create($data, $options=[])
    {
        $fields = [];
        $fields_enum = implode(', ', array_keys($data));
        $assigned = [];
        foreach($data as $key => $value){
            $fields[] = ':'.$key;
            $assigned[':'.$key] = $value;
        }
        $placeholders = implode(',', $fields);
        $opts = ' ';
        if($conf = F::array_get($options, self::ON_CONFLICT)){
            $opts .= ' '. self::ON_CONFLICT . ' DO ' . $conf . ' ';
        }
        $sql = "INSERT INTO {$this->table} ({$fields_enum}) VALUES ({$placeholders}) {$opts} RETURNING {$this->pk}";
        $q = E::$app->db->pdo()->prepare($sql);
        $this->_checkAssigns($assigned);
        $q->execute($assigned);
        $r = $q->fetch();
        return F::array_get($r, $this->pk);
    }

    public function findOne($where=[])
    {
        return $this->_findQ($where, 1)->fetch();
    }

    public function findAll($where=[], $keyfield=null)
    {
        $data = $this->_findQ($where)->fetchAll();
        if ($keyfield) $this->_toDict($data, $keyfield);
        return $data;
    }

    public function delete($where=[])
    {
        $wheres = [];
        $assigned = [];
        foreach($where as $key=>$value){
            if ($value == self::WHERE_PLAIN_TYPE){
                $wheres[] = $key;
            } else {
                $wheres[] = $key . '=:' . $key;
                $assigned[':' . $key] = $value;
            }
        }
        $wheres = implode(' AND ', $wheres);
        $sql = "DELETE FROM {$this->table}";
        if($where) $sql .=  " WHERE {$wheres} ";
        $q = E::$app->db->pdo()->prepare($sql);
        $this->_checkAssigns($assigned);
        $res = $q->execute($assigned);
        return $res;
    }

    /**
     * @param array $where
     * @param null  $limit
     *
     * @return \PDOStatement;
     */
    private function _findQ($where, $limit=null)
    {
        $wheres = [];
        $assigned = [];
        foreach($where as $key=>$value){
            if ($value == self::WHERE_PLAIN_TYPE){
                $wheres[] = $key;
            } else {
                $wheres[] = $key .'=:'.$key;
                $assigned[':'.$key] = $value;
            }
        }
        $wheres = implode(' AND ', $wheres);
        $sql = "SELECT * FROM {$this->table}";
        if($where) $sql .=  " WHERE {$wheres} ";
        $sql .= (($limit) ? ' LIMIT 1;' : ';');
        $q = E::$app->db->pdo()->prepare($sql);
        $this->_checkAssigns($assigned);
        $q->execute($assigned);
        return $q;
    }

    private function _checkAssigns(&$assigned)
    {
        foreach ($assigned as &$item) {
            if($item === true) $item = "true";
            if($item === false) $item = "false";
        }
    }
}
