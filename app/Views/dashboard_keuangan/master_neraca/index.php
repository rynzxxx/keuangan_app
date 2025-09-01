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
            <a href="<?= site_url('/master-neraca/new'); ?>" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Tambah Komponen Baru</a>
        </div>
        <div class="card-body">
            <?php
            $kategoriLabels = [
                'aktiva_lancar' => 'Aktiva Lancar',
                'aktiva_tetap' => 'Aktiva Tetap',
                'hutang_lancar' => 'Hutang Lancar',
                'hutang_jangka_panjang' => 'Hutang Jangka Panjang',
                'modal' => 'Modal'
            ];
            ?>

            <?php foreach ($komponen as $kategori => $items): ?>
                <h5 class="mt-4 mb-3"><strong><?= $kategoriLabels[$kategori]; ?></strong></h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th>Nama Komponen</th>
                                <th class="text-center" style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="2" class="text-center">Belum ada komponen di kategori ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= esc($item['nama_komponen']); ?></td>
                                        <td class="text-center">
                                            <a href="<?= site_url('/master-neraca/' . $item['id'] . '/edit'); ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form action="<?= site_url('/master-neraca/' . $item['id']); ?>" method="post" class="d-inline">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin?')"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>