<?php

namespace App\Models;

use CodeIgniter\Model;

class RekapArusKasModel extends Model
{
    protected $table            = 'rekap_arus_kas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['tahun', 'total_kas_masuk', 'total_kas_keluar', 'saldo_akhir'];
    protected $useTimestamps    = false;
}
