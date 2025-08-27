<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class BkuBulanan extends BaseController
{
    // Method untuk menampilkan daftar data
    public function index()
    {
        $data = ['title' => 'Daftar BKU Bulanan'];
        return view('dashboard_keuangan/bku_bulanan/index', $data);
    }

    // Method untuk menampilkan form tambah data
    public function new()
    {
        $data = ['title' => 'Tambah BKU Bulanan Baru'];
        return view('dashboard_keuangan/bku_bulanan/new', $data);
    }

    // Method untuk menampilkan detail data
    public function detail($id)
    {
        // Di sini nantinya akan ada logika untuk mengambil data dari database berdasarkan $id
        $data = ['title' => 'Detail BKU Bulanan #' . $id];
        return view('dashboard_keuangan/bku_bulanan/detail', $data);
    }
}
