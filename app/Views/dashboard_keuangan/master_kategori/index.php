<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    /* Style untuk kartu persentase yang lebih menarik */
    .percentage-summary-card .display-4 {
        font-weight: 700;
        color: #4e73df;
        font-size: 3rem;
    }

    .percentage-summary-card .progress {
        height: 12px;
        border-radius: 12px;
    }

    .percentage-summary-card .display-4 {
        font-size: 3rem;
        /* Sedikit perkecil angka di mobile agar lebih pas */
    }

    #notification-text .badge {
        white-space: normal;
        /* Izinkan teks untuk wrap/melipat ke baris baru */
        line-height: 1.4;
        /* Atur jarak antar baris agar mudah dibaca */
        text-align: center;
        /* Pastikan teks tetap di tengah saat wrap */
        display: inline-block;
        /* Pastikan badge bisa di-styling dengan benar */
        padding: 0.5rem 0.75rem;
        /* Beri sedikit padding agar tidak terlalu sempit */
    }

    /* Style untuk tabel yang diubah menjadi Card di mobile */
    @media (max-width: 767.98px) {
        .table-responsive-stack thead {
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
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
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

        .table-responsive-stack tr td:first-child {
            border-top: 1px solid #f0f0f0;
        }

        .table-responsive-stack td::before {
            content: attr(data-label);
            font-weight: bold;
            text-align: left;
            margin-right: 1rem;
            color: #6c757d;
        }

        .table-responsive-stack td[data-label="No"] {
            display: none;
            /* Sembunyikan kolom "No" di mobile */
        }
    }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow-sm mb-4 percentage-summary-card">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="card-title mb-1 font-weight-bold text-primary">Alokasi Dana Pengeluaran</h5>
                    <p class="card-text text-muted mb-2">Pastikan total alokasi persentase dari semua kategori tepat 100%.</p>
                    <div id="notification-text" class="mt-1"></div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <h3 id="total-persentase-counter" class="fw-bold mb-0 display-4"><?= number_format($totalPersentase, 2); ?>%</h3>
                </div>
            </div>
            <div class="progress mt-3">
                <div id="percentage-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $totalPersentase; ?>%;" aria-valuenow="<?= $totalPersentase; ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kategori</h6>
            <a href="<?= site_url('/master-kategori/new'); ?>" id="btn-tambah-data" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Tambah Kategori Baru</a>
        </div>
        <div class="card-body">
            <div class="table-responsive-stack">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th>Nama Kategori</th>
                            <th class="text-center" style="width: 15%;">Persentase</th>
                            <th class="text-center" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kategori)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($kategori as $k): ?>
                                <tr>
                                    <td data-label="No" class="text-center"><?= $no++; ?></td>
                                    <td data-label="Nama Kategori"><?= esc($k['nama_kategori']); ?></td>
                                    <td data-label="Persentase" class="text-center"><strong><?= esc($k['persentase']); ?>%</strong></td>
                                    <td data-label="Aksi" class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?= site_url('/master-kategori/' . $k['id'] . '/edit'); ?>" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="<?= site_url('/master-kategori/' . $k['id']); ?>" method="post" class="d-inline">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash-alt"></i></button>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const totalPersentase = <?= $totalPersentase; ?>;
        const btnTambah = document.getElementById('btn-tambah-data');
        const notificationText = document.getElementById('notification-text');
        const percentageBar = document.getElementById('percentage-bar');

        function checkTotal() {
            if (totalPersentase >= 100) {
                btnTambah.classList.add('disabled');
                btnTambah.setAttribute('aria-disabled', 'true');
                btnTambah.href = "javascript:void(0)";

                notificationText.innerHTML = '<span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i>Total alokasi sudah 100%. Tidak bisa menambah kategori baru.</span>';
                percentageBar.classList.remove('bg-info', 'progress-bar-animated', 'progress-bar-striped');
                percentageBar.classList.add('bg-success');

            } else if (totalPersentase > 90) {
                notificationText.innerHTML = `<span class="badge bg-warning text-dark fs-6"><i class="fas fa-exclamation-triangle me-1"></i>Sisa alokasi: <strong>${(100 - totalPersentase).toFixed(2)}%</strong>. Mendekati penuh!</span>`;
                percentageBar.classList.remove('bg-success');
                percentageBar.classList.add('bg-info', 'progress-bar-animated', 'progress-bar-striped');
            } else {
                btnTambah.classList.remove('disabled');
                btnTambah.setAttribute('aria-disabled', 'false');
                btnTambah.href = "<?= site_url('/master-kategori/new'); ?>";

                const sisa = 100 - totalPersentase;
                notificationText.innerHTML = `<span class="badge bg-info fs-6">Sisa alokasi yang tersedia: <strong>${sisa.toFixed(2)}%</strong></span>`;
                percentageBar.classList.remove('bg-success');
                percentageBar.classList.add('bg-info', 'progress-bar-animated', 'progress-bar-striped');
            }
        }

        checkTotal();
    });
</script>
<?= $this->endSection(); ?>