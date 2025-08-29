<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
        helper('form');
    }

    public function index()
    {
        // Ambil setiap pengaturan dari database
        $data = [
            'title' => 'Pengaturan Laporan',
            'ketua_bumdes' => $this->pengaturanModel->where('meta_key', 'ketua_bumdes')->first()['meta_value'] ?? '',
            'bendahara_bumdes' => $this->pengaturanModel->where('meta_key', 'bendahara_bumdes')->first()['meta_value'] ?? '',
            'lokasi_laporan' => $this->pengaturanModel->where('meta_key', 'lokasi_laporan')->first()['meta_value'] ?? '',
        ];
        return view('dashboard_keuangan/pengaturan/index', $data);
    }

    public function update()
    {
        // Ambil data dari form
        $dataToUpdate = [
            'ketua_bumdes' => $this->request->getPost('ketua_bumdes'),
            'bendahara_bumdes' => $this->request->getPost('bendahara_bumdes'),
            'lokasi_laporan' => $this->request->getPost('lokasi_laporan'),
        ];

        // Loop dan update setiap pengaturan
        foreach ($dataToUpdate as $key => $value) {
            $this->pengaturanModel->where('meta_key', $key)->set(['meta_value' => $value])->update();
        }

        return redirect()->to('/pengaturan')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
