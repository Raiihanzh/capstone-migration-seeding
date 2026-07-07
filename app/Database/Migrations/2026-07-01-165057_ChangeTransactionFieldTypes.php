<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeTransactionFieldTypes extends Migration
{
    public function up()
    {
        $fields = [

            'ppn' => [
                'name' => 'ppn',
                'type' => 'DOUBLE',
                'null' => true,
            ],

            'biaya_admin' => [
                'name' => 'biaya_admin',
                'type' => 'DOUBLE',
                'null' => true,
            ],

            'diskon_kupon' => [
                'name' => 'diskon_kupon',
                'type' => 'DOUBLE',
                'null' => true,
            ]

        ];

        $this->forge->modifyColumn('transaction', $fields);
    }

    public function down()
    {
        $fields = [

            'ppn' => [
                'name' => 'ppn',
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0,
            ],

            'biaya_admin' => [
                'name' => 'biaya_admin',
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0,
            ],

            'diskon_kupon' => [
                'name' => 'diskon_kupon',
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0,
            ]

        ];

        $this->forge->modifyColumn('transaction', $fields);
    }
}