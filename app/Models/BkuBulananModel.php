<?php

namespace App\Models;

use CodeIgniter\Model;

class BkuBulananModel extends Model
{
    // Nama tabel di database
    protected $table            = 'bku_bulanan';

    // Primary key dari tabel
    protected $primaryKey       = 'id';

    // Mengizinkan penggunaan auto increment
    protected $useAutoIncrement = true;

    // Tipe data yang akan dikembalikan (object atau array)
    protected $returnType       = 'array';

    // Menggunakan soft deletes (opsional, jika Anda ingin data tidak benar-benar terhapus)
    protected $useSoftDeletes   = false;

    // Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignment)
    // PENTING: Ini adalah bagian yang paling krusial untuk mencegah error!
    protected $allowedFields    = [
        'bulan',
        'tahun',
        'saldo_bulan_lalu',
        'penghasilan_bulan_ini',
        'total_pendapatan',
        'total_pengeluaran',
        'saldo_akhir'
    ];

    // Mengaktifkan penggunaan timestamps (created_at, updated_at)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Aturan validasi (opsional, tapi sangat direkomendasikan)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}
