<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBiayaAdminToTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [
            'biaya_admin' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'default'    => 0,
                'after'      => 'ppn'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'biaya_admin');
    }
}