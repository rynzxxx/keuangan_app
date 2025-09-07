<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailLabaRugiModel extends Model
{
    protected $table            = 'detail_laba_rugi';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['tahun', 'master_laba_rugi_id', 'jumlah'];
}
