<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Komponen Laba Rugi</h6>
        </div>
        <div class="card-body">
            <?= form_open('/master-laba-rugi'); ?>
            <div class="mb-3">
                <label for="nama_komponen" class="form-label">Nama Komponen</label>
                <input type="text" class="form-control <?= ($validation->hasError('nama_komponen')) ? 'is-invalid' : ''; ?>" id="nama_komponen" name="nama_komponen" value="<?= old('nama_komponen'); ?>" autofocus>
                <div class="invalid-feedback"><?= $validation->getError('nama_komponen'); ?></div>
            </div>

            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select <?= ($validation->hasError('kategori')) ? 'is-invalid' : ''; ?>" id="kategori" name="kategori">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="pendapatan" <?= (old('kategori') == 'pendapatan') ? 'selected' : ''; ?>>Pendapatan</option>
                    <option value="biaya" <?= (old('kategori') == 'biaya') ? 'selected' : ''; ?>>Biaya</option>
                </select>
                <div class="invalid-feedback"><?= $validation->getError('kategori'); ?></div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="<?= site_url('/master-laba-rugi'); ?>" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
```

#### **Langkah 4: Tambahkan Route**
Buka `app/Config/Routes.php` dan tambahkan `resource route` baru ini:
```php
$routes->resource('master-laba-rugi', ['controller' => 'MasterLabaRugi']);
```
> Sekarang Anda sudah memiliki halaman Master Laba Rugi yang berfungsi penuh!

---
### ## Bagian 2: Menyesuaikan Halaman Laporan Laba Rugi

Sekarang, kita perbarui `LabaRugi.php` agar menggunakan master data yang baru ini.

**Lokasi File:** `app/Controllers/LabaRugi.php`

Ganti **`use App\Models\MasterNeracaModel;`** menjadi **`use App\Models\MasterLabaRugiModel;`** di bagian atas file.

Kemudian, ganti method `getLaporanLabaRugiData()` dengan versi baru ini:
```php
private function getLaporanLabaRugiData($tahun)
{
$bkuModel = new BkuBulananModel();
$masterLabaRugiModel = new MasterLabaRugiModel(); // Gunakan model baru
$db = \Config\Database::connect();

// 1. Ambil Total Pendapatan dari BKU Tahunan (hanya penghasilan murni)
$totalPenghasilanSetahun = $bkuModel->selectSum('penghasilan_bulan_ini')->where('tahun', $tahun)->get()->getRow()->penghasilan_bulan_ini ?? 0;

// 2. Ambil Rekap Pengeluaran per Kategori dari BKU Tahunan
$builder = $db->table('detail_alokasi as da');
$builder->select('mk.nama_kategori, SUM(da.jumlah_realisasi) as total_per_kategori');
$builder->join('bku_bulanan as bb', 'bb.id = da.bku_id');
$builder->join('master_kategori_pengeluaran as mk', 'mk.id = da.master_kategori_id');
$builder->where('bb.tahun', $tahun);
$builder->groupBy('mk.nama_kategori');
$pengeluaranBKU = $builder->get()->getResultArray();
$pengeluaranBKUMap = array_column($pengeluaranBKU, 'total_per_kategori', 'nama_kategori');

// 3. Ambil Komponen Laba Rugi dari Master Laba Rugi yang baru
$komponenPendapatan = $masterLabaRugiModel->where('kategori', 'pendapatan')->findAll();
$komponenBiaya = $masterLabaRugiModel->where('kategori', 'biaya')->findAll();

return [
'pendapatanUsaha' => $totalPenghasilanSetahun,
'biayaBahanBaku' => $pengeluaranBKUMap['PENGEMBANGAN'] ?? 0,
'biayaGaji' => $pengeluaranBKUMap['HONOR'] ?? 0,
'pad' => $pengeluaranBKUMap['PAD'] ?? 0,
'komponenPendapatan' => $komponenPendapatan,
'komponenBiaya' => $komponenBiaya,
];
}