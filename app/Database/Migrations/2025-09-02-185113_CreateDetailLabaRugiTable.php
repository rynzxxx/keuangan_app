<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailLabaRugiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tahun' => ['type' => 'INT', 'constraint' => 4],
            'master_laba_rugi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jumlah' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('master_laba_rugi_id', 'master_laba_rugi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['tahun', 'master_laba_rugi_id']);
        $this->forge->createTable('detail_laba_rugi');
    }

    public function down()
    {
        $this->forge->dropTable('detail_laba_rugi');
    }
}
