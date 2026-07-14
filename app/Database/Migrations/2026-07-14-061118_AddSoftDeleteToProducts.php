<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToProducts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'deleted_at');
    }
}
