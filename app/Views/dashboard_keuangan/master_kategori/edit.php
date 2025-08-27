<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Kategori</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('/master-kategori/' . $kategori['id']); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control <?= ($validation->hasError('nama_kategori')) ? 'is-invalid' : ''; ?>" id="nama_kategori" name="nama_kategori" value="<?= old('nama_kategori', $kategori['nama_kategori']); ?>" autofocus>
                    <div class="invalid-feedback">
                        <?= $validation->getError('nama_kategori'); ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="persentase" class="form-label">Persentase (%)</label>
                    <input type="number" step="0.01" class="form-control <?= ($validation->hasError('persentase')) ? 'is-invalid' : ''; ?>" id="persentase" name="persentase" value="<?= old('persentase', $kategori['persentase']); ?>" placeholder="Contoh: 30.5">
                    <div class="invalid-feedback">
                        <?= $validation->getError('persentase'); ?>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="<?= site_url('/master-kategori'); ?>" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const id_kategori = <?= $kategori['id'] ?? 'null' ?>;
    document.addEventListener('DOMContentLoaded', function() {
        const namaInput = document.getElementById('nama_kategori');
        const feedbackDiv = namaInput.nextElementSibling;
        const currentId = <?= $kategori['id']; ?>; // Ambil ID kategori saat ini

        let typingTimer;
        const doneTypingInterval = 500;

        async function checkNamaKategori() {
            const nama = namaInput.value.trim();
            const feedbackDiv = namaInput.nextElementSibling;

            // Untuk form edit, pastikan Anda sudah mendefinisikan currentId
            const currentId = typeof id_kategori !== 'undefined' ? id_kategori : null;

            if (nama.length < 3) {
                return;
            }

            // Bangun URL dengan parameter yang relevan
            let checkUrl = `<?= site_url('/master-kategori/check-nama') ?>?nama_kategori=${encodeURIComponent(nama)}`;
            if (currentId) {
                checkUrl += `&id=${currentId}`;
            }

            const response = await fetch(checkUrl);
            const data = await response.json();

            // LOGIKA BARU DI JAVASCRIPT:
            // Sekarang kita memeriksa 'if (data.exists)' bukan '!data.is_unique'
            if (data.exists) {
                namaInput.classList.add('is-invalid');
                feedbackDiv.textContent = 'Nama kategori ini sudah ada di dalam database.';
            } else {
                // Hapus error jika nama belum ada
                if (feedbackDiv.textContent === 'Nama kategori ini sudah ada di dalam database.') {
                    namaInput.classList.remove('is-invalid');
                }
            }
        }

        namaInput.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(checkNamaKategori, doneTypingInterval);
        });
    });
</script>
<?= $this->endSection(); ?>