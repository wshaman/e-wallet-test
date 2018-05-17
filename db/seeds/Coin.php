<?php

use Phinx\Seed\AbstractSeed;

class Coin extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $coins = [
            ['title' => 'US Dollar', 'code' => 'USD', 'precision' => 10000, 'exchange_fee' => 0],
            ['title' => 'RU Rouble', 'code' => 'RUB', 'precision' => 100, 'exchange_fee' => 2],
            ['title' => 'KZ Tenge', 'code' => 'KZT', 'precision' => 100, 'exchange_fee' => 4],
            ['title' => 'Bitcoin', 'code' => 'BTC', 'precision' => 100000000, 'exchange_fee' => 10],
            ['title' => 'Turkish Lira', 'code' => 'TRY', 'precision' => 100, 'exchange_fee' => 3],
            ['title' => 'Euro', 'code' => 'EUR', 'precision' => 10000, 'exchange_fee' => 1],
            ['title' => 'Singapore Dollar', 'code' => 'SGD', 'precision' => 100, 'exchange_fee' => 7],
        ];
        $this->table('coin')->insert($coins)->save();
        $c = $this->query('SELECT id FROM coin WHERE code=\'USD\' LIMIT 1;')->fetch();
        $this->table('coin_rate')->insert(['coin_id' => $c['id'], 'usd_rate' => 1,
            'coin_rate' => 1,
            'valid_from'=>'2010-01-01'])
            ->save();
    }

}
