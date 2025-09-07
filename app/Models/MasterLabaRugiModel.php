<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterLabaRugiModel extends Model
{
    protected $table            = 'master_laba_rugi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nama_komponen', 'kategori'];
    protected $useTimestamps    = true;
}
