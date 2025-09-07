<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterArusKasTable extends Migration
{
    public function up()
    {
        // Mendefinisikan struktur field untuk tabel master_arus_kas
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_komponen' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['masuk', 'keluar'],
                'null'       => false,
            ],
        ]);

        // Menjadikan 'id' sebagai primary key
        $this->forge->addKey('id', true);

        // Membuat tabel 'master_arus_kas'
        $this->forge->createTable('master_arus_kas');
    }

    public function down()
    {
        // Menghapus tabel 'master_arus_kas' jika migrasi di-rollback
        $this->forge->dropTable('master_arus_kas');
    }
}
