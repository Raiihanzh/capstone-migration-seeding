<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPpnToTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [
            'ppn' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'default'    => 0,
                'after'      => 'diskon',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'ppn');
    }
}