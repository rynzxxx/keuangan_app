<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNeracaTables extends Migration
{
    public function up()
    {
        // Tabel untuk master komponen neraca
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_komponen' => ['type' => 'VARCHAR', 'constraint' => 255],
            'kategori' => [
                'type' => 'ENUM',
                'constraint' => ['aktiva_lancar', 'aktiva_tetap', 'hutang_lancar', 'hutang_jangka_panjang', 'modal'],
                'null' => false
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('master_neraca');

        // Tabel untuk menyimpan nilai/jumlah per komponen per tahun
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tahun' => ['type' => 'INT', 'constraint' => 4],
            'master_neraca_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jumlah' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('master_neraca_id', 'master_neraca', 'id', 'CASCADE', 'CASCADE');
        // Kunci unik untuk mencegah duplikasi komponen dalam satu tahun
        $this->forge->addUniqueKey(['tahun', 'master_neraca_id']);
        $this->forge->createTable('detail_neraca');
    }

    public function down()
    {
        $this->forge->dropTable('detail_neraca');
        $this->forge->dropTable('master_neraca');
    }
}
