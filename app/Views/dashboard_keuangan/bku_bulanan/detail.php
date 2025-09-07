<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    /* Style untuk stat card agar lebih interaktif */
    .stat-card-detail {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .stat-card-detail:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    /* Style untuk tabel yang diubah menjadi Card di mobile */
    @media (max-width: 767.98px) {
        .table-responsive-stack thead {
            display: none;
        }

        .table-responsive-stack table,
        .table-responsive-stack tbody,
        .table-responsive-stack tr,
        .table-responsive-stack td,
        .table-responsive-stack th {
            display: block;
            width: 100%;
        }

        .table-responsive-stack tr {
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0;
        }

        .table-responsive-stack td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            padding: 0.75rem 1rem;
            text-align: right;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-responsive-stack tr td:last-child {
            border-bottom: none;
        }

        .table-responsive-stack td::before {
            content: attr(data-label);
            font-weight: bold;
            text-align: left;
            margin-right: 1rem;
            color: #6c757d;
        }
    }
</style>

<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Detail Laporan BKU</h1>
            <p class="mb-2 mb-md-0 text-muted">Laporan Periode: <strong><?= date('F Y', mktime(0, 0, 0, $laporan['bulan'], 1)); ?></strong></p>
        </div>
        <div class="btn-toolbar" role="toolbar">
            <a href="<?= site_url('/bku-bulanan'); ?>" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-arrow-left fa-sm me-2"></i>Kembali</a>
            <div class="btn-group me-2" role="group">
                <a href="<?= site_url('/bku-bulanan/' . $laporan['id'] . '/edit'); ?>" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-edit fa-sm text-white-50 me-2"></i>Edit
                </a>
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-primary shadow-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-print fa-sm text-white-50 me-2"></i>Cetak
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= site_url('/bku-bulanan/cetak-pdf/' . $laporan['id']); ?>" target="_blank"><i class="fas fa-file-pdf fa-fw me-2"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="<?= site_url('/bku-bulanan/cetak-excel/' . $laporan['id']); ?>"><i class="fas fa-file-excel fa-fw me-2"></i>Excel</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card-detail">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= 'Rp ' . number_format($laporan['total_pendapatan'], 0, ',', '.'); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-arrow-down fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 stat-card-detail">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= 'Rp ' . number_format($laporan['total_pengeluaran'], 0, ',', '.'); ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-arrow-up fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card-detail">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Akhir</div>
                            <div class="h5 mb-0 font-weight-bold <?= ($laporan['saldo_akhir'] < 0) ? 'text-danger' : 'text-gray-800'; ?>">
                                <?= 'Rp ' . number_format($laporan['saldo_akhir'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="alokasi-tab" data-bs-toggle="tab" data-bs-target="#alokasi-pane" type="button">Rincian Alokasi Dana</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pendapatan-tab" data-bs-toggle="tab" data-bs-target="#pendapatan-pane" type="button">Rincian Pendapatan</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pengeluaran-tab" data-bs-toggle="tab" data-bs-target="#pengeluaran-pane" type="button">Rincian Pengeluaran</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="alokasi-pane" role="tabpanel">
                    <div class="table-responsive-stack">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kategori Pengeluaran</th>
                                    <th class="text-center">Persentase</th>
                                    <th class="text-end">Alokasi</th>
                                    <th class="text-end">Realisasi</th>
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
                                            <td data-label="Kategori"><?= esc($a['nama_kategori']); ?></td>
                                            <td data-label="Persentase" class="text-center"><?= number_format($a['persentase_saat_itu'], 2); ?>%</td>
                                            <td data-label="Alokasi" class="text-end"><?= 'Rp ' . number_format($a['jumlah_alokasi'], 0, ',', '.'); ?></td>
                                            <td data-label="Realisasi" class="text-end"><?= 'Rp ' . number_format($a['jumlah_realisasi'], 0, ',', '.'); ?></td>
                                            <td data-label="Sisa Alokasi" class="text-end fw-bold <?= ($a['sisa_alokasi'] < 0) ? 'text-danger' : ''; ?>"><?= 'Rp ' . number_format($a['sisa_alokasi'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="pendapatan-pane" role="tabpanel">
                    <div class="table-responsive-stack">
                        <table class="table table-striped table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Jenis Pendapatan</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-info">
                                    <td data-label="Jenis"><strong>Sisa Saldo Bulan Lalu</strong></td>
                                    <td data-label="Jumlah" class="text-end"><strong><?= 'Rp ' . number_format($laporan['saldo_bulan_lalu'], 0, ',', '.'); ?></strong></td>
                                </tr>
                                <?php if (empty($rincianPendapatan)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center fst-italic">Tidak ada pendapatan bulan ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rincianPendapatan as $p): ?>
                                        <tr>
                                            <td data-label="Jenis"><?= esc($p['nama_pendapatan']); ?></td>
                                            <td data-label="Jumlah" class="text-end"><?= 'Rp ' . number_format($p['jumlah'], 0, ',', '.'); ?></td>
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

                <div class="tab-pane fade" id="pengeluaran-pane" role="tabpanel">
                    <div class="table-responsive-stack">
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
                                            <td data-label="Deskripsi"><?= esc($p['deskripsi_pengeluaran']); ?></td>
                                            <td data-label="Kategori"><span class="badge bg-secondary"><?= esc($p['nama_kategori']); ?></span></td>
                                            <td data-label="Jumlah" class="text-end"><?= 'Rp ' . number_format($p['jumlah'], 0, ',', '.'); ?></td>
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