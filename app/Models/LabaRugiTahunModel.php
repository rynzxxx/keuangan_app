<?php

namespace App\Models;

use CodeIgniter\Model;

class LabaRugiTahunModel extends Model
{
    // Sesuaikan nama tabel
    protected $table            = 'laba_rugi_tahun';

    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'tahun',
        'total_pendapatan',
        'total_biaya',
        'laba_rugi_bersih',
        'saldo_modal_akhir'
    ];

    protected $useTimestamps = true;
}
