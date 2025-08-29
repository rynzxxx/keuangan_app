<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <!-- Notifikasi -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?= site_url('/bku-bulanan/new'); ?>" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Buat Laporan Baru</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th class="text-center">Periode</th>
                            <th class="text-end">Total Pendapatan</th>
                            <th class="text-end">Total Pengeluaran</th>
                            <th class="text-end">Saldo Akhir</th>
                            <th class="text-center" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data laporan.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($laporan as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td class="text-center">
                                        <strong><?= date('F', mktime(0, 0, 0, $row['bulan'], 10)); ?> <?= $row['tahun']; ?></strong>
                                    </td>
                                    <td class="text-end"><?= 'Rp ' . number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                                    <td class="text-end"><?= 'Rp ' . number_format($row['total_pengeluaran'], 0, ',', '.'); ?></td>
                                    <td class="text-end fw-bold <?= ($row['saldo_akhir'] < 0) ? 'text-danger' : 'text-success'; ?>">
                                        <?= 'Rp ' . number_format($row['saldo_akhir'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('/bku-bulanan/detail/' . $row['id']); ?>" class="btn btn-info btn-sm" title="Lihat Detail"><i class="fas fa-eye"></i></a>

                                        <!-- TAMBAHKAN TOMBOL INI -->
                                        <a href="<?= site_url('/bku-bulanan/' . $row['id'] . '/edit'); ?>" class="btn btn-warning btn-sm" title="Edit Laporan"><i class="fas fa-edit"></i></a>

                                        <form action="<?= site_url('/bku-bulanan/' . $row['id']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan periode ini? Semua data rinciannya juga akan terhapus permanen.')"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>