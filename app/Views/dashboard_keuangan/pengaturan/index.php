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
            <h6 class="m-0 font-weight-bold text-primary">Formulir Pengaturan Tanda Tangan Laporan</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('/pengaturan/update'); ?>" method="post">
                <?= csrf_field(); ?>

                <div class="mb-3">
                    <label for="lokasi_laporan" class="form-label">Lokasi Laporan</label>
                    <input type="text" class="form-control" id="lokasi_laporan" name="lokasi_laporan" value="<?= esc($lokasi_laporan); ?>" placeholder="Contoh: Melung">
                </div>

                <div class="mb-3">
                    <label for="ketua_bumdes" class="form-label">Nama Ketua BUMDES</label>
                    <input type="text" class="form-control" id="ketua_bumdes" name="ketua_bumdes" value="<?= esc($ketua_bumdes); ?>" placeholder="Contoh: Kartim">
                </div>

                <div class="mb-3">
                    <label for="bendahara_bumdes" class="form-label">Nama Bendahara BUMDES</label>
                    <input type="text" class="form-control" id="bendahara_bumdes" name="bendahara_bumdes" value="<?= esc($bendahara_bumdes); ?>" placeholder="Contoh: Rustiani">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>