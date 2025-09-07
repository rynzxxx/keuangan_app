<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .laporan-section {
        margin-bottom: 2rem;
    }

    .laporan-section-header {
        font-size: 1.1rem;
        font-weight: 700;
        color: #4e73df;
        border-bottom: 2px solid #eaecf4;
        padding-bottom: 0.75rem;
        margin-bottom: 1rem;
    }

    .laporan-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid #eaecf4;
    }

    .laporan-item label {
        margin-bottom: 0;
        padding-right: 1rem;
        color: #5a5c69;
    }

    .laporan-item .input-group {
        max-width: 45%;
    }

    .laporan-subtotal {
        background-color: #f8f9fc;
        border-top: 2px solid #e3e6f0;
        border-bottom: 2px solid #e3e6f0;
    }

    .laporan-final-total {
        transition: background-color 0.3s ease;
    }

    .laporan-final-total.laba {
        background-color: #d1e7dd;
        /* Success */
    }

    .laporan-final-total.rugi {
        background-color: #f8d7da;
        /* Danger */
    }

    .permanent-field {
        background-color: #e9ecef !important;
        cursor: not-allowed;
    }

    /* Tampilan Responsif */
    @media (max-width: 767.98px) {
        .laporan-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .laporan-item .input-group {
            width: 100%;
            max-width: 100%;
        }
    }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Pilih Periode Laporan</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('laba-rugi'); ?>" method="get">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="tahun" class="form-label">Pilih Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select" required>
                            <option value="">-- Pilih Tahun --</option>
                            <?php foreach ($daftar_tahun as $th): ?>
                                <option value="<?= $th['tahun']; ?>" <?= (isset($tahunDipilih) && $tahunDipilih == $th['tahun']) ? 'selected' : ''; ?>>
                                    <?= $th['tahun']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-eye me-2"></i>Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($tahunDipilih)): ?>
        <form action="<?= site_url('laba-rugi/simpan'); ?>" method="post" id="laba-rugi-form">
            <?= csrf_field(); ?>
            <input type="hidden" name="tahun" value="<?= $tahunDipilih; ?>">

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                    <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">Laporan Laba Rugi Tahun <?= esc($tahunDipilih); ?></h6>
                    <div>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-print me-2"></i>Cetak/Ekspor
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= site_url('laba-rugi/cetak-pdf/' . $tahunDipilih); ?>" target="_blank"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('laba-rugi/cetak-excel/' . $tahunDipilih); ?>"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                            </ul>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                    </div>
                </div>
                <div class="card-body p-lg-4">

                    <div class="laporan-section">
                        <h5 class="laporan-section-header">PENDAPATAN</h5>
                        <div class="laporan-item">
                            <label>Pendapatan Usaha (dari BKU)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control text-end permanent-field" id="pendapatan-usaha" value="<?= (int)$pendapatanUsaha; ?>" readonly>
                            </div>
                        </div>
                        <?php foreach ($komponenPendapatan as $item): ?>
                            <div class="laporan-item">
                                <label><?= esc($item['nama_komponen']); ?></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control input-rupiah text-end" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>" data-kategori="pendapatan">
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="laporan-item laporan-subtotal fw-bold">
                            <label>TOTAL PENDAPATAN</label>
                            <span id="total-pendapatan">Rp 0</span>
                        </div>
                    </div>

                    <div class="laporan-section">
                        <h5 class="laporan-section-header">BIAYA-BIAYA</h5>
                        <div class="laporan-item">
                            <label>Biaya Bahan Baku (Pengembangan)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control text-end permanent-field" id="biaya-bahan-baku" value="<?= (int)$biayaBahanBaku; ?>" readonly>
                            </div>
                        </div>
                        <div class="laporan-item">
                            <label>Biaya Gaji (Honor)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control text-end permanent-field" id="biaya-gaji" value="<?= (int)$biayaGaji; ?>" readonly>
                            </div>
                        </div>
                        <div class="laporan-item">
                            <label>Pendapatan Asli Desa (PAD)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control text-end permanent-field" id="pad" value="<?= (int)$pad; ?>" readonly>
                            </div>
                        </div>
                        <?php foreach ($komponenBiaya as $item): ?>
                            <div class="laporan-item">
                                <label><?= esc($item['nama_komponen']); ?></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control input-rupiah text-end" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>" data-kategori="biaya">
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="laporan-item laporan-subtotal fw-bold">
                            <label>TOTAL BIAYA</label>
                            <span id="total-biaya">Rp 0</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer laporan-final-total" id="baris-laba-rugi">
                    <div class="laporan-item border-0 px-lg-3">
                        <h5 class="m-0 fw-bold">LABA / (RUGI) BERSIH</h5>
                        <h5 class="m-0 fw-bold" id="laba-rugi-bersih">Rp 0</h5>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('laba-rugi-form');
        if (!form) return;

        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        function unformatRupiah(rupiahStr) {
            if (typeof rupiahStr !== 'string' || rupiahStr === null) return 0;
            return parseInt(rupiahStr.replace(/[^0-9]/g, ''), 10) || 0;
        }

        function initMask() {
            document.querySelectorAll('.input-rupiah').forEach(input => {
                IMask(input, {
                    mask: Number,
                    scale: 0,
                    signed: false,
                    thousandsSeparator: '.',
                    min: 0
                });
            });
        }

        function calculateLabaRugi() {
            let totalPendapatan = unformatRupiah(document.getElementById('pendapatan-usaha').value);
            document.querySelectorAll('input[data-kategori="pendapatan"]').forEach(input => {
                totalPendapatan += unformatRupiah(input.value);
            });
            document.getElementById('total-pendapatan').textContent = formatRupiah(totalPendapatan);

            let totalBiaya = unformatRupiah(document.getElementById('biaya-bahan-baku').value) + unformatRupiah(document.getElementById('biaya-gaji').value) + unformatRupiah(document.getElementById('pad').value);
            document.querySelectorAll('input[data-kategori="biaya"]').forEach(input => {
                totalBiaya += unformatRupiah(input.value);
            });
            document.getElementById('total-biaya').textContent = formatRupiah(totalBiaya);

            const labaRugi = totalPendapatan - totalBiaya;
            const labaRugiCell = document.getElementById('laba-rugi-bersih');
            const labaRugiRow = document.getElementById('baris-laba-rugi');

            labaRugiCell.textContent = formatRupiah(labaRugi);

            labaRugiRow.classList.remove('laba', 'rugi');
            if (labaRugi >= 0) {
                labaRugiRow.classList.add('laba');
            } else {
                labaRugiRow.classList.add('rugi');
            }
        }

        initMask();
        calculateLabaRugi();
        form.addEventListener('input', calculateLabaRugi);
    });
</script>
<?= $this->endSection(); ?>