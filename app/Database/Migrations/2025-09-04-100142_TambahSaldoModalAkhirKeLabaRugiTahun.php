<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahSaldoModalAkhirKeLabaRugiTahun extends Migration
{
    public function up()
    {
        $fields = [
            'saldo_modal_akhir' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
                'after' => 'laba_rugi_bersih' // Posisi kolom
            ],
        ];
        $this->forge->addColumn('laba_rugi_tahun', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('laba_rugi_tahun', 'saldo_modal_akhir');
    }
}
