<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// ... (kode routes yang sudah ada)

// Rute untuk proses otentikasi
$routes->get('/', 'Auth::login'); // Arahkan halaman utama ke login
$routes->get('/login', 'Auth::login');
$routes->post('/login/process', 'Auth::processLogin');
$routes->get('/logout', 'Auth::logout');

// Grup rute yang dilindungi filter otentikasi
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/dashboard_keuangan/dashboard', 'Dashboard::index');
    $routes->get('/dashboard', 'Dashboard::index');
    // Rute untuk BKU Bulanan
    $routes->get('/bku-bulanan/detail/(:num)', 'BkuBulanan::detail/$1');
    $routes->resource('bku-bulanan', ['controller' => 'BkuBulanan', 'except' => 'show']);
    // Rute untuk BKU Tahunan
    $routes->get('/bku-tahunan', 'BkuTahunan::index');
    $routes->get('/bku-tahunan/new', 'BkuTahunan::new');
    $routes->get('/bku-tahunan/detail/(:num)', 'BkuTahunan::detail/$1');
    // Rute untuk BKU Arus Kas
    $routes->get('/laporan-arus-kas', 'LaporanArusKas::index');
    $routes->get('/laporan-arus-kas/new', 'LaporanArusKas::new');
    $routes->get('/laporan-arus-kas/detail/(:num)', 'LaporanArusKas::detail/$1');
    // Rute untuk BKU Perubahan Modal
    $routes->get('/laporan-perubahan-modal', 'LaporanPerubahanModal::index');
    $routes->get('/laporan-perubahan-modal/new', 'LaporanPerubahanModal::new');
    $routes->get('/laporan-perubahan-modal/detail/(:num)', 'LaporanPerubahanModal::detail/$1');
    // Rute untuk Neraca Keuangan
    $routes->get('/neraca-keuangan', 'NeracaKeuangan::index');
    $routes->get('/neraca-keuangan/new', 'NeracaKeuangan::new');
    $routes->get('/neraca-keuangan/detail/(:num)', 'NeracaKeuangan::detail/$1');
    // Rute untuk Master Kategori Pengeluaran
    $routes->resource('master-kategori', ['controller' => 'MasterKategori']);
    // Rute untuk AJAX check nama kategori
    $routes->get('/master-kategori/check-nama', 'MasterKategori::checkNama');
    // Rute untuk Master Pendapatan
    $routes->resource('master-pendapatan', ['controller' => 'MasterPendapatan']);
    // Rute untuk AJAX check nama pendapatan
    $routes->get('/master-pendapatan/check-nama', 'MasterPendapatan::checkNama');
    // Rute untuk ekspor BKU Bulanan pdf
    $routes->get('/bku-bulanan/cetak-pdf/(:num)', 'BkuBulanan::cetakPdf/$1');
    // Rute untuk ekspor BKU Bulanan ke Excel
    $routes->get('/bku-bulanan/cetak-excel/(:num)', 'BkuBulanan::cetakExcel/$1');
    // Rute untuk mendapatkan saldo bulan lalu via AJAX
    $routes->get('/bku-bulanan/get-saldo-lalu', 'BkuBulanan::getSaldoBulanLalu');
    // Rute untuk Pengaturan Laporan
    $routes->get('/pengaturan', 'Pengaturan::index', ['filter' => 'auth']);
    $routes->post('/pengaturan/update', 'Pengaturan::update', ['filter' => 'auth']);
    // Rute untuk ekspor BKU Tahunan pdf dan excel
    $routes->get('/bku-tahunan/cetak-pdf/(:num)', 'BkuTahunan::cetakPdf/$1');
    $routes->get('/bku-tahunan/cetak-excel/(:num)', 'BkuTahunan::cetakExcel/$1');
    // Rute untuk History Aktivitas
    $routes->get('/history', 'History::index', ['filter' => 'auth']);
});
