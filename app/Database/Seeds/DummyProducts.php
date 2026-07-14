<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DummyProducts extends Seeder
{
    public function run()
    {
        $data = [
            [
                'sku' => 'ITM-001',
                'name' => 'Kopi Kapal Api',
                'buy_price' => 1000,
                'sell_price' => 1500,
                'stock' => 100
            ],
            [
                'sku' => 'ITM-002',
                'name' => 'Indomie Goreng Spesial',
                'buy_price' => 2500,
                'sell_price' => 3000,
                'stock' => 50
            ],
            [
                'sku' => 'ITM-003',
                'name' => 'Beras Pandan Wangi 5kg',
                'buy_price' => 60000,
                'sell_price' => 65000,
                'stock' => 20
            ],
            [
                'sku' => 'ITM-004',
                'name' => 'Gula Pasir 1kg',
                'buy_price' => 12000,
                'sell_price' => 14000,
                'stock' => 30
            ],
            [
                'sku' => 'ITM-005',
                'name' => 'Minyak Goreng 2L',
                'buy_price' => 30000,
                'sell_price' => 35000,
                'stock' => 15
            ],
        ];

        $this->db->table('products')->insertBatch($data);
    }
}
