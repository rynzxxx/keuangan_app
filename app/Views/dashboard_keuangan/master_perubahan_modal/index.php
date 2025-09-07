<?= $this->extend('dashboard_keuangan/layout/template'); // Sesuaikan layout Anda 
?>

<?= $this->section('content'); ?>
<style>
    /* --- [TAMBAHAN] Kode untuk Tabel Master Data Responsif --- */

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
            /* Setiap baris menjadi sebuah card */
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .table-responsive-stack td {
            /* Atur tata letak di dalam sel */
            display: flex;
            justify-content: space-between;
            /* Label di kiri, data di kanan */
            align-items: center;
            border: none;
            border-bottom: 1px solid #f0f0f0;
            padding: 0.75rem 1rem;
            text-align: right;
            /* Data rata kanan */
        }

        .table-responsive-stack tr td:last-child {
            border-bottom: none;
            /* Hapus border di item terakhir */
        }

        .table-responsive-stack td::before {
            /* Buat label dari atribut data-label */
            content: attr(data-label);
            font-weight: bold;
            text-align: left;
            /* Label rata kiri */
            margin-right: 1rem;
        }

        /* Khusus untuk kolom aksi, dorong tombol ke kanan */
        .table-responsive-stack td[data-label="Aksi"] form {
            width: 100%;
            text-align: right;
        }
    }
</style>
<div class="container">
    <h1><?= esc($title) ?></h1>
    <a href="/master-perubahan-modal/new" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Tambah Komponen Baru
    </a>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="table-responsive-stack">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Komponen</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($komponen as $item): ?>
                    <tr>
                        <td data-label="#"><?= $no++ ?></td>
                        <td data-label="Nama Komponen"><?= esc($item['nama_komponen']) ?></td>
                        <td data-label="Kategori"><?= ucfirst(esc($item['kategori'])) ?></td>
                        <td data-label="Aksi">
                            <form action="/master-perubahan-modal/delete/<?= $item['id'] ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komponen ini?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>