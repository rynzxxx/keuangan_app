<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengaturanTable extends Migration

{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'meta_key' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'meta_value' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pengaturan');

        // Langsung isi dengan data awal
        $this->db->table('pengaturan')->insertBatch([
            ['meta_key' => 'kepala_desa', 'meta_value' => 'KHOERUDIN S. Sos'],
            ['meta_key' => 'penasihat', 'meta_value' => 'KHOERUDIN'],
            ['meta_key' => 'pengawas', 'meta_value' => 'SUDARSO'],
            ['meta_key' => 'ketua_bumdes', 'meta_value' => 'KARTIM'],
            ['meta_key' => 'bendahara_bumdes', 'meta_value' => 'RUSTIANI'],
            ['meta_key' => 'lokasi_laporan', 'meta_value' => 'Melung'],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan');
    }
}
