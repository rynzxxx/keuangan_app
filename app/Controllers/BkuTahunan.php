<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class BkuTahunan extends BaseController
{
    // Method untuk menampilkan daftar data
    public function index()
    {
        $data = ['title' => 'Daftar BKU Tahunan'];
        return view('dashboard_keuangan/bku_tahunan/index', $data);
    }

    // Method untuk menampilkan form tambah data
    public function new()
    {
        $data = ['title' => 'Tambah BKU Tahunan Baru'];
        return view('dashboard_keuangan/bku_tahunan/new', $data);
    }

    // Method untuk menampilkan detail data
    public function detail($id)
    {
        // Di sini nantinya akan ada logika untuk mengambil data dari database berdasarkan $id
        $data = ['title' => 'Detail BKU Tahunan #' . $id];
        return view('dashboard_keuangan/bku_tahunan/detail', $data);
    }
}
