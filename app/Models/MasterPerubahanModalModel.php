<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterPerubahanModalModel extends Model
{
    protected $table            = 'master_perubahan_modal';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama_komponen', 'kategori'];
    protected $useTimestamps    = true;
}
