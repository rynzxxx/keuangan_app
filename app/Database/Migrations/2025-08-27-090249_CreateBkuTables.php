<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBkuTables extends Migration
{
    public function up()
    {
        // Tabel Utama BKU Bulanan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'bulan' => ['type' => 'INT', 'constraint' => 2],
            'tahun' => ['type' => 'INT', 'constraint' => 4],
            'total_pendapatan' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'total_pengeluaran' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'saldo_akhir' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['bulan', 'tahun']);
        $this->forge->createTable('bku_bulanan');

        // Tabel Detail Pendapatan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'bku_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'master_pendapatan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jumlah' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('bku_id', 'bku_bulanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('master_pendapatan_id', 'master_pendapatan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_pendapatan');

        // Tabel Detail Pengeluaran
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'bku_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'master_kategori_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'deskripsi_pengeluaran' => ['type' => 'VARCHAR', 'constraint' => 255],
            'jumlah' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('bku_id', 'bku_bulanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('master_kategori_id', 'master_kategori_pengeluaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_pengeluaran');
    }

    public function down()
    {
        $this->forge->dropTable('detail_pendapatan');
        $this->forge->dropTable('detail_pengeluaran');
        $this->forge->dropTable('bku_bulanan');
    }
}
