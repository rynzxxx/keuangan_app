<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatTabelDetailPerubahanModal extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tahun' => ['type' => 'YEAR', 'constraint' => '4'],
            'master_perubahan_modal_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jumlah' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        // Menambahkan foreign key untuk integritas data
        $this->forge->addForeignKey('master_perubahan_modal_id', 'master_perubahan_modal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_perubahan_modal');
    }

    public function down()
    {
        $this->forge->dropTable('detail_perubahan_modal');
    }
}
