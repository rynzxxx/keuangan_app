<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // Nama tabel di database
    protected $table = 'users';

    // Kunci utama tabel
    protected $primaryKey = 'id';

    // Kolom yang diizinkan untuk diisi (untuk keamanan)
    protected $allowedFields = ['username', 'password_hash'];

    // Kamu bisa menambahkan fungsi lain di sini jika dibutuhkan
    // Contoh: fungsi untuk mendapatkan data user berdasarkan username
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }
}
