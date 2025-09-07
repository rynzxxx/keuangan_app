<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<style>
    /* --- [TAMBAHAN] Style untuk Tabel Laporan BKU Bulanan Responsif --- */

    /* Aktifkan hanya untuk layar medium ke bawah (di bawah 992px) */
    @media (max-width: 991.98px) {
        .table-responsive-stack thead {
            /* Sembunyikan header tabel asli di mobile */
            display: none;
        }

        .table-responsive-stack table,
        .table-responsive-stack tbody,
        .table-responsive-stack tr,
        .table-responsive-stack td {
            display: block;
            width: 100%;
        }

        .table-responsive-stack tr {
            /* Setiap baris menjadi sebuah card */
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            overflow: hidden;
            /* Penting untuk radius di header card */
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
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

        /* --- Style Khusus untuk Membuat Tampilan Card Lebih Menarik --- */

        /* Sembunyikan kolom "No" di mobile karena sudah tidak relevan */
        .table-responsive-stack td[data-label="No"] {
            display: none;
        }

        /* Jadikan sel "Periode" sebagai header kartu */
        .table-responsive-stack td[data-label="Periode"] {
            background-color: #f8f9fc;
            font-size: 1.1rem;
            padding: 1rem;
            justify-content: center;
        }

        .table-responsive-stack td[data-label="Periode"]::before {
            display: none;
            /* Sembunyikan label "Periode:" karena sudah jelas */
        }

        /* Pastikan tombol aksi berada di tengah */
        .table-responsive-stack td[data-label="Aksi"] {
            justify-content: center;
        }

        .table-responsive-stack td[data-label="Aksi"] .btn-group {
            width: 100%;
            display: flex;
        }

        .table-responsive-stack td[data-label="Aksi"] .btn-group>* {
            flex: 1 1 auto;
            /* Buat tombol memenuhi space */
        }
    }
</style>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

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
            <div class="table-responsive-stack">
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
                                    <td data-label="No" class="text-center"><?= $no++; ?></td>
                                    <td data-label="Periode" class="text-center">
                                        <strong><?= date('F', mktime(0, 0, 0, $row['bulan'], 10)); ?> <?= $row['tahun']; ?></strong>
                                    </td>
                                    <td data-label="Total Pendapatan" class="text-end"><?= 'Rp ' . number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                                    <td data-label="Total Pengeluaran" class="text-end"><?= 'Rp ' . number_format($row['total_pengeluaran'], 0, ',', '.'); ?></td>
                                    <td data-label="Saldo Akhir" class="text-end fw-bold <?= ($row['saldo_akhir'] < 0) ? 'text-danger' : 'text-success'; ?>">
                                        <?= 'Rp ' . number_format($row['saldo_akhir'], 0, ',', '.'); ?>
                                    </td>
                                    <td data-label="Aksi" class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?= site_url('/bku-bulanan/detail/' . $row['id']); ?>" class="btn btn-info btn-sm" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                            <a href="<?= site_url('/bku-bulanan/' . $row['id'] . '/edit'); ?>" class="btn btn-warning btn-sm" title="Edit Laporan"><i class="fas fa-edit"></i></a>
                                            <form action="<?= site_url('/bku-bulanan/' . $row['id']); ?>" method="post" class="d-inline">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan periode ini? Semua data rinciannya juga akan terhapus permanen.')"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </div>
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