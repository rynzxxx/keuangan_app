<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<style>
    /* --- [TAMBAHAN] Style untuk Halaman Neraca Keuangan --- */

    /* Membuat layout baris item yang fleksibel */
    .form-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        /* Jarak antara label dan input */
        margin-bottom: 0.75rem;
    }

    .form-item-row label {
        margin-bottom: 0;
        /* Hapus margin bawah dari label */
        flex-shrink: 0;
        /* Mencegah label menyusut */
    }

    .form-item-row .input-group {
        max-width: 50%;
        /* Batasi lebar input di desktop */
    }

    /* Tampilan Responsif untuk Neraca di layar kecil */
    @media (max-width: 991.98px) {

        /* Breakpoint LG, saat layout berubah jadi TABS */
        .form-item-row {
            flex-direction: column;
            /* Susun vertikal: label di atas, input di bawah */
            align-items: flex-start;
            /* Semua rata kiri */
            gap: 0.25rem;
            /* Perkecil jarak */
        }

        .form-item-row label {
            font-size: 0.9rem;
        }

        .form-item-row .input-group {
            width: 100%;
            /* Lebar penuh di mobile */
            max-width: 100%;
        }
    }
</style>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Pilih Periode Laporan</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('neraca-keuangan'); ?>" method="get">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="tahun" class="form-label">Pilih Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select" required>
                            <option value="">-- Pilih Tahun --</option>
                            <?php foreach ($daftar_tahun as $item): ?>
                                <option value="<?= $item['tahun']; ?>" <?= (isset($tahunDipilih) && $tahunDipilih == $item['tahun']) ? 'selected' : ''; ?>>
                                    <?= $item['tahun']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-eye me-2"></i>Tampilkan Laporan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($tahunDipilih)): ?>
        <hr class="my-4">
        <form action="<?= site_url('neraca-keuangan/simpan'); ?>" method="post" id="neraca-form">
            <?= csrf_field(); ?>
            <input type="hidden" name="tahun" value="<?= $tahunDipilih; ?>">

            <div class="row d-none d-lg-flex">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">AKTIVA</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="mt-2"><strong>Aktiva Lancar</strong></h6>
                            <?php foreach ($komponen['aktiva_lancar'] as $item): ?>
                                <div class="form-item-row">
                                    <label><?= esc($item['nama_komponen']); ?></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end input-rupiah" data-kategori="aktiva_lancar" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="form-item-row fw-bold">
                                <label>JUMLAH AKTIVA LANCAR</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-end fw-bold total-aktiva-lancar" value="0" readonly>
                                </div>
                            </div>

                            <h6 class="mt-4"><strong>Aktiva Tetap</strong></h6>
                            <?php foreach ($komponen['aktiva_tetap'] as $item): ?>
                                <div class="form-item-row">
                                    <label><?= esc($item['nama_komponen']); ?></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end input-rupiah" data-kategori="aktiva_tetap" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="form-item-row fw-bold">
                                <label>JUMLAH AKTIVA TETAP</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-end fw-bold total-aktiva-tetap" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-primary text-white d-flex justify-content-between">
                            <h6 class="m-0 font-weight-bold">TOTAL AKTIVA</h6>
                            <h6 class="m-0 font-weight-bold total-aktiva">0</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="m-0 font-weight-bold">PASIVA</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="mt-2"><strong>Hutang Lancar</strong></h6>
                            <?php foreach ($komponen['hutang_lancar'] as $item): ?>
                                <div class="form-item-row">
                                    <label><?= esc($item['nama_komponen']); ?></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end input-rupiah" data-kategori="hutang_lancar" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="form-item-row fw-bold">
                                <label>JUMLAH HUTANG LANCAR</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-end fw-bold total-hutang-lancar" value="0" readonly>
                                </div>
                            </div>

                            <h6 class="mt-4"><strong>Hutang Jangka Panjang</strong></h6>
                            <?php foreach ($komponen['hutang_jangka_panjang'] as $item): ?>
                                <div class="form-item-row">
                                    <label><?= esc($item['nama_komponen']); ?></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end input-rupiah" data-kategori="hutang_jangka_panjang" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="form-item-row fw-bold">
                                <label>JUMLAH HUTANG JANGKA PANJANG</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-end fw-bold total-hutang-jangka-panjang" value="0" readonly>
                                </div>
                            </div>

                            <h6 class="mt-4"><strong>Modal</strong></h6>
                            <div class="form-item-row">
                                <label>Surplus/Defisit Ditahan</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-end surplus-defisit" value="<?= (int)$surplusDefisitDitahan; ?>" readonly>
                                </div>
                            </div>
                            <?php foreach ($komponen['modal'] as $item): ?>
                                <div class="form-item-row">
                                    <label><?= esc($item['nama_komponen']); ?></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end input-rupiah" data-kategori="modal" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="form-item-row fw-bold">
                                <label>JUMLAH MODAL</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-end fw-bold total-modal" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-warning text-dark d-flex justify-content-between">
                            <h6 class="m-0 font-weight-bold">TOTAL PASIVA</h6>
                            <h6 class="m-0 font-weight-bold total-pasiva">0</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-lg-none">
                <ul class="nav nav-tabs nav-fill" id="neracaTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="aktiva-tab" data-bs-toggle="tab" data-bs-target="#aktiva-tab-pane" type="button" role="tab">AKTIVA</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pasiva-tab" data-bs-toggle="tab" data-bs-target="#pasiva-tab-pane" type="button" role="tab">PASIVA</button>
                    </li>
                </ul>
                <div class="tab-content" id="neracaTabContent">
                    <div class="tab-pane fade show active" id="aktiva-tab-pane" role="tabpanel">
                        <div class="card shadow mb-4 border-top-0" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                            <div class="card-body">
                                <h6 class="mt-2"><strong>Aktiva Lancar</strong></h6>
                                <?php foreach ($komponen['aktiva_lancar'] as $item): ?>
                                    <div class="form-item-row">
                                        <label><?= esc($item['nama_komponen']); ?></label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control text-end input-rupiah" data-kategori="aktiva_lancar" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <hr>
                                <div class="form-item-row fw-bold">
                                    <label>JUMLAH AKTIVA LANCAR</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end fw-bold total-aktiva-lancar" value="0" readonly>
                                    </div>
                                </div>

                                <h6 class="mt-4"><strong>Aktiva Tetap</strong></h6>
                                <?php foreach ($komponen['aktiva_tetap'] as $item): ?>
                                    <div class="form-item-row">
                                        <label><?= esc($item['nama_komponen']); ?></label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control text-end input-rupiah" data-kategori="aktiva_tetap" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <hr>
                                <div class="form-item-row fw-bold">
                                    <label>JUMLAH AKTIVA TETAP</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end fw-bold total-aktiva-tetap" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-primary text-white d-flex justify-content-between">
                                <h6 class="m-0 font-weight-bold">TOTAL AKTIVA</h6>
                                <h6 class="m-0 font-weight-bold total-aktiva">0</h6>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pasiva-tab-pane" role="tabpanel">
                        <div class="card shadow mb-4 border-top-0" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                            <div class="card-body">
                                <h6 class="mt-2"><strong>Hutang Lancar</strong></h6>
                                <?php foreach ($komponen['hutang_lancar'] as $item): ?>
                                    <div class="form-item-row">
                                        <label><?= esc($item['nama_komponen']); ?></label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control text-end input-rupiah" data-kategori="hutang_lancar" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <hr>
                                <div class="form-item-row fw-bold">
                                    <label>JUMLAH HUTANG LANCAR</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end fw-bold total-hutang-lancar" value="0" readonly>
                                    </div>
                                </div>

                                <h6 class="mt-4"><strong>Hutang Jangka Panjang</strong></h6>
                                <?php foreach ($komponen['hutang_jangka_panjang'] as $item): ?>
                                    <div class="form-item-row">
                                        <label><?= esc($item['nama_komponen']); ?></label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control text-end input-rupiah" data-kategori="hutang_jangka_panjang" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <hr>
                                <div class="form-item-row fw-bold">
                                    <label>JUMLAH HUTANG JANGKA PANJANG</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end fw-bold total-hutang-jangka-panjang" value="0" readonly>
                                    </div>
                                </div>

                                <h6 class="mt-4"><strong>Modal</strong></h6>
                                <div class="form-item-row">
                                    <label>Surplus/Defisit Ditahan</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end surplus-defisit" value="<?= (int)$surplusDefisitDitahan; ?>" readonly>
                                    </div>
                                </div>
                                <?php foreach ($komponen['modal'] as $item): ?>
                                    <div class="form-item-row">
                                        <label><?= esc($item['nama_komponen']); ?></label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control text-end input-rupiah" data-kategori="modal" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <hr>
                                <div class="form-item-row fw-bold">
                                    <label>JUMLAH MODAL</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control text-end fw-bold total-modal" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-warning text-dark d-flex justify-content-between">
                                <h6 class="m-0 font-weight-bold">TOTAL PASIVA</h6>
                                <h6 class="m-0 font-weight-bold total-pasiva">0</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div id="status-balance" class="fw-bold mb-3 mb-md-0 fs-5"></div>
                    <div>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-print me-2"></i>Cetak/Ekspor
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= site_url('neraca-keuangan/cetak-pdf/' . $tahunDipilih); ?>" target="_blank"><i class="fas fa-file-pdf me-2"></i>Cetak PDF</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('neraca-keuangan/cetak-excel/' . $tahunDipilih); ?>"><i class="fas fa-file-excel me-2"></i>Ekspor Excel</a></li>
                            </ul>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan Data Neraca</button>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<?php if (isset($tahunDipilih)): ?>
    <script src="https://unpkg.com/imask"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function unformatRupiah(rupiahStr) {
                if (typeof rupiahStr !== 'string' || rupiahStr === null) return 0;
                return parseInt(rupiahStr.replace(/[^0-9]/g, ''), 10) || 0;
            }

            function initMask() {
                document.querySelectorAll('.input-rupiah').forEach(function(input) {
                    IMask(input, {
                        mask: Number,
                        scale: 0,
                        signed: false,
                        thousandsSeparator: '.',
                        min: 0
                    });
                });
            }

            function calculateNeraca() {
                let totalAktivaLancar = 0;
                document.querySelectorAll('[data-kategori="aktiva_lancar"]').forEach(input => totalAktivaLancar += unformatRupiah(input.value));
                document.querySelectorAll('.total-aktiva-lancar').forEach(el => el.value = formatRupiah(totalAktivaLancar));

                let totalAktivaTetap = 0;
                document.querySelectorAll('[data-kategori="aktiva_tetap"]').forEach(input => totalAktivaTetap += unformatRupiah(input.value));
                document.querySelectorAll('.total-aktiva-tetap').forEach(el => el.value = formatRupiah(totalAktivaTetap));

                const totalAktiva = totalAktivaLancar + totalAktivaTetap;
                document.querySelectorAll('.total-aktiva').forEach(el => el.textContent = formatRupiah(totalAktiva));

                let totalHutangLancar = 0;
                document.querySelectorAll('[data-kategori="hutang_lancar"]').forEach(input => totalHutangLancar += unformatRupiah(input.value));
                document.querySelectorAll('.total-hutang-lancar').forEach(el => el.value = formatRupiah(totalHutangLancar));

                let totalHutangJangkaPanjang = 0;
                document.querySelectorAll('[data-kategori="hutang_jangka_panjang"]').forEach(input => totalHutangJangkaPanjang += unformatRupiah(input.value));
                document.querySelectorAll('.total-hutang-jangka-panjang').forEach(el => el.value = formatRupiah(totalHutangJangkaPanjang));

                let surplusDefisit = 0;
                document.querySelectorAll('.surplus-defisit').forEach(el => surplusDefisit += unformatRupiah(el.value));

                let totalModalDinamis = 0;
                document.querySelectorAll('[data-kategori="modal"]').forEach(input => totalModalDinamis += unformatRupiah(input.value));

                const totalModal = surplusDefisit + totalModalDinamis;
                document.querySelectorAll('.total-modal').forEach(el => el.value = formatRupiah(totalModal));

                const totalPasiva = totalHutangLancar + totalHutangJangkaPanjang + totalModal;
                document.querySelectorAll('.total-pasiva').forEach(el => el.textContent = formatRupiah(totalPasiva));

                const statusDiv = document.getElementById('status-balance');
                const selisih = totalAktiva - totalPasiva;
                if (selisih === 0) {
                    statusDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>BALANCE';
                    statusDiv.className = 'fw-bold text-success fs-5';
                } else {
                    statusDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>TIDAK BALANCE (Selisih: ${formatRupiah(selisih)})`;
                    statusDiv.className = 'fw-bold text-danger fs-5';
                }
            }
            initMask();
            calculateNeraca();
            document.getElementById('neraca-form').addEventListener('input', calculateNeraca);
        });
    </script>
<?php endif; ?>
<?= $this->endSection(); ?>