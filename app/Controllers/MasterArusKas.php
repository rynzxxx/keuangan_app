<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterArusKasModel;

class MasterArusKas extends BaseController
{
    public function index()
    {
        $model = new MasterArusKasModel();
        $data = [
            'title' => 'Master Komponen Arus Kas',
            'komponen_masuk' => $model->where('kategori', 'masuk')->findAll(),
            'komponen_keluar' => $model->where('kategori', 'keluar')->findAll(),
        ];
        return view('dashboard_keuangan/master_arus_kas/index', $data);
    }

    public function create()
    {
        $model = new MasterArusKasModel();
        $data = [
            'nama_komponen' => $this->request->getPost('nama_komponen'),
            'kategori' => $this->request->getPost('kategori'),
        ];

        if ($model->save($data)) {
            return redirect()->to('/master-arus-kas')->with('success', 'Komponen berhasil ditambahkan.');
        } else {
            return redirect()->to('/master-arus-kas')->with('error', 'Gagal menambahkan komponen.');
        }
    }

    public function update($id)
    {
        $model = new MasterArusKasModel();
        $data = [
            'nama_komponen' => $this->request->getPost('nama_komponen'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to('/master-arus-kas')->with('success', 'Komponen berhasil diperbarui.');
        } else {
            return redirect()->to('/master-arus-kas')->with('error', 'Gagal memperbarui komponen.');
        }
    }

    public function delete($id)
    {
        $model = new MasterArusKasModel();
        if ($model->delete($id)) {
            return redirect()->to('/master-arus-kas')->with('success', 'Komponen berhasil dihapus.');
        } else {
            return redirect()->to('/master-arus-kas')->with('error', 'Gagal menghapus komponen.');
        }
    }
}
