<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterArusKasModel extends Model
{
    protected $table            = 'master_arus_kas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama_komponen', 'kategori'];
    protected $useTimestamps    = false;
}
