<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transactions', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'completed',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'status');
    }
}
