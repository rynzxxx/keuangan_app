<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPengeluaranModel extends Model
{
    // Konfigurasi dasar model
    protected $table            = 'detail_pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kolom yang diizinkan untuk diisi
    protected $allowedFields    = [
        'bku_id',
        'master_kategori_id',
        'deskripsi_pengeluaran',
        'jumlah'
    ];

    // Sama seperti detail pendapatan, kita hanya menggunakan 'created_at'
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
