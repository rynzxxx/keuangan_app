<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <!-- =================================================================== -->
    <!-- BAGIAN 1: FORM PEMILIHAN TAHUN (SELALU TAMPIL) -->
    <!-- =================================================================== -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Pilih Periode Laporan</h6>
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
                        <button type="submit" class="btn btn-info"><i class="fas fa-eye me-2"></i>Tampilkan Laporan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- BAGIAN 2: FORM LAPORAN NERACA (TAMPIL JIKA TAHUN DIPILIH) -->
    <!-- =================================================================== -->
    <?php if (isset($tahunDipilih)): ?>
        <hr class="my-4">
        <form action="<?= site_url('neraca-keuangan/simpan'); ?>" method="post" id="neraca-form">
            <?= csrf_field(); ?>
            <input type="hidden" name="tahun" value="<?= $tahunDipilih; ?>">

            <div class="row">
                <!-- Kolom Aktiva -->
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">AKTIVA</h6>
                        </div>
                        <div class="card-body" id="aktiva-card-body">
                            <h6 class="mt-2"><strong>Aktiva Lancar</strong></h6>
                            <?php foreach ($komponen['aktiva_lancar'] as $item): ?>
                                <div class="row mb-2">
                                    <label class="col-sm-6 col-form-label"><?= esc($item['nama_komponen']); ?></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-rupiah" data-kategori="aktiva_lancar" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="row">
                                <label class="col-sm-6 col-form-label fw-bold">JUMLAH AKTIVA LANCAR</label>
                                <div class="col-sm-6">
                                    <input type="text" id="total-aktiva-lancar" class="form-control-plaintext text-end fw-bold" value="0" readonly>
                                </div>
                            </div>

                            <h6 class="mt-4"><strong>Aktiva Tetap</strong></h6>
                            <?php foreach ($komponen['aktiva_tetap'] as $item): ?>
                                <div class="row mb-2">
                                    <label class="col-sm-6 col-form-label"><?= esc($item['nama_komponen']); ?></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-rupiah" data-kategori="aktiva_tetap" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="row">
                                <label class="col-sm-6 col-form-label fw-bold">JUMLAH AKTIVA TETAP</label>
                                <div class="col-sm-6">
                                    <input type="text" id="total-aktiva-tetap" class="form-control-plaintext text-end fw-bold" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-primary text-white d-flex justify-content-between">
                            <h6 class="m-0 font-weight-bold">TOTAL AKTIVA</h6>
                            <h6 class="m-0 font-weight-bold" id="total-aktiva">0</h6>
                        </div>
                    </div>
                </div>

                <!-- Kolom Pasiva -->
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="m-0 font-weight-bold">PASIVA</h6>
                        </div>
                        <div class="card-body" id="pasiva-card-body">
                            <h6 class="mt-2"><strong>Hutang Lancar</strong></h6>
                            <?php foreach ($komponen['hutang_lancar'] as $item): ?>
                                <div class="row mb-2">
                                    <label class="col-sm-6 col-form-label"><?= esc($item['nama_komponen']); ?></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-rupiah" data-kategori="hutang_lancar" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="row">
                                <label class="col-sm-6 col-form-label fw-bold">JUMLAH HUTANG LANCAR</label>
                                <div class="col-sm-6">
                                    <input type="text" id="total-hutang-lancar" class="form-control-plaintext text-end fw-bold" value="0" readonly>
                                </div>
                            </div>

                            <h6 class="mt-4"><strong>Hutang Jangka Panjang</strong></h6>
                            <?php foreach ($komponen['hutang_jangka_panjang'] as $item): ?>
                                <div class="row mb-2">
                                    <label class="col-sm-6 col-form-label"><?= esc($item['nama_komponen']); ?></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-rupiah" data-kategori="hutang_jangka_panjang" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="row">
                                <label class="col-sm-6 col-form-label fw-bold">JUMLAH HUTANG JANGKA PANJANG</label>
                                <div class="col-sm-6">
                                    <input type="text" id="total-hutang-jangka-panjang" class="form-control-plaintext text-end fw-bold" value="0" readonly>
                                </div>
                            </div>

                            <h6 class="mt-4"><strong>Modal</strong></h6>
                            <div class="row mb-2">
                                <label class="col-sm-6 col-form-label">Surplus/Defisit Ditahan</label>
                                <div class="col-sm-6">
                                    <input type="text" id="surplus-defisit" class="form-control-plaintext text-end fw-bold" value="<?= (int)$surplusDefisitDitahan; ?>" readonly>
                                </div>
                            </div>
                            <?php foreach ($komponen['modal'] as $item): ?>
                                <div class="row mb-2">
                                    <label class="col-sm-6 col-form-label"><?= esc($item['nama_komponen']); ?></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-rupiah" data-kategori="modal" name="jumlah[<?= $item['id']; ?>]" value="<?= (int)$item['jumlah']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <hr>
                            <div class="row">
                                <label class="col-sm-6 col-form-label fw-bold">JUMLAH MODAL</label>
                                <div class="col-sm-6">
                                    <input type="text" id="total-modal" class="form-control-plaintext text-end fw-bold" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-warning text-dark d-flex justify-content-between">
                            <h6 class="m-0 font-weight-bold">TOTAL PASIVA</h6>
                            <h6 class="m-0 font-weight-bold" id="total-pasiva">0</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div id="status-balance" class="fw-bold"></div>
                    <!-- [PERBAIKAN] Menambahkan Tombol Cetak/Ekspor -->
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

        <script src="https://unpkg.com/imask"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // --- FUNGSI BANTUAN ---
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

                // --- FUNGSI KALKULASI UTAMA ---
                function calculateNeraca() {
                    // Hitung Total Aktiva Lancar
                    let totalAktivaLancar = 0;
                    document.querySelectorAll('[data-kategori="aktiva_lancar"]').forEach(input => totalAktivaLancar += unformatRupiah(input.value));
                    document.getElementById('total-aktiva-lancar').value = formatRupiah(totalAktivaLancar);

                    // Hitung Total Aktiva Tetap
                    let totalAktivaTetap = 0;
                    document.querySelectorAll('[data-kategori="aktiva_tetap"]').forEach(input => totalAktivaTetap += unformatRupiah(input.value));
                    document.getElementById('total-aktiva-tetap').value = formatRupiah(totalAktivaTetap);

                    // Hitung Total Keseluruhan Aktiva
                    const totalAktiva = totalAktivaLancar + totalAktivaTetap;
                    document.getElementById('total-aktiva').textContent = formatRupiah(totalAktiva);

                    // Hitung Total Hutang Lancar
                    let totalHutangLancar = 0;
                    document.querySelectorAll('[data-kategori="hutang_lancar"]').forEach(input => totalHutangLancar += unformatRupiah(input.value));
                    document.getElementById('total-hutang-lancar').value = formatRupiah(totalHutangLancar);

                    // Hitung Total Hutang Jangka Panjang
                    let totalHutangJangkaPanjang = 0;
                    document.querySelectorAll('[data-kategori="hutang_jangka_panjang"]').forEach(input => totalHutangJangkaPanjang += unformatRupiah(input.value));
                    document.getElementById('total-hutang-jangka-panjang').value = formatRupiah(totalHutangJangkaPanjang);

                    // Hitung Total Modal
                    let surplusDefisit = unformatRupiah(document.getElementById('surplus-defisit').value);
                    let totalModalDinamis = 0;
                    document.querySelectorAll('[data-kategori="modal"]').forEach(input => totalModalDinamis += unformatRupiah(input.value));
                    const totalModal = surplusDefisit + totalModalDinamis;
                    document.getElementById('total-modal').value = formatRupiah(totalModal);

                    // Hitung Total Keseluruhan Pasiva
                    const totalPasiva = totalHutangLancar + totalHutangJangkaPanjang + totalModal;
                    document.getElementById('total-pasiva').textContent = formatRupiah(totalPasiva);

                    // Periksa dan tampilkan status balance
                    const statusDiv = document.getElementById('status-balance');
                    const selisih = totalAktiva - totalPasiva;
                    if (selisih === 0) {
                        statusDiv.textContent = 'BALANCE';
                        statusDiv.className = 'fw-bold text-success';
                    } else {
                        statusDiv.textContent = `TIDAK BALANCE (Selisih: ${formatRupiah(selisih)})`;
                        statusDiv.className = 'fw-bold text-danger';
                    }
                }

                // --- INISIALISASI ---
                initMask();
                calculateNeraca(); // Panggil sekali saat halaman dimuat

                // Tambahkan event listener ke form untuk kalkulasi otomatis
                document.getElementById('neraca-form').addEventListener('input', calculateNeraca);
            });
        </script>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>