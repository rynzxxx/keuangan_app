<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class LaporanArusKas extends BaseController
{
    // Method untuk menampilkan daftar data
    public function index()
    {
        $data = ['title' => 'Daftar Laporan Arus Kas'];
        return view('dashboard_keuangan/laporan_arus_kas/index', $data);
    }

    // Method untuk menampilkan form tambah data
    public function new()
    {
        $data = ['title' => 'Tambah Laporan Arus Kas Baru'];
        return view('dashboard_keuangan/laporan_arus_kas/new', $data);
    }

    // Method untuk menampilkan detail data
    public function detail($id)
    {
        // Di sini nantinya akan ada logika untuk mengambil data dari database berdasarkan $id
        $data = ['title' => 'Detail Laporan Arus Kas #' . $id];
        return view('dashboard_keuangan/laporan_arus_kas/detail', $data);
    }
}
