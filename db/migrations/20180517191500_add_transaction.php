<?php

use Phinx\Migration\AbstractMigration;

class AddTransaction extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('transaction', ['id'=>false, 'primary_key'=>'hash'])
            ->addColumn('hash', 'string', ['length'=>60])
            ->addColumn('sender_id', 'biginteger', ['null'=>true])
            ->addColumn('receiver_id', 'biginteger', ['null'=>false])
            ->addColumn('send_amount', 'decimal', ['precision'=>40, 'scale'=>0])
            ->addColumn('receive_amount', 'decimal', ['precision'=>40, 'scale'=>0])
            ->addColumn('send_rate', 'integer')
            ->addColumn('receive_rate', 'integer')
            ->addColumn('send_precision', 'integer')
            ->addColumn('receive_precision', 'integer')
            ->addTimestamps()
            ->addForeignKey('sender_id', 'client', 'id')
            ->addForeignKey('receiver_id', 'client', 'id')
            ->addIndex('sender_id', ['unique'=>false])
            ->addIndex('receiver_id', ['unique'=>false])
            ->create();
    }
}
