<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterNeracaModel;

class MasterNeraca extends BaseController
{
    protected $masterNeracaModel;

    public function __construct()
    {
        $this->masterNeracaModel = new MasterNeracaModel();
        helper('form');
    }


    /**
     * Menampilkan daftar semua komponen neraca
     */
    public function index()
    {
        $semuaKomponen = $this->masterNeracaModel->orderBy('kategori, id')->findAll();

        // Kelompokkan komponen berdasarkan kategori untuk tampilan yang lebih rapi
        $komponenTergrup = [
            'aktiva_lancar' => [],
            'aktiva_tetap' => [],
            'hutang_lancar' => [],
            'hutang_jangka_panjang' => [],
            'modal' => []
        ];
        foreach ($semuaKomponen as $item) {
            if (isset($komponenTergrup[$item['kategori']])) {
                $komponenTergrup[$item['kategori']][] = $item;
            }
        }

        $data = [
            'title' => 'Master Komponen Neraca Keuangan',
            'komponen' => $komponenTergrup
        ];

        return view('dashboard_keuangan/master_neraca/index', $data);
    }

    /**
     * Menampilkan form untuk menambah data baru
     */
    public function new()
    {
        $data = [
            'title' => 'Tambah Komponen Neraca Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('dashboard_keuangan/master_neraca/new', $data);
    }

    /**
     * Memproses data dari form tambah
     */
    public function create()
    {
        $rules = [
            'nama_komponen' => 'required|is_unique[master_neraca.nama_komponen]',
            'kategori'      => 'required|in_list[aktiva_lancar,aktiva_tetap,hutang_lancar,hutang_jangka_panjang,modal]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/master-neraca/new')->withInput();
        }

        $this->masterNeracaModel->save([
            'nama_komponen' => $this->request->getVar('nama_komponen'),
            'kategori'      => $this->request->getVar('kategori'),
        ]);

        session()->setFlashdata('success', 'Komponen neraca berhasil ditambahkan.');
        return redirect()->to('/master-neraca');
    }

    /**
     * Menampilkan form untuk mengedit data
     */
    public function edit($id = null)
    {
        $data = [
            'title'    => 'Edit Komponen Neraca',
            'validation' => \Config\Services::validation(),
            'komponen' => $this->masterNeracaModel->find($id)
        ];

        if (empty($data['komponen'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Komponen neraca tidak ditemukan.');
        }

        return view('dashboard_keuangan/master_neraca/edit', $data);
    }

    /**
     * Memproses data dari form edit
     */
    public function update($id = null)
    {
        $dataLama = $this->masterNeracaModel->find($id);
        $namaKomponenRule = ($this->request->getVar('nama_komponen') == $dataLama['nama_komponen'])
            ? 'required'
            : 'required|is_unique[master_neraca.nama_komponen]';

        $rules = [
            'nama_komponen' => $namaKomponenRule,
            'kategori'      => 'required|in_list[aktiva_lancar,aktiva_tetap,hutang_lancar,hutang_jangka_panjang,modal]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/master-neraca/' . $id . '/edit')->withInput();
        }

        $this->masterNeracaModel->save([
            'id'            => $id,
            'nama_komponen' => $this->request->getVar('nama_komponen'),
            'kategori'      => $this->request->getVar('kategori'),
        ]);

        session()->setFlashdata('success', 'Komponen neraca berhasil diperbarui.');
        return redirect()->to('/master-neraca');
    }

    /**
     * Menghapus data
     */
    public function delete($id = null)
    {
        $this->masterNeracaModel->delete($id);
        session()->setFlashdata('success', 'Komponen neraca berhasil dihapus.');
        return redirect()->to('/master-neraca');
    }
}
