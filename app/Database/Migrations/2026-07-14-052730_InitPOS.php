<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitPOS extends Migration
{
    public function up()
    {
        // Products
        $this->forge->addField([
            'id'          => ['type' => 'SERIAL'],
            'sku'         => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'buy_price'   => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'sell_price'  => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'stock'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'  => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'  => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products');

        // Transactions
        $this->forge->addField([
            'id'             => ['type' => 'SERIAL'],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'total_amount'   => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'payment_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'change_amount'  => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'created_at'     => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('transactions');

        // Transaction Details
        $this->forge->addField([
            'id'             => ['type' => 'SERIAL'],
            'transaction_id' => ['type' => 'INT'],
            'product_id'     => ['type' => 'INT'],
            'quantity'       => ['type' => 'INT'],
            'unit_price'     => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'subtotal'       => ['type' => 'DECIMAL', 'constraint' => '12,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('transaction_details');
    }

    public function down()
    {
        $this->forge->dropTable('transaction_details');
        $this->forge->dropTable('transactions');
        $this->forge->dropTable('products');
    }
}
