<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    // Fungsi ini dijalankan saat migrasi 'naik' atau dijalankan
    public function up()
    {
        // Mendefinisikan kolom-kolom untuk tabel users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true, // Pastikan username unik
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        // Menentukan primary key
        $this->forge->addKey('id', true);

        // Membuat tabel users
        $this->forge->createTable('users');
    }

    // Fungsi ini dijalankan saat migrasi 'turun' atau dibatalkan (rollback)
    public function down()
    {
        // Menghapus tabel users
        $this->forge->dropTable('users');
    }
}
