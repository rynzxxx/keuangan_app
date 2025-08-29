<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPermanentIncomeFields extends Migration
{
    public function up()
    {
        $fields = [
            'saldo_bulan_lalu' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'default' => 0.00,
                'after' => 'tahun' // Posisi kolom setelah 'tahun'
            ],
            'penghasilan_bulan_ini' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'default' => 0.00,
                'after' => 'saldo_bulan_lalu'
            ]
        ];
        $this->forge->addColumn('bku_bulanan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('bku_bulanan', ['saldo_bulan_lalu', 'penghasilan_bulan_ini']);
    }
}
