<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pilih Periode Laporan</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('/bku-tahunan'); ?>" method="get">
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
                        <button type="submit" class="btn btn-info"><i class="fas fa-eye me-2"></i>Tampilkan Laporan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($hasil)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Hasil Laporan Tahun <?= esc($tahunDipilih); ?></h6>
                <div>
                    <a href="<?= site_url('/bku-tahunan/cetak-pdf/' . $tahunDipilih); ?>" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf me-1"></i> Cetak PDF</a>
                    <a href="<?= site_url('/bku-tahunan/cetak-excel/' . $tahunDipilih); ?>" class="btn btn-sm btn-success"><i class="fas fa-file-excel me-1"></i> Ekspor Excel</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-left-success py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= 'Rp ' . number_format($hasil['totalPendapatan'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-danger py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= 'Rp ' . number_format($hasil['totalPengeluaran'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-primary py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sisa Saldo Tahun Ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= 'Rp ' . number_format($hasil['saldoAkhirTahun'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <h5>Rincian Pengeluaran per Kategori</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Kategori Pengeluaran</th>
                                <th class="text-end">Total Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($hasil['pengeluaranPerKategori'])): ?>
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada data pengeluaran.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($hasil['pengeluaranPerKategori'] as $row): ?>
                                    <tr>
                                        <td><?= esc($row['nama_kategori']); ?></td>
                                        <td class="text-end"><?= 'Rp ' . number_format($row['total_per_kategori'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th class="text-end">TOTAL KESELURUHAN PENGELUARAN</th>
                                <th class="text-end"><?= 'Rp ' . number_format($hasil['totalPengeluaran'], 0, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection(); ?>