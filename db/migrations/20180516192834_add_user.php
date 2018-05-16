<?php

use Phinx\Migration\AbstractMigration;

class AddUser extends AbstractMigration
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
        $this->table("location", ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'biginteger', ['identity'=>true])
            ->addColumn('country', 'string', ['length'=>40])
            ->addColumn('city', 'string', ['length'=>40])
            ->create();

        $this->table("client", ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'biginteger', ['identity'=>true])
            ->addColumn('fullname', 'string', ['length'=>250])
            ->addColumn('location_id', 'biginteger')
            ->addColumn('coin_id', 'integer')
            ->addColumn('amount', 'decimal', ['precision'=>30, 'scale'=>0])
            ->addColumn('amount_display', 'decimal', ['precision'=>34, 'scale'=>4])
            ->addForeignKey('coin_id', 'coin', 'id')
            ->addIndex(["fullname", "location_id", "coin_id"], ['unique'=>true])
            ->create();
    }
}
