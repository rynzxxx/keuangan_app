<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<style>
    /* CSS Kustom untuk tampilan yang lebih baik */
    .summary-card h6 {
        font-size: 0.8rem;
    }

    .summary-card h4 {
        font-weight: 700;
    }

    .remove-btn {
        font-size: 1.2rem;
        cursor: pointer;
        color: var(--bs-danger);
        transition: transform 0.2s;
    }

    .remove-btn:hover {
        transform: scale(1.2);
    }

    .input-rupiah {
        text-align: right;
    }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4 summary-card">
        <div class="card-header">
            <h5 class="m-0 font-weight-bold text-primary">Ringkasan Keuangan (Real-time)</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6><i class="fas fa-arrow-down text-success"></i> TOTAL PENDAPATAN</h6>
                    <h4 id="summary-pendapatan">0</h4>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6><i class="fas fa-arrow-up text-danger"></i> TOTAL PENGELUARAN</h6>
                    <h4 id="summary-pengeluaran">0</h4>
                </div>
                <div class="col-md-4">
                    <h6><i class="fas fa-wallet text-primary"></i> SALDO AKHIR</h6>
                    <h4 id="summary-saldo">0</h4>
                </div>
            </div>
            <hr>
            <h6><i class="fas fa-chart-pie"></i> Alokasi Dana & Realisasi Pengeluaran</h6>
            <div id="alokasi-dana-wrapper" class="table-responsive mt-2">
                <p class="text-muted">Tabel alokasi akan muncul setelah Anda memasukkan pendapatan.</p>
            </div>
        </div>
    </div>

    <form action="<?= site_url('bku-bulanan'); ?>" method="post" id="bku-form">
        <?= csrf_field(); ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                <div class="row g-3 align-items-center mb-3 mb-md-0">
                    <div class="col-auto">
                        <label for="bulan" class="form-label fw-bold">Periode Laporan:</label>
                    </div>
                    <div class="col-auto">
                        <select name="bulan" id="bulan" class="form-select" required>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i; ?>" <?= (date('m') == $i) ? 'selected' : ''; ?>><?= date('F', mktime(0, 0, 0, $i, 10)); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="tahun" id="tahun" class="form-select" required>
                            <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                                <option value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i> Simpan Laporan</button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="m-0"><i class="fas fa-plus-circle me-2"></i>PENDAPATAN</h5>
                    </div>
                    <div class="card-body">
                        <div id="pendapatan-wrapper">
                            <!-- FIELD PERMANEN 1: Sisa Saldo Bulan Lalu -->
                            <div class="row g-3 mb-3 align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label-plaintext fw-bold">Sisa Saldo Bulan Lalu</label>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" id="saldo-bulan-lalu" class="form-control-plaintext text-end fw-bold" value="Mencari..." readonly>
                                </div>
                                <div class="col-md-1"></div> <!-- Kolom kosong untuk alignment -->
                            </div>

                            <!-- FIELD PERMANEN 2: Penghasilan Bulan Ini -->
                            <div class="row g-3 mb-3 align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label-plaintext fw-bold">Penghasilan Bulan Ini</label>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" id="penghasilan-bulan-ini" class="form-control-plaintext text-end fw-bold" value="0" readonly>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                            <hr>
                            <!-- Baris pendapatan dinamis akan ditambahkan di sini oleh JS -->
                        </div>
                        <button type="button" class="btn btn-outline-success w-100 mt-2" id="add-pendapatan">
                            <i class="fas fa-plus"></i> Tambah Baris Pendapatan
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="m-0"><i class="fas fa-minus-circle me-2"></i>PENGELUARAN</h5>
                    </div>
                    <div class="card-body">
                        <div id="pengeluaran-wrapper">
                        </div>
                        <button type="button" class="btn btn-outline-danger w-100 mt-2" id="add-pengeluaran">
                            <i class="fas fa-plus"></i> Tambah Baris Pengeluaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://unpkg.com/imask"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ===================================================================================
        // SETUP AWAL & VARIABEL GLOBAL
        // ===================================================================================
        const masterPendapatan = <?= json_encode($master_pendapatan); ?>;
        const masterKategori = <?= json_encode($master_kategori); ?>;
        let pendapatanIndex = 0;
        let pengeluaranIndex = 0;
        const pendapatanWrapper = document.getElementById('pendapatan-wrapper');
        const pengeluaranWrapper = document.getElementById('pengeluaran-wrapper');
        const addPendapatanBtn = document.getElementById('add-pendapatan');
        const addPengeluaranBtn = document.getElementById('add-pengeluaran');
        const bkuForm = document.getElementById('bku-form');
        const bulanDropdown = document.getElementById('bulan');
        const tahunDropdown = document.getElementById('tahun');
        const saldoBulanLaluInput = document.getElementById('saldo-bulan-lalu');
        const penghasilanBulanIniInput = document.getElementById('penghasilan-bulan-ini');

        // [PERBAIKAN] Fungsi ini diubah untuk mengirim token CSRF
        async function fetchSaldoBulanLalu() {
            const bulan = bulanDropdown.value;
            const tahun = tahunDropdown.value;
            saldoBulanLaluInput.value = "Mencari...";

            // Ambil token CSRF dari hidden input di dalam form
            const csrfHash = document.querySelector('input[name="<?= csrf_token() ?>"]').value;

            try {
                const response = await fetch(`<?= site_url('/bku-bulanan/get-saldo-lalu') ?>?bulan=${bulan}&tahun=${tahun}`, {
                    // Tambahkan header ini untuk mengirim token
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfHash
                    }
                });

                // Periksa jika respons tidak OK (misal: error 403, 500)
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                console.log("===== INFORMASI DEBUG DARI SERVER =====");
                console.log("Data yang diterima:", data);
                console.log("=====================================");

                saldoBulanLaluInput.value = formatRupiah(data.saldo);
                calculateSummary();
            } catch (error) {
                saldoBulanLaluInput.value = "Gagal memuat";
                console.error("Error saat mengambil saldo:", error);
            }
        }

        // ===================================================================================
        // FUNGSI-FUNGSI BANTUAN (HELPERS)
        // ===================================================================================

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function unformatRupiah(rupiahStr) {
            if (typeof rupiahStr !== 'string' || rupiahStr === null) {
                return 0;
            }
            const numericString = rupiahStr.replace(/[^0-9]/g, '');
            if (numericString === '') {
                return 0;
            }
            return parseInt(numericString, 10);
        }

        function initIMask() {
            document.querySelectorAll('.input-rupiah:not(.imask-initialized)').forEach(function(input) {
                IMask(input, {
                    mask: Number,
                    scale: 0,
                    signed: false,
                    thousandsSeparator: '.',
                    min: 0,
                    max: 999999999999 // 12 digit
                });
                input.removeAttribute('maxlength');
                input.classList.add('imask-initialized');
            });
        }

        // ===================================================================================
        // FUNGSI DINAMIS UNTUK MENAMBAH/MENGHAPUS BARIS FORM
        // ===================================================================================

        function addPendapatanRow() {
            const div = document.createElement('div');
            div.className = 'row g-3 mb-3 align-items-center dynamic-row';
            div.innerHTML = `
            <div class="col-md-6">
                <select name="pendapatan[${pendapatanIndex}][id]" class="form-select" required>
                    <option value="">-- Pilih Pendapatan --</option>
                    ${masterPendapatan.map(p => `<option value="${p.id}">${p.nama_pendapatan}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="pendapatan[${pendapatanIndex}][jumlah]" class="form-control input-rupiah" placeholder="Jumlah (Rp)" required>
            </div>
            <div class="col-md-1 text-center">
                <i class="fas fa-trash-alt remove-btn" title="Hapus baris ini"></i>
            </div>
        `;
            pendapatanWrapper.appendChild(div);
            pendapatanIndex++;
            initIMask();
        }

        function addPengeluaranRow() {
            const div = document.createElement('div');
            div.className = 'row g-3 mb-3 dynamic-row';
            div.innerHTML = `
            <div class="col-12">
                <input type="text" name="pengeluaran[${pengeluaranIndex}][deskripsi]" class="form-control" placeholder="Deskripsi Pengeluaran" required>
            </div>
            <div class="col-md-6">
                <select name="pengeluaran[${pengeluaranIndex}][kategori_id]" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    ${masterKategori.map(k => `<option value="${k.id}">${k.nama_kategori} (${k.persentase}%)</option>`).join('')}
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="pengeluaran[${pengeluaranIndex}][jumlah]" class="form-control input-rupiah" placeholder="Jumlah (Rp)" required>
            </div>
            <div class="col-md-1 text-center align-self-center">
                <i class="fas fa-trash-alt remove-btn" title="Hapus baris ini"></i>
            </div>
        `;
            pengeluaranWrapper.appendChild(div);
            pengeluaranIndex++;
            initIMask();
        }

        addPendapatanBtn.addEventListener('click', addPendapatanRow);
        addPengeluaranBtn.addEventListener('click', addPengeluaranRow);

        bkuForm.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-btn')) {
                e.target.closest('.dynamic-row').remove();
                calculateSummary();
            }
        });

        // ===================================================================================
        // FUNGSI UTAMA UNTUK KALKULASI REAL-TIME
        // ===================================================================================

        function calculateSummary() {
            const saldoLalu = unformatRupiah(document.getElementById('saldo-bulan-lalu').value);
            let penghasilanBulanIni = 0;
            document.querySelectorAll('#pendapatan-wrapper .dynamic-row .input-rupiah').forEach(input => {
                penghasilanBulanIni += unformatRupiah(input.value);
            });
            document.getElementById('penghasilan-bulan-ini').value = formatRupiah(penghasilanBulanIni);
            let totalPendapatan = saldoLalu + penghasilanBulanIni;
            let totalPengeluaran = 0;
            let pengeluaranPerKategori = {};
            masterKategori.forEach(k => {
                pengeluaranPerKategori[k.id] = 0;
            });
            document.querySelectorAll('#pengeluaran-wrapper .dynamic-row').forEach(row => {
                const jumlah = unformatRupiah(row.querySelector('.input-rupiah').value);
                const kategoriId = row.querySelector('select').value;
                totalPengeluaran += jumlah;
                if (kategoriId && pengeluaranPerKategori.hasOwnProperty(kategoriId)) {
                    pengeluaranPerKategori[kategoriId] += jumlah;
                }
            });
            let saldoAkhir = totalPendapatan - totalPengeluaran;
            document.getElementById('summary-pendapatan').textContent = formatRupiah(totalPendapatan);
            document.getElementById('summary-pengeluaran').textContent = formatRupiah(totalPengeluaran);
            const saldoElement = document.getElementById('summary-saldo');
            saldoElement.textContent = formatRupiah(saldoAkhir);
            saldoElement.classList.toggle('text-danger', saldoAkhir < 0);
            const alokasiWrapper = document.getElementById('alokasi-dana-wrapper');
            if (totalPendapatan > 0) {
                let alokasiHTML = `
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kategori</th>
                            <th>Alokasi Dana (% dari Pendapatan)</th>
                            <th>Realisasi Pengeluaran</th>
                            <th>Sisa Alokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                masterKategori.forEach(k => {
                    const alokasi = totalPendapatan * (k.persentase / 100);
                    const realisasi = pengeluaranPerKategori[k.id] || 0;
                    const sisa = alokasi - realisasi;
                    alokasiHTML += `
                        <tr>
                            <td>${k.nama_kategori}</td>
                            <td>${formatRupiah(alokasi)}</td>
                            <td>${formatRupiah(realisasi)}</td>
                            <td class="${sisa < 0 ? 'text-danger fw-bold' : ''}">${formatRupiah(sisa)}</td>
                        </tr>
                    `;
                });
                alokasiHTML += `</tbody></table>`;
                alokasiWrapper.innerHTML = alokasiHTML;
            } else {
                alokasiWrapper.innerHTML = '<p class="text-muted">Tabel alokasi akan muncul setelah Anda memasukkan pendapatan.</p>';
            }
        }

        bulanDropdown.addEventListener('change', fetchSaldoBulanLalu);
        tahunDropdown.addEventListener('change', fetchSaldoBulanLalu);

        bkuForm.addEventListener('input', calculateSummary);

        // ===================================================================================
        // INISIALISASI AWAL
        // ===================================================================================
        addPendapatanRow();
        fetchSaldoBulanLalu(); // Panggil sekali saat halaman dimuat
    });
</script>
<?= $this->endSection(); ?>