<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailAlokasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'bku_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'master_kategori_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'persentase_saat_itu' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'comment' => 'Persentase yang berlaku saat laporan dibuat'],
            'jumlah_alokasi' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'comment' => 'Hasil hitung: total pendapatan * persentase'],
            'jumlah_realisasi' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'comment' => 'Total pengeluaran riil untuk kategori ini'],
            'sisa_alokasi' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'comment' => 'Hasil hitung: alokasi - realisasi'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('bku_id', 'bku_bulanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('master_kategori_id', 'master_kategori_pengeluaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_alokasi');
    }

    public function down()
    {
        $this->forge->dropTable('detail_alokasi');
    }
}
