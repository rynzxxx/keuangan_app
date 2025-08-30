<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['meta_key', 'meta_value'];

    // Tambahkan metode ini
    public function getAllAsArray()
    {
        $result = $this->findAll();
        $settings = [];
        foreach ($result as $item) {
            $settings[$item['meta_key']] = $item['meta_value'];
        }
        return $settings;
    }
}
