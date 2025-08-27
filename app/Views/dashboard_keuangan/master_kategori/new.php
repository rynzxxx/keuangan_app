<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Kategori</h6>
        </div>
        <div class="card-body">

            <div class="alert alert-info">
                Sisa alokasi persentase yang tersedia adalah: <strong><?= number_format($sisaPersentase, 2); ?>%</strong>
            </div>

            <?= form_open('/master-kategori'); ?>

            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control <?= ($validation->hasError('nama_kategori')) ? 'is-invalid' : ''; ?>" id="nama_kategori" name="nama_kategori" value="<?= old('nama_kategori'); ?>" autofocus>
                <div class="invalid-feedback">
                    <?= $validation->getError('nama_kategori'); ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="persentase" class="form-label">Persentase (%)</label>
                <input
                    type="number"
                    step="0.01"
                    max="<?= $sisaPersentase; ?>"
                    class="form-control <?= ($validation->hasError('persentase')) ? 'is-invalid' : ''; ?>"
                    id="persentase"
                    name="persentase"
                    value="<?= old('persentase'); ?>"
                    placeholder="Maksimal <?= number_format($sisaPersentase, 2); ?>">
                <div class="invalid-feedback">
                    <?= $validation->getError('persentase'); ?>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="<?= site_url('/master-kategori'); ?>" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaInput = document.getElementById('nama_kategori');
        const feedbackDiv = namaInput.nextElementSibling; // Div untuk pesan error

        // Variabel untuk menunda eksekusi (debounce)
        let typingTimer;
        const doneTypingInterval = 500; // 0.5 detik

        // Fungsi untuk melakukan pengecekan via AJAX
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

        // Event listener saat pengguna selesai mengetik
        namaInput.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(checkNamaKategori, doneTypingInterval);
        });
    });
</script>
<?= $this->endSection(); ?>