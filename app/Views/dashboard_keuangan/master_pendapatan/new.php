<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Jenis Pendapatan</h6>
        </div>
        <div class="card-body">
            <?= form_open('/master-pendapatan'); ?>
            <div class="mb-3">
                <label for="nama_pendapatan" class="form-label">Nama Pendapatan</label>
                <input type="text" class="form-control <?= ($validation->hasError('nama_pendapatan')) ? 'is-invalid' : ''; ?>" id="nama_pendapatan" name="nama_pendapatan" value="<?= old('nama_pendapatan'); ?>" autofocus>
                <div class="invalid-feedback">
                    <?= $validation->getError('nama_pendapatan'); ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= old('deskripsi'); ?></textarea>
            </div>
            <div class="d-flex justify-content-end">
                <a href="<?= site_url('/master-pendapatan'); ?>" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaInput = document.getElementById('nama_pendapatan');
        const feedbackDiv = namaInput.nextElementSibling;
        let typingTimer;
        const doneTypingInterval = 500;

        async function checkNamaPendapatan() {
            const nama = namaInput.value.trim();
            if (nama.length < 3) return;

            const response = await fetch(`<?= site_url('/master-pendapatan/check-nama') ?>?nama_pendapatan=${encodeURIComponent(nama)}`);
            const data = await response.json();

            if (data.exists) {
                namaInput.classList.add('is-invalid');
                feedbackDiv.textContent = 'Nama pendapatan ini sudah ada di dalam database.';
            } else {
                if (feedbackDiv.textContent === 'Nama pendapatan ini sudah ada di dalam database.') {
                    namaInput.classList.remove('is-invalid');
                }
            }
        }

        namaInput.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(checkNamaPendapatan, doneTypingInterval);
        });
    });
</script>
<?= $this->endSection(); ?>