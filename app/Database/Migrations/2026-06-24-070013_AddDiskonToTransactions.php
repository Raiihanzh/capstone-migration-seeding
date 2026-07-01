<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiskonToTransaction extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [
            'diskon' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0,
                'after' => 'total_harga'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'diskon');
    }
}