<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogAktivitasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 100, 'comment' => 'User yang melakukan aksi'],
            'aktivitas' => ['type' => 'VARCHAR', 'constraint' => 50, 'comment' => 'Jenis aksi: MEMBUAT, MENGUPDATE, MENGHAPUS'],
            'deskripsi' => ['type' => 'TEXT', 'comment' => 'Deskripsi detail dari aktivitas'],
            'bku_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'comment' => 'ID BKU Bulanan terkait'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('log_aktivitas');
    }

    public function down()
    {
        $this->forge->dropTable('log_aktivitas');
    }
}
