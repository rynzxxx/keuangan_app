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
    <p class="mb-4">Anda sedang mengedit laporan untuk periode: <strong><?= date('F Y', mktime(0, 0, 0, $laporan['bulan'], 1)); ?></strong></p>

    <!-- Bagian Notifikasi dan Kartu Ringkasan -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error'); ?>
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

    <!-- Form Utama -->
    <form action="<?= site_url('bku-bulanan/' . $laporan['id']); ?>" method="post" id="bku-form">
        <?= csrf_field(); ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="card shadow-sm mb-4">
            <div class="card-body text-end">
                <a href="<?= site_url('/bku-bulanan/detail/' . $laporan['id']); ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i> Update Laporan</button>
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
                            <!-- [PERUBAHAN] Menambahkan field permanen -->
                            <div class="row g-3 mb-3 align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label-plaintext fw-bold">Sisa Saldo Bulan Lalu</label>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" id="saldo-bulan-lalu" class="form-control-plaintext text-end fw-bold" value="<?= number_format($laporan['saldo_bulan_lalu'], 0, ',', '.'); ?>" readonly>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
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
                            <!-- Baris pengeluaran akan ditambahkan di sini oleh JS -->
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

        // --- SETUP AWAL ---
        const masterPendapatan = <?= json_encode($master_pendapatan); ?>;
        const masterKategori = <?= json_encode($master_kategori); ?>;
        const rincianPendapatan = <?= json_encode($rincianPendapatan); ?>;
        const rincianPengeluaran = <?= json_encode($rincianPengeluaran); ?>;

        let pendapatanIndex = 0;
        let pengeluaranIndex = 0;

        const pendapatanWrapper = document.getElementById('pendapatan-wrapper');
        const pengeluaranWrapper = document.getElementById('pengeluaran-wrapper');
        const addPendapatanBtn = document.getElementById('add-pendapatan');
        const addPengeluaranBtn = document.getElementById('add-pengeluaran');
        const bkuForm = document.getElementById('bku-form');

        // --- Fungsi helpers ---
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function unformatRupiah(rupiahStr) {
            if (typeof rupiahStr !== 'string' || rupiahStr === null) return 0;
            const numericString = rupiahStr.replace(/[^0-9]/g, '');
            if (numericString === '') return 0;
            return parseInt(numericString, 10);
        }

        function initMask() {
            document.querySelectorAll('.input-rupiah:not(.mask-initialized)').forEach(function(input) {
                IMask(input, {
                    mask: Number,
                    scale: 0,
                    signed: false,
                    thousandsSeparator: '.'
                });
                input.removeAttribute('maxlength');
                input.classList.add('mask-initialized');
            });
        }

        // --- FUNGSI DINAMIS ---
        function addPendapatanRow(data = null) {
            const div = document.createElement('div');
            div.className = 'row g-3 mb-3 align-items-center dynamic-row';

            let options = masterPendapatan.map(p =>
                `<option value="${p.id}" ${data && data.master_pendapatan_id == p.id ? 'selected' : ''}>${p.nama_pendapatan}</option>`
            ).join('');

            div.innerHTML = `
            <div class="col-md-6">
                <select name="pendapatan[${pendapatanIndex}][id]" class="form-select" required>
                    <option value="">-- Pilih Pendapatan --</option>
                    ${options}
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="pendapatan[${pendapatanIndex}][jumlah]" class="form-control input-rupiah" placeholder="Jumlah" required value="${data ? data.jumlah : ''}">
            </div>
            <div class="col-md-1 text-center">
                <i class="fas fa-trash-alt remove-btn" title="Hapus baris ini"></i>
            </div>
        `;
            pendapatanWrapper.appendChild(div);
            pendapatanIndex++;
            initMask();
        }

        function addPengeluaranRow(data = null) {
            const div = document.createElement('div');
            div.className = 'row g-3 mb-3 dynamic-row';

            let options = masterKategori.map(k =>
                `<option value="${k.id}" ${data && data.master_kategori_id == k.id ? 'selected' : ''}>${k.nama_kategori} (${k.persentase}%)</option>`
            ).join('');

            div.innerHTML = `
            <div class="col-12">
                <input type="text" name="pengeluaran[${pengeluaranIndex}][deskripsi]" class="form-control" placeholder="Deskripsi Pengeluaran" required value="${data ? data.deskripsi_pengeluaran : ''}">
            </div>
            <div class="col-md-6">
                <select name="pengeluaran[${pengeluaranIndex}][kategori_id]" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    ${options}
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="pengeluaran[${pengeluaranIndex}][jumlah]" class="form-control input-rupiah" placeholder="Jumlah" required value="${data ? data.jumlah : ''}">
            </div>
            <div class="col-md-1 text-center align-self-center">
                <i class="fas fa-trash-alt remove-btn" title="Hapus baris ini"></i>
            </div>
        `;
            pengeluaranWrapper.appendChild(div);
            pengeluaranIndex++;
            initMask();
        }

        addPendapatanBtn.addEventListener('click', () => addPendapatanRow());
        addPengeluaranBtn.addEventListener('click', () => addPengeluaranRow());

        bkuForm.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-btn')) {
                e.target.closest('.dynamic-row').remove();
                calculateSummary();
            }
        });

        // [PERBAIKAN] Fungsi calculateSummary ditambahkan console.log untuk debug
        function calculateSummary() {
            console.clear(); // Bersihkan console setiap kali perhitungan ulang
            console.log("===== MEMULAI PERHITUNGAN =====");

            const saldoLalu = unformatRupiah(document.getElementById('saldo-bulan-lalu').value);
            console.log("Sisa Saldo Bulan Lalu (dibaca dari form):", saldoLalu);

            let penghasilanBulanIni = 0;
            console.log("--- Rincian Penghasilan Bulan Ini ---");
            document.querySelectorAll('#pendapatan-wrapper .dynamic-row .input-rupiah').forEach((input, index) => {
                const nilai = unformatRupiah(input.value);
                console.log(`Baris #${index + 1}:`, nilai);
                penghasilanBulanIni += nilai;
            });
            console.log("-------------------------------------");
            document.getElementById('penghasilan-bulan-ini').value = formatRupiah(penghasilanBulanIni);
            console.log("Total Penghasilan Bulan Ini:", penghasilanBulanIni);

            let totalPendapatan = saldoLalu + penghasilanBulanIni;
            console.log("TOTAL PENDAPATAN (Saldo Lalu + Penghasilan Bulan Ini):", totalPendapatan);
            console.log("=================================");

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
        bkuForm.addEventListener('input', calculateSummary);

        // --- INISIALISASI AWAL ---
        rincianPendapatan.forEach(item => addPendapatanRow(item));
        rincianPengeluaran.forEach(item => addPengeluaranRow(item));

        if (rincianPendapatan.length === 0) {
            addPendapatanRow();
        }

        calculateSummary();
    });
</script>
<?= $this->endSection(); ?>