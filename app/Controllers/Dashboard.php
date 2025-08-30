<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BkuBulananModel;

class Dashboard extends BaseController
{

    public function index()
    {
        $db = \Config\Database::connect();

        // --- 1. Tentukan Rentang Waktu ---
        // Ambil dari URL, jika tidak ada, atur default 1 tahun terakhir
        $tanggalMulai = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-1 year'));
        $tanggalSelesai = $this->request->getGet('end_date') ?? date('Y-m-d');

        // --- 2. Siapkan Data untuk Grafik Pendapatan vs Pengeluaran ---
        $bkuModel = new BkuBulananModel();
        $laporanBulanan = $bkuModel
            ->where("STR_TO_DATE(CONCAT(tahun, '-', bulan, '-01'), '%Y-%m-%d') >=", $tanggalMulai)
            ->where("STR_TO_DATE(CONCAT(tahun, '-', bulan, '-01'), '%Y-%m-%d') <=", $tanggalSelesai)
            ->orderBy('tahun', 'ASC')->orderBy('bulan', 'ASC')
            ->findAll();

        $grafikLine = [
            'labels' => [],
            'pendapatan' => [],
            'pengeluaran' => [],
        ];
        foreach ($laporanBulanan as $laporan) {
            $grafikLine['labels'][] = $laporan['tahun'] . '-' . str_pad($laporan['bulan'], 2, '0', STR_PAD_LEFT);
            $grafikLine['pendapatan'][] = $laporan['total_pendapatan'];
            $grafikLine['pengeluaran'][] = $laporan['total_pengeluaran'];
        }

        // --- 3. Siapkan Data untuk Grafik Donat Pendapatan ---
        $builderPendapatan = $db->table('detail_pendapatan as dp');
        $builderPendapatan->select('mp.nama_pendapatan, SUM(dp.jumlah) as total');
        $builderPendapatan->join('master_pendapatan as mp', 'mp.id = dp.master_pendapatan_id');
        $builderPendapatan->join('bku_bulanan as bb', 'bb.id = dp.bku_id');
        $builderPendapatan->where("STR_TO_DATE(CONCAT(bb.tahun, '-', bb.bulan, '-01'), '%Y-%m-%d') >=", $tanggalMulai);
        $builderPendapatan->where("STR_TO_DATE(CONCAT(bb.tahun, '-', bb.bulan, '-01'), '%Y-%m-%d') <=", $tanggalSelesai);
        $builderPendapatan->groupBy('mp.nama_pendapatan');
        $komponenPendapatan = $builderPendapatan->get()->getResultArray();

        // --- 4. Siapkan Data untuk Grafik Donat Pengeluaran ---
        $builderPengeluaran = $db->table('detail_pengeluaran as dp');
        $builderPengeluaran->select('mkp.nama_kategori, SUM(dp.jumlah) as total');
        $builderPengeluaran->join('master_kategori_pengeluaran as mkp', 'mkp.id = dp.master_kategori_id');
        $builderPengeluaran->join('bku_bulanan as bb', 'bb.id = dp.bku_id');
        $builderPengeluaran->where("STR_TO_DATE(CONCAT(bb.tahun, '-', bb.bulan, '-01'), '%Y-%m-%d') >=", $tanggalMulai);
        $builderPengeluaran->where("STR_TO_DATE(CONCAT(bb.tahun, '-', bb.bulan, '-01'), '%Y-%m-%d') <=", $tanggalSelesai);
        $builderPengeluaran->groupBy('mkp.nama_kategori');
        $komponenPengeluaran = $builderPengeluaran->get()->getResultArray();

        // Kirim semua data ke view
        $data = [
            'title' => 'Dashboard Utama',
            'grafikLine' => $grafikLine,
            'komponenPendapatan' => $komponenPendapatan,
            'komponenPengeluaran' => $komponenPengeluaran,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai
        ];

        return view('dashboard_keuangan/dashboard', $data);
    }
}
