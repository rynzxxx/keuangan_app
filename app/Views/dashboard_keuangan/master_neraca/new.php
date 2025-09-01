<?= $this->extend('dashboard_keuangan/layout/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Komponen Neraca</h6>
        </div>
        <div class="card-body">
            <?= form_open('/master-neraca'); ?>
            <div class="mb-3">
                <label for="nama_komponen" class="form-label">Nama Komponen</label>
                <input type="text" class="form-control <?= ($validation->hasError('nama_komponen')) ? 'is-invalid' : ''; ?>" id="nama_komponen" name="nama_komponen" value="<?= old('nama_komponen'); ?>" autofocus>
                <div class="invalid-feedback"><?= $validation->getError('nama_komponen'); ?></div>
            </div>

            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select <?= ($validation->hasError('kategori')) ? 'is-invalid' : ''; ?>" id="kategori" name="kategori">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="aktiva_lancar" <?= old('kategori') == 'aktiva_lancar' ? 'selected' : '' ?>>Aktiva Lancar</option>
                    <option value="aktiva_tetap" <?= old('kategori') == 'aktiva_tetap' ? 'selected' : '' ?>>Aktiva Tetap</option>
                    <option value="hutang_lancar" <?= old('kategori') == 'hutang_lancar' ? 'selected' : '' ?>>Hutang Lancar</option>
                    <option value="hutang_jangka_panjang" <?= old('kategori') == 'hutang_jangka_panjang' ? 'selected' : '' ?>>Hutang Jangka Panjang</option>
                    <option value="modal" <?= old('kategori') == 'modal' ? 'selected' : '' ?>>Modal</option>
                </select>
                <div class="invalid-feedback"><?= $validation->getError('kategori'); ?></div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="<?= site_url('/master-neraca'); ?>" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>