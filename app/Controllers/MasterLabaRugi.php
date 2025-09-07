<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterLabaRugiModel;

class MasterLabaRugi extends BaseController
{
    protected $masterLabaRugiModel;

    public function __construct()
    {
        $this->masterLabaRugiModel = new MasterLabaRugiModel();
        helper('form');
    }

    public function index()
    {
        $semuaKomponen = $this->masterLabaRugiModel->orderBy('kategori, id')->findAll();

        // Kelompokkan berdasarkan kategori untuk tampilan yang lebih rapi
        $komponen = ['pendapatan' => [], 'biaya' => []];
        foreach ($semuaKomponen as $item) {
            $komponen[$item['kategori']][] = $item;
        }

        $data = [
            'title' => 'Master Komponen Laba Rugi',
            'komponen' => $komponen
        ];
        return view('dashboard_keuangan/master_laba_rugi/index', $data);
    }

    public function new()
    {
        $data = [
            'title' => 'Tambah Komponen Laba Rugi Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('dashboard_keuangan/master_laba_rugi/new', $data);
    }

    public function create()
    {
        $rules = [
            'nama_komponen' => 'required|is_unique[master_laba_rugi.nama_komponen]',
            'kategori'      => 'required|in_list[pendapatan,biaya]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/master-laba-rugi/new')->withInput();
        }

        $this->masterLabaRugiModel->save([
            'nama_komponen' => $this->request->getVar('nama_komponen'),
            'kategori'      => $this->request->getVar('kategori'),
        ]);

        session()->setFlashdata('success', 'Komponen berhasil ditambahkan.');
        return redirect()->to('/master-laba-rugi');
    }

    public function delete($id)
    {
        // Cari komponen berdasarkan ID, jika tidak ada tampilkan error
        if (!$this->masterLabaRugiModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data komponen tidak ditemukan.');
        }

        // Hapus data dari database
        $this->masterLabaRugiModel->delete($id);

        // Siapkan pesan sukses
        session()->setFlashdata('success', 'Komponen berhasil dihapus.');

        // Kembalikan ke halaman index
        return redirect()->to('/master-laba-rugi');
    }
}
