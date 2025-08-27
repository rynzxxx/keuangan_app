<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPendapatanModel extends Model
{
    // Konfigurasi dasar model
    protected $table            = 'detail_pendapatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Kolom yang diizinkan untuk diisi
    protected $allowedFields    = [
        'bku_id',
        'master_pendapatan_id',
        'jumlah'
    ];

    // Tabel ini hanya punya 'created_at', jadi kita atur timestamps-nya
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    // Nonaktifkan 'updated_at' karena tidak ada kolomnya di tabel
    protected $updatedField  = '';
}
