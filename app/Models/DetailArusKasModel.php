<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailArusKasModel extends Model
{
    protected $table            = 'detail_arus_kas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['tahun', 'master_arus_kas_id', 'jumlah'];
    protected $useTimestamps    = false;
}
