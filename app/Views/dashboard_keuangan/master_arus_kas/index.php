<?= $this->extend('dashboard_keuangan/layout/template'); // Sesuaikan dengan layout Anda 
?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success" role="alert">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus me-2"></i>Tambah Komponen Baru</h6>
                </div>
                <form action="<?= base_url('/master-arus-kas/create'); ?>" method="post">
                    <div class="card-body">
                        <?= csrf_field(); ?>
                        <div class="mb-3">
                            <label for="nama_komponen" class="form-label">Nama Komponen</label>
                            <input type="text" class="form-control" id="nama_komponen" name="nama_komponen" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="masuk">Arus Kas Masuk</option>
                                <option value="keluar">Arus Kas Keluar</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#masuk-tab-pane" type="button" role="tab" aria-controls="masuk-tab-pane" aria-selected="true">
                                <i class="fas fa-arrow-down text-success me-2"></i>Arus Kas Masuk
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="keluar-tab" data-bs-toggle="tab" data-bs-target="#keluar-tab-pane" type="button" role="tab" aria-controls="keluar-tab-pane" aria-selected="false">
                                <i class="fas fa-arrow-up text-danger me-2"></i>Arus Kas Keluar
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="masuk-tab-pane" role="tabpanel" aria-labelledby="masuk-tab">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($komponen_masuk as $km) : ?>
                                    <li class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                                        <span class="mb-2 mb-sm-0"><?= esc($km['nama_komponen']); ?></span>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?= $km['id']; ?>"><i class="fas fa-edit"></i></button>
                                            <a href="<?= base_url('/master-arus-kas/delete/' . $km['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin?')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="keluar-tab-pane" role="tabpanel" aria-labelledby="keluar-tab">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($komponen_keluar as $kk) : ?>
                                    <li class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                                        <span class="mb-2 mb-sm-0"><?= esc($kk['nama_komponen']); ?></span>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?= $kk['id']; ?>"><i class="fas fa-edit"></i></button>
                                            <a href="<?= base_url('/master-arus-kas/delete/' . $kk['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin?')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php foreach (array_merge($komponen_masuk, $komponen_keluar) as $k) : ?>
    <div class="modal fade" id="editModal-<?= $k['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $k['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel-<?= $k['id'] ?>">Edit Komponen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('/master-arus-kas/update/' . $k['id']); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_komponen_<?= $k['id'] ?>" class="form-label">Nama Komponen</label>
                            <input type="text" id="nama_komponen_<?= $k['id'] ?>" class="form-control" name="nama_komponen" value="<?= esc($k['nama_komponen']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" value="<?= ucfirst($k['kategori']); ?>" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection(); ?>