<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Bagian Header Halaman -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Laporan BKU</h1>
            <p class="mb-0 text-muted">Laporan Periode: <strong><?= date('F Y', mktime(0, 0, 0, $laporan['bulan'], 1)); ?></strong></p>
        </div>
        <div>
            <div class="btn-group">
                <a href="<?= site_url('/bku-bulanan/' . $laporan['id'] . '/edit'); ?>" class="btn btn-sm btn-success shadow-sm me-2">
                    <i class="fas fa-edit fa-sm text-white-50 me-2"></i>Edit Laporan
                </a>

                <button type="button" class="btn btn-sm btn-primary shadow-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-print fa-sm text-white-50 me-2"></i>Cetak Laporan
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="<?= site_url('/bku-bulanan/cetak-pdf/' . $laporan['id']); ?>" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i>Cetak PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('/bku-bulanan/cetak-excel/' . $laporan['id']); ?>">
                            <i class="fas fa-file-excel me-2"></i>Unduh Excel
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= site_url('/bku-bulanan'); ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50 me-2"></i>Kembali</a>
        </div>
    </div>

    <!-- Kartu Ringkasan Utama -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= 'Rp ' . number_format($laporan['total_pendapatan'], 0, ',', '.'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= 'Rp ' . number_format($laporan['total_pengeluaran'], 0, ',', '.'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Akhir</div>
                            <div class="h5 mb-0 font-weight-bold <?= ($laporan['saldo_akhir'] < 0) ? 'text-danger' : 'text-gray-800'; ?>">
                                <?= 'Rp ' . number_format($laporan['saldo_akhir'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Rincian Alokasi Dana (Tersimpan) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-chart-pie me-2"></i>Rincian Alokasi & Realisasi Dana (Data Tersimpan)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Kategori Pengeluaran</th>
                            <th class="text-center">Persentase</th>
                            <th class="text-end">Alokasi Dana</th>
                            <th class="text-end">Realisasi Pengeluaran</th>
                            <th class="text-end">Sisa Alokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rincianAlokasi)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Data alokasi tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rincianAlokasi as $a): ?>
                                <tr>
                                    <td><?= esc($a['nama_kategori']); ?></td>
                                    <td class="text-center"><?= number_format($a['persentase_saat_itu'], 2); ?>%</td>
                                    <td class="text-end"><?= 'Rp ' . number_format($a['jumlah_alokasi'], 0, ',', '.'); ?></td>
                                    <td class="text-end"><?= 'Rp ' . number_format($a['jumlah_realisasi'], 0, ',', '.'); ?></td>
                                    <td class="text-end fw-bold <?= ($a['sisa_alokasi'] < 0) ? 'text-danger' : ''; ?>">
                                        <?= 'Rp ' . number_format($a['sisa_alokasi'], 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Rincian Transaksi -->
    <div class="row">
        <!-- Kolom Rincian Pendapatan -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-clipboard-list me-2"></i>Rincian Pendapatan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Jenis Pendapatan</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- [PERUBAHAN] Menampilkan Sisa Saldo Bulan Lalu -->
                                <tr class="table-info">
                                    <td><strong>Sisa Saldo Bulan Lalu</strong></td>
                                    <td class="text-end"><strong><?= 'Rp ' . number_format($laporan['saldo_bulan_lalu'], 0, ',', '.'); ?></strong></td>
                                </tr>
                                <?php if (empty($rincianPendapatan)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center fst-italic">Tidak ada pendapatan bulan ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rincianPendapatan as $p): ?>
                                        <tr>
                                            <td><?= esc($p['nama_pendapatan']); ?></td>
                                            <td class="text-end"><?= 'Rp ' . number_format($p['jumlah'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th class="text-end">TOTAL PENDAPATAN</th>
                                    <th class="text-end"><?= 'Rp ' . number_format($laporan['total_pendapatan'], 0, ',', '.'); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Rincian Pengeluaran -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-clipboard-list me-2"></i>Rincian Pengeluaran</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Deskripsi</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rincianPengeluaran)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data pengeluaran.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rincianPengeluaran as $p): ?>
                                        <tr>
                                            <td><?= esc($p['deskripsi_pengeluaran']); ?></td>
                                            <td><span class="badge bg-secondary"><?= esc($p['nama_kategori']); ?></span></td>
                                            <td class="text-end"><?= 'Rp ' . number_format($p['jumlah'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">TOTAL PENGELUARAN</th>
                                    <th class="text-end"><?= 'Rp ' . number_format($laporan['total_pengeluaran'], 0, ',', '.'); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>