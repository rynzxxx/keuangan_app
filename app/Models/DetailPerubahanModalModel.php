<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPerubahanModalModel extends Model
{
    protected $table            = 'detail_perubahan_modal';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['tahun', 'master_perubahan_modal_id', 'jumlah'];
    protected $useTimestamps    = true;
}
