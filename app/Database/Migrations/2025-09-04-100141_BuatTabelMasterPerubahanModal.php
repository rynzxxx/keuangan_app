<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BuatTabelMasterPerubahanModal extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_komponen' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'kategori' => ['type' => 'ENUM("penambahan", "pengurangan")', 'default' => 'penambahan'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('master_perubahan_modal');
    }

    public function down()
    {
        $this->forge->dropTable('master_perubahan_modal');
    }
}
