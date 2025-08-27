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
    // Rute untuk BKU Bulanan
    $routes->get('/bku-bulanan', 'BkuBulanan::index');
    $routes->get('/bku-bulanan/new', 'BkuBulanan::new');
    $routes->get('/bku-bulanan/detail/(:num)', 'BkuBulanan::detail/$1');
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
});
