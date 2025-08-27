<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterKategoriPengeluaranModel extends Model
{
    // Konfigurasi dasar model
    protected $table            = 'master_kategori_pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kolom yang diizinkan untuk diisi
    protected $allowedFields    = [
        'nama_kategori',
        'persentase'
    ];

    protected $useSoftDeletes = false;

    // Menggunakan timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
