<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 23.05.17
 * Time: 16:55
 */

namespace Engine\tools\traits;


use Engine\engine\E;
use Engine\tools\Status;

trait ModelTransactionTrait
{
    public function findNew()
    {
        $q = E::$app->db->pdo()->query("SELECT * FROM {$this->table} WHERE txid_out IS NULL LIMIT 1");
        return $q->fetch();
    }

    public function updateStatus($id, $status)
    {
        return $this->update(['status'=>$status], ['id'=>$id]);
    }

    public function getNextUnfinished()
    {
        $q = E::$app->db->pdo()
            ->prepare("SELECT * FROM {$this->table} WHERE status IN (:s1, :s2, :s3) LIMIT 1;");
        $q->execute([
            's1'=> Status::TRANSACTION_CONFIRMED,
            's2' => Status::TRANSACTION_PRESCAN,
            's3' => Status::TRANSACTION_REQUIRES_ACCUM //Status::TRANSACTION_SUSPENDED_SCAN_F
        ]);
        return $q->fetch();
    }
}
