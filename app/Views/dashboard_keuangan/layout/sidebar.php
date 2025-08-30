<aside class="sidebar d-flex flex-column">
    <div class="sidebar-header">
        <h4 class="text-white">KeuanganApp</h4>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="<?= site_url('/dashboard_keuangan/dashboard'); ?>">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#bkuBulanan" role="button" aria-expanded="false" aria-controls="bkuBulanan">
                <i class="fas fa-book me-2"></i> BKU Bulanan <i class="fas fa-angle-left"></i>
            </a>
            <div class="collapse" id="bkuBulanan">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('bku-bulanan'); ?>">Lihat Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('bku-bulanan/new'); ?>">Tambah Baru</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/history'); ?>">Log Aktivitas</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#bkuTahunan" role="button" aria-expanded="false" aria-controls="bkuTahunan">
                <i class="fas fa-calendar-alt me-2"></i> BKU Tahunan <i class="fas fa-angle-left"></i>
            </a>
            <div class="collapse" id="bkuTahunan">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('bku-tahunan'); ?>">Lihat Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('bku-tahunan/new'); ?>">Tambah Baru</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#arusKas" role="button" aria-expanded="false" aria-controls="arusKas">
                <i class="fas fa-chart-line me-2"></i> Laporan Arus Kas <i class="fas fa-angle-left"></i>
            </a>
            <div class="collapse" id="arusKas">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('laporan-arus-kas'); ?>">Lihat Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('laporan-arus-kas/new'); ?>">Tambah Baru</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#perubahanModal" role="button" aria-expanded="false" aria-controls="perubahanModal">
                <i class="fas fa-file-invoice-dollar me-2"></i> Laporan Perubahan Modal <i class="fas fa-angle-left"></i>
            </a>
            <div class="collapse" id="perubahanModal">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('laporan-perubahan-modal'); ?>">Lihat Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('laporan-perubahan-modal/new'); ?>">Tambah Baru</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#neraca" role="button" aria-expanded="false" aria-controls="neraca">
                <i class="fas fa-balance-scale me-2"></i> Neraca Keuangan <i class="fas fa-angle-left"></i>
            </a>
            <div class="collapse" id="neraca">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('neraca-keuangan'); ?>">Lihat Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('neraca-keuangan/new'); ?>">Tambah Baru</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#masterdata" role="button" aria-expanded="false" aria-controls="masterdata">

                <i class="fas fa-database me-2"></i>

                Master Data

                <i class="fas fa-angle-left ms-auto"></i>

            </a>
            <div class="collapse" id="masterdata">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('master-kategori'); ?>">Kategori Pengeluaran</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('master-pendapatan'); ?>">Pendapatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('pengaturan'); ?>">Pengaturan tandatangan</a></li>

                </ul>
            </div>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a class="nav-link text-danger" href="<?= site_url('logout'); ?>">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </div>
</aside>