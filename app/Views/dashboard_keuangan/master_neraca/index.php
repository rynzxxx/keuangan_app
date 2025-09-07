<?= $this->extend('dashboard_keuangan/layout/template'); ?>
<?= $this->section('content'); ?>
<style>
    /* --- [TAMBAHAN] Kode untuk Tabel Master Data Responsif (di dalam Accordion) --- */

    /* Aktifkan hanya untuk layar kecil (di bawah 768px) */
    @media (max-width: 767.98px) {
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
            /* Setiap baris menjadi sebuah item terpisah */
            border-bottom: 1px solid #dee2e6;
        }

        .table-responsive-stack tr:last-child {
            border-bottom: none;
        }

        .table-responsive-stack td {
            /* Atur tata letak di dalam sel */
            display: flex;
            justify-content: space-between;
            /* Label di kiri, data di kanan */
            align-items: center;
            border: none;
            padding: 0.75rem 1rem;
            text-align: right;
            /* Data rata kanan */
        }

        .table-responsive-stack td::before {
            /* Buat label dari atribut data-label */
            content: attr(data-label);
            font-weight: bold;
            text-align: left;
            /* Label rata kiri */
            margin-right: 1rem;
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

            <div class="accordion" id="masterNeracaAccordion">
                <?php $index = 0;
                foreach ($komponen as $kategori => $items): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?= $kategori; ?>">
                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $kategori; ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse-<?= $kategori; ?>">
                                <strong class="me-2"><?= $kategoriLabels[$kategori]; ?></strong>
                                <span class="badge bg-secondary rounded-pill"><?= count($items); ?> item</span>
                            </button>
                        </h2>
                        <div id="collapse-<?= $kategori; ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading-<?= $kategori; ?>" data-bs-parent="#masterNeracaAccordion">
                            <div class="accordion-body p-0">
                                <div class="table-responsive-stack">
                                    <table class="table table-striped table-hover mb-0" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Nama Komponen</th>
                                                <th class="text-center" style="width: 15%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($items)): ?>
                                                <tr>
                                                    <td colspan="2" class="text-center fst-italic py-3">Belum ada komponen di kategori ini.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($items as $item): ?>
                                                    <tr>
                                                        <td data-label="Nama Komponen"><?= esc($item['nama_komponen']); ?></td>
                                                        <td data-label="Aksi" class="text-center">
                                                            <div class="btn-group" role="group">
                                                                <a href="<?= site_url('/master-neraca/' . $item['id'] . '/edit'); ?>" class="btn btn-outline-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                                                <form action="<?= site_url('/master-neraca/' . $item['id']); ?>" method="post" class="d-inline">
                                                                    <?= csrf_field(); ?>
                                                                    <input type="hidden" name="_method" value="DELETE">
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin?')"><i class="fas fa-trash-alt"></i></button>
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
                <?php $index++;
                endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>