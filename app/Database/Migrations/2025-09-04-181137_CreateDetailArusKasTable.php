<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailArusKasTable extends Migration
{
    public function up()
    {
        // Mendefinisikan struktur field untuk tabel detail_arus_kas
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
            'master_arus_kas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
        ]);

        // Menjadikan 'id' sebagai primary key
        $this->forge->addKey('id', true);

        // Menambahkan foreign key untuk relasi ke tabel 'master_arus_kas'
        // ON DELETE CASCADE: Jika komponen di master dihapus, data detail yang terkait juga akan terhapus.
        // ON UPDATE CASCADE: Jika ID di master berubah, ID di sini juga akan ikut berubah.
        $this->forge->addForeignKey('master_arus_kas_id', 'master_arus_kas', 'id', 'CASCADE', 'CASCADE');

        // Membuat tabel 'detail_arus_kas'
        $this->forge->createTable('detail_arus_kas');
    }

    public function down()
    {
        // Menghapus tabel 'detail_arus_kas' jika migrasi di-rollback
        $this->forge->dropTable('detail_arus_kas');
    }
}
