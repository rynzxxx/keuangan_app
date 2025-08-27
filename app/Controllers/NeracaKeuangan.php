<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class NeracaKeuangan extends BaseController
{
    // Method untuk menampilkan daftar data
    public function index()
    {
        $data = ['title' => 'Daftar Neraca Keuangan'];
        return view('dashboard_keuangan/neraca_keuangan/index', $data);
    }

    // Method untuk menampilkan form tambah data
    public function new()
    {
        $data = ['title' => 'Tambah Neraca Keuangan Baru'];
        return view('dashboard_keuangan/neraca_keuangan/new', $data);
    }

    // Method untuk menampilkan detail data
    public function detail($id)
    {
        // Di sini nantinya akan ada logika untuk mengambil data dari database berdasarkan $id
        $data = ['title' => 'Detail Neraca Keuangan #' . $id];
        return view('dashboard_keuangan/neraca_keuangan/detail', $data);
    }
}
