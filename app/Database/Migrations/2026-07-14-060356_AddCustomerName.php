<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerName extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transactions', [
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'invoice_number'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'customer_name');
    }
}
