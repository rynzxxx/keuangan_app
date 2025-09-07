<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .list-group-item-action {
        flex-shrink: 0;
        /* Mencegah tombol menyusut */
    }

    /* Di layar sangat kecil, buat item menjadi vertikal */
    @media (max-width: 480px) {
        .list-group-item-responsive {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .list-group-item-responsive .list-group-item-action {
            margin-top: 0.5rem;
            /* Beri jarak antara teks dan tombol */
            width: 100%;
            display: flex;
        }

        .list-group-item-responsive .list-group-item-action form,
        .list-group-item-responsive .list-group-item-action .btn {
            flex-grow: 1;
            /* Buat tombol hapus memenuhi space */
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
        <div class="card-header d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pendapatan-tab" data-bs-toggle="tab" data-bs-target="#pendapatan-pane" type="button" role="tab">
                        <i class="fas fa-arrow-down text-success me-2"></i>Pendapatan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="biaya-tab" data-bs-toggle="tab" data-bs-target="#biaya-pane" type="button" role="tab">
                        <i class="fas fa-arrow-up text-danger me-2"></i>Biaya
                    </button>
                </li>
            </ul>
            <div class="mt-2 mt-sm-0">
                <a href="<?= site_url('/master-laba-rugi/new'); ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus-circle me-2"></i>Tambah Komponen Baru</a>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="pendapatan-pane" role="tabpanel">
                    <ul class="list-group list-group-flush">
                        <?php if (empty($komponen['pendapatan'])): ?>
                            <li class="list-group-item text-center fst-italic">Belum ada komponen pendapatan.</li>
                        <?php else: ?>
                            <?php foreach ($komponen['pendapatan'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-responsive">
                                    <span><?= esc($item['nama_komponen']); ?></span>
                                    <div class="list-group-item-action">
                                        <form action="<?= site_url('/master-laba-rugi/' . $item['id']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus komponen ini?')">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="tab-pane fade" id="biaya-pane" role="tabpanel">
                    <ul class="list-group list-group-flush">
                        <?php if (empty($komponen['biaya'])): ?>
                            <li class="list-group-item text-center fst-italic">Belum ada komponen biaya.</li>
                        <?php else: ?>
                            <?php foreach ($komponen['biaya'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-responsive">
                                    <span><?= esc($item['nama_komponen']); ?></span>
                                    <div class="list-group-item-action">
                                        <form action="<?= site_url('/master-laba-rugi/' . $item['id']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus komponen ini?')">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>