<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTabelLabaRugiTahun extends Migration
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
                'constraint' => '4',
            ],
            'total_pendapatan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'total_biaya' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'laba_rugi_bersih' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Menentukan Primary Key
        $this->forge->addKey('id', true);

        // Membuat tabel 'laba_rugi_tahun'
        $this->forge->createTable('laba_rugi_tahun');
    }

    public function down()
    {
        // Menghapus tabel 'laba_rugi_tahun' jika migration di-rollback
        $this->forge->dropTable('laba_rugi_tahun');
    }
}
