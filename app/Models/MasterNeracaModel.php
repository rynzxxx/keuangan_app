<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterNeracaModel extends Model
{
    protected $table = 'master_neraca';
    protected $allowedFields = ['nama_komponen', 'kategori'];
}
