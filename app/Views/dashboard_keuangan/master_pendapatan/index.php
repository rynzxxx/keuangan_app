<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
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
            <a href="<?= site_url('/master-pendapatan/new'); ?>" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Tambah Data Baru</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
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
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= esc($p['nama_pendapatan']); ?></td>
                                    <td><?= esc($p['deskripsi']); ?></td>
                                    <td class="text-center">
                                        <a href="<?= site_url('/master-pendapatan/' . $p['id'] . '/edit'); ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>

                                        <form action="<?= site_url('/master-pendapatan/' . $p['id']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fas fa-trash-alt"></i></button>
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