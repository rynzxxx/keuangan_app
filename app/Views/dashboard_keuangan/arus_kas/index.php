<?= $this->extend('dashboard_keuangan/layout/template') ?>

<?= $this->section('content') ?>
<style>
    /* --- Style Global & Helper --- */

    /* Style untuk Input Readonly agar konsisten di semua halaman */
    input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
        opacity: 1;
    }

    /* Perbaikan untuk komponen Input Group agar rapi */
    .input-group>input[readonly].form-control,
    .input-group .form-control {
        border-left: 0;
    }

    .input-group-text {
        background-color: #e9ecef;
        border-right: 0;
    }

    .input-group {
        align-items: center;
        /* Perataan vertikal sempurna */
    }


    /* --- CSS Responsif untuk Halaman Laporan Arus Kas --- */
    @media (max-width: 767.98px) {
        .table-responsive-stack thead {
            /* Sembunyikan header tabel asli di mobile */
            display: none;
        }

        .table-responsive-stack table,
        .table-responsive-stack tbody,
        .table-responsive-stack tr,
        .table-responsive-stack td {
            display: block;
            /* Buat semua elemen menjadi blok */
        }

        .table-responsive-stack tr {
            /* Setiap baris menjadi sebuah "card" terpisah */
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        /* Sel pertama (berisi teks label) menjadi judul */
        .table-responsive-stack td:first-child {
            font-weight: bold;
            margin-bottom: 0.5rem;
            /* Jarak antara judul dan input di bawahnya */
        }

        /* Sel kedua (berisi input) */
        .table-responsive-stack td:last-child {
            padding: 0;
            /* Hapus padding bawaan sel */
        }

        /* Pastikan input group memenuhi lebar */
        .table-responsive-stack td .input-group {
            width: 100%;
        }

        /* Khusus untuk baris <hr>, jangan beri style card */
        .table-responsive-stack tr td[colspan="2"] {
            padding: 0;
        }

        .table-responsive-stack tr:has(td[colspan="2"]) {
            padding: 0;
            border: none;
            margin-bottom: 0;
        }
    }
</style>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header"><i class="fas fa-filter me-2"></i>Pilih Periode Laporan</div>
        <div class="card-body">
            <form action="<?= site_url('arus-kas') ?>" method="get" class="row align-items-end">
                <div class="col-md-3">
                    <label for="tahun" class="form-label">Tahun Periode:</label>
                    <select name="tahun" id="tahun" class="form-select">
                        <option value="">Pilih Tahun</option>
                        <?php if (!empty($daftar_tahun)): ?>
                            <?php foreach ($daftar_tahun as $t): ?>
                                <option value="<?= $t['tahun'] ?>" <?= (isset($tahunDipilih) && $t['tahun'] == $tahunDipilih) ? 'selected' : '' ?>>
                                    <?= $t['tahun'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($tahunDipilih)) : ?>
        <form action="<?= base_url('/arus-kas/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun" value="<?= $tahunDipilih ?>">

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <h5 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">Laporan Arus Kas Tahun <?= $tahunDipilih ?></h5>
                    <div>
                        <a class="btn btn-outline-success btn-sm me-2" href="<?= site_url('arus-kas/export-excel/' . $tahunDipilih) ?>">
                            <i class="fas fa-file-excel fa-sm fa-fw"></i> Export Excel
                        </a>
                        <a class="btn btn-outline-danger btn-sm" href="<?= site_url('arus-kas/export-pdf/' . $tahunDipilih) ?>" target="_blank">
                            <i class="fas fa-file-pdf fa-sm fa-fw"></i> Export PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-success text-white d-flex align-items-center">
                                    <i class="fas fa-arrow-down me-2"></i>
                                    <h6 class="m-0 font-weight-bold">Arus Kas Masuk</h6>
                                </div>
                                <div class="card-body p-2 table-responsive-stack">
                                    <table class="table table-borderless table-sm align-middle">
                                        <tbody>
                                            <tr>
                                                <td>Penerimaan Pendapatan Operasional Utama</td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">Rp</span>
                                                        <input id="pendapatan-utama" type="text" class="form-control text-right" value="<?= number_format($pendapatanUtama, 0, ',', '.') ?>" readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php foreach ($komponenMasuk as $km) : ?>
                                                <tr>
                                                    <td><?= esc($km['nama_komponen']) ?></td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" class="form-control text-right rupiah-input" name="jumlah[<?= $km['id'] ?>]" data-kategori="masuk" value="<?= (int) $km['jumlah'] ?>">
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer font-weight-bold">
                                    <div class="d-flex justify-content-between">
                                        <span>TOTAL KAS MASUK</span>
                                        <span id="total-kas-masuk">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-danger text-white d-flex align-items-center">
                                    <i class="fas fa-arrow-up me-2"></i>
                                    <h6 class="m-0 font-weight-bold">Arus Kas Keluar</h6>
                                </div>
                                <div class="card-body p-2 table-responsive-stack">
                                    <table class="table table-borderless table-sm align-middle">
                                        <tbody>
                                            <tr>
                                                <td>Pembelian Barang dan Jasa</td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">Rp</span>
                                                        <input id="pembelian-barang" type="text" class="form-control text-right" value="<?= number_format($pembelianBarang, 0, ',', '.') ?>" readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Pembayaran Beban Gaji</td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">Rp</span>
                                                        <input id="beban-gaji" type="text" class="form-control text-right" value="<?= number_format($bebanGaji, 0, ',', '.') ?>" readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Pendapatan Asli Desa</td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">Rp</span>
                                                        <input id="pad" type="text" class="form-control text-right" value="<?= number_format($pad, 0, ',', '.') ?>" readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr class="my-1">
                                                </td>
                                            </tr>
                                            <?php foreach ($komponenKeluar as $kk) : ?>
                                                <tr>
                                                    <td><?= esc($kk['nama_komponen']) ?></td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" class="form-control text-right rupiah-input" name="jumlah[<?= $kk['id'] ?>]" data-kategori="keluar" value="<?= (int) $kk['jumlah'] ?>">
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer font-weight-bold">
                                    <div class="d-flex justify-content-between">
                                        <span>TOTAL KAS KELUAR</span>
                                        <span id="total-kas-keluar">Rp (0)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0 text-center text-md-start">
                            <h4 class="m-0">
                                <span class="text-gray-800">SALDO AKHIR:</span>
                                <span id="saldo-akhir" class="font-weight-bold text-primary">Rp 0</span>
                            </h4>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Laporan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function parseNumber(str) {
            return parseFloat(String(str).replace(/[^\d-]/g, '')) || 0;
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(num).replace('Rp', 'Rp ');
        }

        function calculateTotals() {
            let pendapatanUtama = parseNumber(document.getElementById('pendapatan-utama').value);
            let pembelianBarang = parseNumber(document.getElementById('pembelian-barang').value);
            let bebanGaji = parseNumber(document.getElementById('beban-gaji').value);
            let pad = parseNumber(document.getElementById('pad').value);

            let totalDinamisMasuk = 0;
            let totalDinamisKeluar = 0;

            document.querySelectorAll('.rupiah-input').forEach(input => {
                let nilai = parseNumber(input.value);
                if (input.dataset.kategori === 'masuk') {
                    totalDinamisMasuk += nilai;
                } else if (input.dataset.kategori === 'keluar') {
                    totalDinamisKeluar += nilai;
                }
            });

            let totalKasMasuk = pendapatanUtama + totalDinamisMasuk;
            let totalKasKeluar = pembelianBarang + bebanGaji + pad + totalDinamisKeluar;
            let saldoAkhir = totalKasMasuk - totalKasKeluar;

            document.getElementById('total-kas-masuk').innerText = formatRupiah(totalKasMasuk);
            document.getElementById('total-kas-keluar').innerText = `Rp (${new Intl.NumberFormat('id-ID').format(totalKasKeluar)})`;
            document.getElementById('saldo-akhir').innerText = formatRupiah(saldoAkhir);
        }

        document.querySelectorAll('.rupiah-input').forEach(function(input) {
            const mask = new IMask(input, {
                mask: Number,
                scale: 0,
                signed: false,
                thousandsSeparator: '.',
                min: 0
            });
            mask.on('accept', function() {
                calculateTotals();
            });
        });

        calculateTotals();
    });
</script>
<?= $this->endSection() ?>