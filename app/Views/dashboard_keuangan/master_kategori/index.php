<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card bg-light mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Total Alokasi Persentase</h5>
                    <p id="notification-text" class="card-text mt-1"></p>
                </div>
                <div class="text-end">
                    <h3 id="total-persentase-counter" class="fw-bold mb-0 display-4"><?= number_format($totalPersentase, 2); ?>%</h3>
                </div>
            </div>
            <div class="progress mt-2" style="height: 10px;">
                <div id="percentage-bar" class="progress-bar" role="progressbar" style="width: <?= $totalPersentase; ?>%;" aria-valuenow="<?= $totalPersentase; ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?= site_url('/master-kategori/new'); ?>" id="btn-tambah-data" class="btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Tambah Data Baru</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th>Nama Kategori</th>
                            <th class="text-center" style="width: 15%;">Persentase</th>
                            <th class="text-center" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kategori)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($kategori as $k): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= esc($k['nama_kategori']); ?></td>
                                    <td class="text-center"><?= esc($k['persentase']); ?>%</td>
                                    <td class="text-center">
                                        <a href="<?= site_url('/master-kategori/' . $k['id'] . '/edit'); ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>

                                        <form action="<?= site_url('/master-kategori/' . $k['id']); ?>" method="post" class="d-inline">
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil total awal dari PHP
        const totalPersentase = <?= $totalPersentase; ?>;

        // Ambil elemen-elemen
        const btnTambah = document.getElementById('btn-tambah-data');
        const notificationText = document.getElementById('notification-text');
        const percentageBar = document.getElementById('percentage-bar');

        // Fungsi untuk memeriksa dan memperbarui UI
        function checkTotal() {
            if (totalPersentase >= 100) {
                // Nonaktifkan tombol
                btnTambah.classList.add('disabled');
                btnTambah.setAttribute('aria-disabled', 'true');
                btnTambah.href = "javascript:void(0)"; // Mencegah navigasi

                // Tampilkan notifikasi
                notificationText.innerHTML = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Total persentase sudah mencapai 100%.</span>';
                percentageBar.classList.add('bg-success');

            } else {
                // Aktifkan tombol
                btnTambah.classList.remove('disabled');
                btnTambah.setAttribute('aria-disabled', 'false');
                btnTambah.href = "<?= site_url('/master-kategori/new'); ?>"; // Kembalikan link

                // Tampilkan sisa persentase
                const sisa = 100 - totalPersentase;
                notificationText.innerHTML = `<span class="badge bg-info">Sisa alokasi: <strong>${sisa.toFixed(2)}%</strong></span>`;
            }
        }

        // Jalankan pemeriksaan saat halaman dimuat
        checkTotal();
    });
</script>
<?= $this->endSection(); ?>