<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterLabaRugiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_komponen' => ['type' => 'VARCHAR', 'constraint' => 255],
            'kategori' => [
                'type' => 'ENUM',
                'constraint' => ['pendapatan', 'biaya'], // Kategori khusus: pendapatan atau biaya
                'null' => false
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('master_laba_rugi');
    }

    public function down()
    {
        $this->forge->dropTable('master_laba_rugi');
    }
}
