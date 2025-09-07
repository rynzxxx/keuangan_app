<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRekapArusKasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun' => [
                'type'       => 'YEAR',
                'constraint' => 4,
                'unique'     => true, // Hanya ada satu rekap per tahun
            ],
            'total_kas_masuk' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'total_kas_keluar' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'saldo_akhir' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('rekap_arus_kas');
    }

    public function down()
    {
        $this->forge->dropTable('rekap_arus_kas');
    }
}
