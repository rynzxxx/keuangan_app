<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailAlokasiModel extends Model
{
    protected $table            = 'detail_alokasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'bku_id',
        'master_kategori_id',
        'persentase_saat_itu',
        'jumlah_alokasi',
        'jumlah_realisasi',
        'sisa_alokasi'
    ];

    // Menggunakan created_at saja
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
