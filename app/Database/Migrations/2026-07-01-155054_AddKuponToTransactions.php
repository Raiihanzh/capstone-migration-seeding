<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKuponToTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [

            'kode_kupon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'biaya_admin'
            ],

            'diskon_kupon' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'default'    => 0,
                'after'      => 'kode_kupon'
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', [
            'kode_kupon',
            'diskon_kupon'
        ]);
    }
}