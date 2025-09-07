<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    /* Membuat kartu ringkasan menempel di atas saat di-scroll */
    .sticky-summary {
        position: sticky;
        top: 1rem;
        /* Sesuaikan jarak dari atas */
        z-index: 1020;
        /* Pastikan di atas konten lain */
    }

    .summary-card h6 {
        font-size: 0.8rem;
        font-weight: 700;
        color: #858796;
    }

    .summary-card h4 {
        font-weight: 700;
    }

    /* Style untuk baris input dinamis yang lebih rapi */
    .dynamic-row-item {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 0.75rem;
    }

    .remove-btn {
        font-size: 1.2rem;
        cursor: pointer;
        color: var(--bs-danger);
        transition: transform 0.2s, color 0.2s;
    }

    .remove-btn:hover {
        transform: scale(1.2);
        color: #a01c26;
    }

    /* Style untuk tabel alokasi dana */
    .alokasi-table {
        font-size: 0.9rem;
    }

    .alokasi-table th {
        white-space: nowrap;
    }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
            <p class="mb-0 text-muted">Anda sedang mengedit laporan untuk periode: <strong><?= date('F Y', mktime(0, 0, 0, $laporan['bulan'], 1)); ?></strong></p>
        </div>
    </div>

    <form action="<?= site_url('bku-bulanan/' . $laporan['id']); ?>" method="post" id="bku-form">
        <?= csrf_field(); ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="m-0"><i class="fas fa-plus-circle me-2"></i>PENDAPATAN</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <label class="form-label-plaintext fw-bold">Sisa Saldo Bulan Lalu</label>
                                    <input type="text" id="saldo-bulan-lalu" class="form-control-plaintext text-end fw-bold" value="<?= number_format($laporan['saldo_bulan_lalu'], 0, ',', '.'); ?>" readonly>
                                </div>
                                <hr class="mt-0">
                                <div id="pendapatan-wrapper" class="d-flex flex-column gap-3">
                                </div>
                                <button type="button" class="btn btn-outline-success w-100 mt-3" id="add-pendapatan">
                                    <i class="fas fa-plus"></i> Tambah Rincian Pendapatan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-danger text-white">
                                <h5 class="m-0"><i class="fas fa-minus-circle me-2"></i>PENGELUARAN</h5>
                            </div>
                            <div class="card-body">
                                <div id="pengeluaran-wrapper" class="d-flex flex-column gap-3">
                                </div>
                                <button type="button" class="btn btn-outline-danger w-100 mt-3" id="add-pengeluaran">
                                    <i class="fas fa-plus"></i> Tambah Rincian Pengeluaran
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-summary">
                    <div class="card shadow-sm mb-4 summary-card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calculator me-2"></i>Ringkasan Keuangan (Real-time)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-12 border-bottom pb-3 mb-3">
                                    <h6><i class="fas fa-arrow-down text-success me-1"></i>TOTAL PENDAPATAN</h6>
                                    <h4 id="summary-pendapatan" class="text-success">Rp 0</h4>
                                </div>
                                <div class="col-12 border-bottom pb-3 mb-3">
                                    <h6><i class="fas fa-arrow-up text-danger me-1"></i>TOTAL PENGELUARAN</h6>
                                    <h4 id="summary-pengeluaran" class="text-danger">Rp 0</h4>
                                </div>
                                <div class="col-12">
                                    <h6><i class="fas fa-wallet text-primary me-1"></i>SALDO AKHIR</h6>
                                    <h4 id="summary-saldo" class="text-primary">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-chart-pie me-2"></i>Alokasi & Realisasi Dana</h6>
                        </div>
                        <div class="card-body p-2" id="alokasi-dana-wrapper">
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i>Update Laporan</button>
                            <a href="<?= site_url('/bku-bulanan/detail/' . $laporan['id']); ?>" class="btn btn-secondary">Batal</a>
                        </div>
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
        const bkuForm = document.getElementById('bku-form');

        // --- Fungsi helpers ---
        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        function unformatRupiah(rupiahStr) {
            if (typeof rupiahStr !== 'string' || rupiahStr === null) return 0;
            const n = rupiahStr.replace(/[^0-9]/g, '');
            return n === '' ? 0 : parseInt(n, 10);
        }

        function initMask() {
            document.querySelectorAll('.input-rupiah:not(.mask-initialized)').forEach(function(input) {
                IMask(input, {
                    mask: Number,
                    scale: 0,
                    signed: false,
                    thousandsSeparator: '.'
                });
                input.classList.add('mask-initialized');
            });
        }

        // --- FUNGSI DINAMIS (DISEMPURNAKAN) ---
        function addPendapatanRow(data = null) {
            const div = document.createElement('div');
            div.className = 'dynamic-row-item';
            let options = masterPendapatan.map(p => `<option value="${p.id}" ${data && data.master_pendapatan_id == p.id ? 'selected' : ''}>${p.nama_pendapatan}</option>`).join('');
            div.innerHTML = `
            <div class="row g-2">
                <div class="col-10">
                    <select name="pendapatan[${pendapatanIndex}][id]" class="form-select form-select-sm" required><option value="">-- Pilih Pendapatan --</option>${options}</select>
                </div>
                <div class="col-2 text-end">
                    <i class="fas fa-trash-alt remove-btn" title="Hapus baris ini"></i>
                </div>
                <div class="col-12">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="pendapatan[${pendapatanIndex}][jumlah]" class="form-control input-rupiah" placeholder="Jumlah" required value="${data ? data.jumlah : ''}">
                    </div>
                </div>
            </div>`;
            pendapatanWrapper.appendChild(div);
            pendapatanIndex++;
            initMask();
        }

        function addPengeluaranRow(data = null) {
            const div = document.createElement('div');
            div.className = 'dynamic-row-item';
            let options = masterKategori.map(k => `<option value="${k.id}" ${data && data.master_kategori_id == k.id ? 'selected' : ''}>${k.nama_kategori} (${k.persentase}%)</option>`).join('');
            div.innerHTML = `
            <div class="row g-2">
                <div class="col-12">
                    <input type="text" name="pengeluaran[${pengeluaranIndex}][deskripsi]" class="form-control form-control-sm" placeholder="Deskripsi Pengeluaran" required value="${data ? data.deskripsi_pengeluaran : ''}">
                </div>
                <div class="col-10">
                    <select name="pengeluaran[${pengeluaranIndex}][kategori_id]" class="form-select form-select-sm" required><option value="">-- Pilih Kategori --</option>${options}</select>
                </div>
                 <div class="col-2 text-end">
                    <i class="fas fa-trash-alt remove-btn" title="Hapus baris ini"></i>
                </div>
                <div class="col-12">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="pengeluaran[${pengeluaranIndex}][jumlah]" class="form-control input-rupiah" placeholder="Jumlah" required value="${data ? data.jumlah : ''}">
                    </div>
                </div>
            </div>`;
            pengeluaranWrapper.appendChild(div);
            pengeluaranIndex++;
            initMask();
        }

        // --- FUNGSI KALKULASI UTAMA (TETAP SAMA, HANYA FORMAT RUPIAH DIPERBAIKI) ---
        function calculateSummary() {
            const saldoLalu = unformatRupiah(document.getElementById('saldo-bulan-lalu').value);
            let penghasilanBulanIni = 0;
            document.querySelectorAll('#pendapatan-wrapper .input-rupiah').forEach(input => {
                penghasilanBulanIni += unformatRupiah(input.value);
            });

            let totalPendapatan = saldoLalu + penghasilanBulanIni;

            let totalPengeluaran = 0;
            let pengeluaranPerKategori = {};
            masterKategori.forEach(k => {
                pengeluaranPerKategori[k.id] = 0;
            });
            document.querySelectorAll('#pengeluaran-wrapper .dynamic-row-item').forEach(row => {
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
                let alokasiHTML = `<div class="table-responsive"><table class="table table-sm alokasi-table">`;
                masterKategori.forEach(k => {
                    const alokasi = totalPendapatan * (k.persentase / 100);
                    const realisasi = pengeluaranPerKategori[k.id] || 0;
                    const sisa = alokasi - realisasi;
                    alokasiHTML += `
                <tr>
                    <td>${k.nama_kategori} (${k.persentase}%)</td>
                    <td class="text-end ${sisa < 0 ? 'text-danger fw-bold' : ''}">${formatRupiah(sisa).replace('Rp ','')}</td>
                </tr>`;
                });
                alokasiHTML += `</table></div>`;
                alokasiWrapper.innerHTML = alokasiHTML;
            } else {
                alokasiWrapper.innerHTML = '<p class="text-muted text-center small p-3">Tabel sisa alokasi akan muncul di sini.</p>';
            }
        }

        // --- EVENT LISTENERS ---
        document.getElementById('add-pendapatan').addEventListener('click', () => addPendapatanRow());
        document.getElementById('add-pengeluaran').addEventListener('click', () => addPengeluaranRow());
        bkuForm.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-btn')) {
                e.target.closest('.dynamic-row-item').remove();
                calculateSummary();
            }
        });
        bkuForm.addEventListener('input', calculateSummary);

        // --- INISIALISASI AWAL ---
        rincianPendapatan.forEach(item => addPendapatanRow(item));
        rincianPengeluaran.forEach(item => addPengeluaranRow(item));
        if (rincianPendapatan.length === 0) {
            addPendapatanRow();
        }
        if (rincianPengeluaran.length === 0) {
            addPengeluaranRow();
        }
        calculateSummary();
    });
</script>

<?= $this->endSection(); ?>