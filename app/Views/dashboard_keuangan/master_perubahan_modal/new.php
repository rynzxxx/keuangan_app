<?= $this->extend('dashboard_keuangan/layout/template'); // Sesuaikan layout Anda 
?>

<?= $this->section('content'); ?>
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><?= esc($title) ?></h4>
                </div>
                <div class="card-body">

                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger" role="alert">
                            <h6 class="alert-heading">Terjadi Kesalahan</h6>
                            <ul class="mb-0 ps-3">
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/master-perubahan-modal" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="nama_komponen" class="form-label">Nama Komponen</label>
                            <input type="text" class="form-control" id="nama_komponen" name="nama_komponen" value="<?= old('nama_komponen') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="penambahan" <?= old('kategori') == 'penambahan' ? 'selected' : '' ?>>Penambahan</option>
                                <option value="pengurangan" <?= old('kategori') == 'pengurangan' ? 'selected' : '' ?>>Pengurangan</option>
                            </select>
                        </div>

                </div>
                <div class="card-footer text-end">
                    <a href="/master-perubahan-modal" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection(); ?>