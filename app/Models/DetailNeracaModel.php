<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailNeracaModel extends Model
{
    protected $table = 'detail_neraca';
    protected $allowedFields = ['tahun', 'master_neraca_id', 'jumlah'];
}
