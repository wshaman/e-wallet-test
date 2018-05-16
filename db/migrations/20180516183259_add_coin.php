<?php

use Phinx\Migration\AbstractMigration;

class AddCoin extends AbstractMigration
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
        $this->table("coin")
            ->addColumn('title', 'string', ['length'=>50, 'null'=>false])
            ->addColumn("code", "string", ['length'=>5])
            ->addColumn('precision', 'integer', ['default'=>100, 'null'=>false])
            ->addColumn('exchange_fee', 'integer', ['default'=>0, 'null'=>false])
            ->addIndex('code', ['unique'=>True])
        ->create();

        $this->table("coin_rate", ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'biginteger', ['identity'=>true])
            ->addColumn('coin_id', 'integer')
            ->addColumn('usd_rate', 'integer')
            ->addColumn('coin_rate', 'integer')
            ->addColumn('valid_from', 'date')
            ->addForeignKey('coin_id', 'coin', 'id')
            ->addIndex('coin_id', ['unique'=>false])
            ->addIndex('valid_from', ['unique'=>false])
        ->create();
    }
}
