<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterKategoriPengeluaranModel;

class MasterKategori extends BaseController
{
    // Deklarasikan model agar bisa dipakai di seluruh class
    protected $kategoriModel;

    public function __construct()
    {
        // Inisialisasi model di constructor
        $this->kategoriModel = new MasterKategoriPengeluaranModel();
        // Load form helper untuk digunakan di view
        helper('form');
    }

    /**
     * Method khusus untuk pengecekan nama via AJAX.
     */
    public function checkNama()
    {
        // Hanya izinkan akses via AJAX
        if ($this->request->isAJAX()) {
            $namaKategori = $this->request->getGet('nama_kategori');
            $id = $this->request->getGet('id'); // ID kategori saat ini (untuk form edit)

            $query = $this->kategoriModel->where('nama_kategori', $namaKategori);

            // Jika ini adalah form edit, kita harus mengecualikan data saat ini dari pengecekan
            if ($id) {
                $query->where('id !=', $id);
            }

            $result = $query->first();
            $dataExists = ($result !== null);

            // Kirim response dalam format JSON
            return $this->response->setJSON(['exists' => $dataExists]);
        }
        // Jika bukan AJAX, tolak akses.
        return $this->response->setStatusCode(403);
    }

    /**
     * Menampilkan daftar semua kategori (READ)
     */
    public function index()
    {
        // [OPTIMASI] Panggil database cukup satu kali
        $kategori = $this->kategoriModel->findAll();
        $totalPersentase = array_sum(array_column($kategori, 'persentase'));

        $data = [
            'title' => 'Master Kategori Pengeluaran',
            // Gunakan kembali variabel yang sudah ada
            'kategori' => $kategori,
            'totalPersentase' => $totalPersentase
        ];
        return view('dashboard_keuangan/master_kategori/index', $data);
    }

    /**
     * Menampilkan form untuk menambah data baru (CREATE Form)
     */
    public function new()
    {
        // Hitung total persentase yang sudah ada
        $totalPersentaseSaatIni = array_sum(array_column($this->kategoriModel->findAll(), 'persentase'));
        $sisaPersentase = 100 - $totalPersentaseSaatIni;

        $data = [
            'title' => 'Tambah Kategori Baru',
            'validation' => \Config\Services::validation(),
            'sisaPersentase' => $sisaPersentase
        ];
        return view('dashboard_keuangan/master_kategori/new', $data);
    }

    /**
     * Memproses data dari form tambah data (CREATE Process)
     */
    public function create()
    {
        $totalPersentaseSaatIni = array_sum(array_column($this->kategoriModel->findAll(), 'persentase'));
        $sisaPersentase = 100 - $totalPersentaseSaatIni;

        // [PERBAIKAN] Aturan validasi is_unique yang benar untuk create
        $rules = [
            'nama_kategori' => 'required|is_unique[master_kategori_pengeluaran.nama_kategori,id,0,deleted_at,NULL]',
            'persentase' => "required|numeric|greater_than[0]|less_than_equal_to[{$sisaPersentase}]"
        ];

        $errors = [
            'persentase' => [
                'less_than_equal_to' => "Input persentase tidak boleh melebihi sisa alokasi ({$sisaPersentase}%)."
            ]
        ];

        // [PERBAIKAN] Sertakan array $errors agar pesan kustom tampil
        if (!$this->validate($rules, $errors)) {
            return redirect()->to('/master-kategori/new')->withInput();
        }

        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getVar('nama_kategori'),
            'persentase' => $this->request->getVar('persentase'),
        ]);

        session()->setFlashdata('success', 'Data kategori berhasil ditambahkan.');
        return redirect()->to('/master-kategori');
    }

    /**
     * Menampilkan form untuk mengedit data (UPDATE Form)
     */
    public function edit($id = null)
    {
        $data = [
            'title' => 'Edit Kategori',
            'validation' => \Config\Services::validation(),
            'kategori' => $this->kategoriModel->find($id)
        ];

        if (empty($data['kategori'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kategori tidak ditemukan.');
        }

        return view('dashboard_keuangan/master_kategori/edit', $data);
    }

    /**
     * Memproses data dari form edit (UPDATE Process)
     */
    public function update($id = null)
    {
        $dataLama = $this->kategoriModel->find($id);
        if (!$dataLama) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data kategori tidak ditemukan dengan ID: ' . $id);
        }

        $totalPersentaseLainnya = array_sum(array_column($this->kategoriModel->where('id !=', $id)->findAll(), 'persentase'));
        $sisaPersentase = 100 - $totalPersentaseLainnya;

        $namaKategoriRule = "required|is_unique[master_kategori_pengeluaran.nama_kategori,id,{$id},deleted_at,NULL]";
        if ($this->request->getVar('nama_kategori') == $dataLama['nama_kategori']) {
            $namaKategoriRule = 'required';
        }

        $rules = [
            'nama_kategori' => $namaKategoriRule,
            'persentase' => "required|numeric|greater_than[0]|less_than_equal_to[{$sisaPersentase}]"
        ];

        $errors = [
            'persentase' => [
                'less_than_equal_to' => "Input persentase tidak boleh melebihi sisa alokasi ({$sisaPersentase}%)."
            ]
        ];

        if (!$this->validate($rules, $errors)) {
            return redirect()->to('/master-kategori/' . $id . '/edit')->withInput();
        }

        $this->kategoriModel->save([
            'id' => $id,
            'nama_kategori' => $this->request->getVar('nama_kategori'),
            'persentase' => $this->request->getVar('persentase'),
        ]);

        session()->setFlashdata('success', 'Data kategori berhasil diperbarui.');
        return redirect()->to('/master-kategori');
    }

    /**
     * Menghapus data berdasarkan ID (DELETE)
     */
    public function delete($id = null)
    {
        // Asumsi soft delete aktif, maka parameter kedua (true) tidak diperlukan jika ingin soft delete
        // Jika ingin hapus permanen, pastikan $useSoftDeletes di model false
        $this->kategoriModel->delete($id);
        session()->setFlashdata('success', 'Data kategori berhasil dihapus.');
        return redirect()->to('/master-kategori');
    }
}
