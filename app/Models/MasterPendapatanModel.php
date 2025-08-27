<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterPendapatanModel extends Model
{
    // Konfigurasi dasar model
    protected $table            = 'master_pendapatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kolom yang diizinkan untuk diisi
    protected $allowedFields    = [
        'nama_pendapatan',
        'deskripsi'
    ];

    // Menggunakan timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
