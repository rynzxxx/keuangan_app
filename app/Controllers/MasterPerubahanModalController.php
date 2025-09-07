<?php

namespace App\Controllers;

use App\Models\MasterPerubahanModalModel;

class MasterPerubahanModalController extends BaseController
{
    protected $masterModel;

    public function __construct()
    {
        $this->masterModel = new MasterPerubahanModalModel();
    }

    // Read: Menampilkan semua data
    public function index()
    {
        $data = [
            'title' => 'Master Komponen Perubahan Modal',
            'komponen' => $this->masterModel->findAll(),
        ];
        return view('dashboard_keuangan/master_perubahan_modal/index', $data);
    }

    // Create: Menampilkan form tambah data
    public function new()
    {
        $data = [
            'title' => 'Tambah Komponen Baru',
        ];
        return view('dashboard_keuangan/master_perubahan_modal/new', $data);
    }

    // Create: Menyimpan data baru
    public function create()
    {
        // Aturan validasi
        $rules = [
            'nama_komponen' => 'required|min_length[3]',
            'kategori' => 'required|in_list[penambahan,pengurangan]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->masterModel->save([
            'nama_komponen' => $this->request->getPost('nama_komponen'),
            'kategori' => $this->request->getPost('kategori'),
        ]);

        return redirect()->to('/master-perubahan-modal')->with('success', 'Komponen baru berhasil ditambahkan.');
    }

    // Delete: Menghapus data
    public function delete($id)
    {
        $this->masterModel->delete($id);
        return redirect()->to('/master-perubahan-modal')->with('success', 'Komponen berhasil dihapus.');
    }
}
