<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>

<style>
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
            padding: 1rem;
        }

        .table-responsive-stack td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            padding: 0.5rem 0;
            text-align: right;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-responsive-stack tr td:last-child {
            border-bottom: none;
            padding-top: 1rem;
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
        }

        /* Membuat deskripsi bisa wrap dan tombol aksi di tengah */
        .table-responsive-stack td[data-label="Deskripsi"] {
            align-items: flex-start;
        }

        .table-responsive-stack td[data-label="Deskripsi"]::before {
            margin-top: 0.25rem;
        }

        .table-responsive-stack td[data-label="Aksi"] {
            justify-content: center;
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
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Master Pendapatan</h6>
            <a href="<?= site_url('/master-pendapatan/new'); ?>" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Tambah Data Baru</a>
        </div>
        <div class="card-body">
            <div class="table-responsive-stack">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th>Nama Pendapatan</th>
                            <th>Deskripsi</th>
                            <th class="text-center" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendapatan)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($pendapatan as $p): ?>
                                <tr>
                                    <td data-label="No" class="text-center"><?= $no++; ?></td>
                                    <td data-label="Nama Pendapatan"><?= esc($p['nama_pendapatan']); ?></td>
                                    <td data-label="Deskripsi"><?= esc($p['deskripsi']); ?></td>
                                    <td data-label="Aksi" class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?= site_url('/master-pendapatan/' . $p['id'] . '/edit'); ?>" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="<?= site_url('/master-pendapatan/' . $p['id']); ?>" method="post" class="d-inline">
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
<?= $this->endSection(); ?>