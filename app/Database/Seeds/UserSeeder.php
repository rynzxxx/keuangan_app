<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Menyiapkan data untuk dimasukkan
        $data = [
            'username' => 'admin',
            // Kita hash passwordnya langsung di sini!
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT)
        ];

        // Menggunakan Query Builder untuk memasukkan data
        $this->db->table('users')->insert($data);
    }
}
