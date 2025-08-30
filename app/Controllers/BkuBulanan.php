<?php

namespace App\Controllers;

// --- Bagian Use Statement ---
use App\Controllers\BaseController;
use App\Models\BkuBulananModel;
use App\Models\DetailAlokasiModel;
use App\Models\DetailPendapatanModel;
use App\Models\DetailPengeluaranModel;
use App\Models\MasterKategoriPengeluaranModel;
use App\Models\MasterPendapatanModel;
use App\Models\PengaturanModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\LogAktivitasModel;


class BkuBulanan extends BaseController
{

    public function index()
    {
        // Panggil model
        $bkuModel = new BkuBulananModel();

        $data = [
            'title' => 'Daftar BKU Bulanan',
            // Ambil semua data, urutkan berdasarkan tahun dan bulan terbaru
            'laporan' => $bkuModel->orderBy('tahun', 'DESC')->orderBy('bulan', 'DESC')->findAll()
        ];

        return view('dashboard_keuangan/bku_bulanan/index', $data);
    }

    /**
     * Helper method untuk mencatat aktivitas
     */
    private function logAktivitas($aktivitas, $deskripsi, $bku_id = null)
    {
        $logModel = new LogAktivitasModel();
        $logModel->save([
            'username'  => session()->get('username') ?? 'System', // Ambil username dari session
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'bku_id'    => $bku_id
        ]);
    }


    /**     * Membuat dan men-download laporan dalam format Excel (.xlsx)
     */
    public function cetakPdf($id = null)
    {
        // 1. Ambil semua data yang dibutuhkan (termasuk data pengaturan)
        $bkuModel = new BkuBulananModel();
        $detailPendapatanModel = new DetailPendapatanModel();
        $detailPengeluaranModel = new DetailPengeluaranModel();
        $masterKategoriModel = new MasterKategoriPengeluaranModel();
        $detailAlokasiModel = new DetailAlokasiModel();
        $pengaturanModel = new PengaturanModel(); // Model baru

        $laporan = $bkuModel->find($id);
        if (!$laporan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Laporan BKU tidak ditemukan.');
        }

        // Ambil semua rincian data
        $rincianPendapatan = $detailPendapatanModel->select('detail_pendapatan.*, master_pendapatan.nama_pendapatan')->join('master_pendapatan', 'master_pendapatan.id = detail_pendapatan.master_pendapatan_id')->where('detail_pendapatan.bku_id', $id)->findAll();
        $rincianPengeluaran = $detailPengeluaranModel->select('detail_pengeluaran.*, master_kategori_pengeluaran.nama_kategori')->join('master_kategori_pengeluaran', 'master_kategori_pengeluaran.id = detail_pengeluaran.master_kategori_id')->where('detail_pengeluaran.bku_id', $id)->findAll();
        $rincianAlokasi = $detailAlokasiModel->select('detail_alokasi.*, master_kategori_pengeluaran.nama_kategori')->join('master_kategori_pengeluaran', 'master_kategori_pengeluaran.id = detail_alokasi.master_kategori_id')->where('detail_alokasi.bku_id', $id)->findAll();
        $masterKategori = $masterKategoriModel->findAll();

        // Ambil data untuk tanda tangan
        $ketua = $pengaturanModel->where('meta_key', 'ketua_bumdes')->first()['meta_value'] ?? 'NAMA KETUA';
        $bendahara = $pengaturanModel->where('meta_key', 'bendahara_bumdes')->first()['meta_value'] ?? 'NAMA BENDAHARA';
        $lokasi = $pengaturanModel->where('meta_key', 'lokasi_laporan')->first()['meta_value'] ?? 'LOKASI';

        // Data nama bulan
        $bulanIndonesia = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $namaBulan = $bulanIndonesia[(int)$laporan['bulan']];

        // Siapkan semua data untuk dikirim ke view
        $data = [
            'laporan' => $laporan,
            'rincianPendapatan' => $rincianPendapatan,
            'rincianPengeluaran' => $rincianPengeluaran,
            'rincianAlokasi' => $rincianAlokasi,
            'masterKategori' => $masterKategori, // Kirim master kategori ke view
            'ketua' => $ketua,
            'bendahara' => $bendahara,
            'lokasi' => $lokasi,
            'namaBulan' => $namaBulan
        ];

        // 2. Siapkan nama file
        $filename = 'BKU_Bulanan_' . $namaBulan . '_' . $laporan['tahun'] . '.pdf';

        // 3. Render view HTML ke dalam sebuah variabel
        $html = view('dashboard_keuangan/bku_bulanan/cetak_pdf', $data);

        // 4. Inisialisasi library Dompdf
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // [PERUBAHAN] Ganti orientasi menjadi landscape
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // 5. Kirim file PDF ke browser untuk di-download
        $dompdf->stream($filename, ['Attachment' => false]); // Attachment false untuk pratinjau
    }

    /**
     * Membuat dan men-download laporan dalam format Excel (.xlsx)
     */
    public function cetakExcel($id = null)
    {
        // =======================================================================================
        // LANGKAH 1: Tambahkan Array Konfigurasi di sini
        // =======================================================================================
        // Peta untuk mendefinisikan hierarki.
        // Key adalah 'nama_kategori' dari INDUK.
        // Value adalah array berisi 'nama_kategori' dari ANAK-ANAKNYA.
        // PENTING: Nama harus sama persis dengan yang ada di database.
        $konfigurasiHierarki = [
            'OPERASIONAL PENGELOLAAN' => [
                'KESEKRETARIATAN',
                'PROMOSI'
            ],
            // Jika nanti ada induk baru, cukup tambahkan di sini. Contoh:
            // 'PENGEMBANGAN ASET' => [
            //     'ASET PRODUKTIF',
            //     'ASET NON-PRODUKTIF'
            // ]
        ];

        // 1. Ambil semua data yang dibutuhkan (Tidak ada perubahan)
        $bkuModel = new BkuBulananModel();
        $detailPendapatanModel = new DetailPendapatanModel();
        $detailPengeluaranModel = new DetailPengeluaranModel();
        $masterKategoriModel = new MasterKategoriPengeluaranModel();
        $detailAlokasiModel = new DetailAlokasiModel();
        $pengaturanModel = new PengaturanModel();

        $laporan = $bkuModel->find($id);
        if (!$laporan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Laporan BKU tidak ditemukan.');
        }

        $rincianPendapatan = $detailPendapatanModel->select('detail_pendapatan.*, master_pendapatan.nama_pendapatan')->join('master_pendapatan', 'master_pendapatan.id = detail_pendapatan.master_pendapatan_id')->where('detail_pendapatan.bku_id', $id)->findAll();
        $rincianPengeluaran = $detailPengeluaranModel->select('detail_pengeluaran.*, master_kategori_pengeluaran.nama_kategori')->join('master_kategori_pengeluaran', 'master_kategori_pengeluaran.id = detail_pengeluaran.master_kategori_id')->where('detail_pengeluaran.bku_id', $id)->findAll();
        $rincianAlokasi = $detailAlokasiModel->select('detail_alokasi.*, master_kategori_pengeluaran.nama_kategori')->join('master_kategori_pengeluaran', 'master_kategori_pengeluaran.id = detail_alokasi.master_kategori_id')->where('detail_alokasi.bku_id', $id)->findAll();
        $masterKategori = $masterKategoriModel->findAll();

        $ketua = $pengaturanModel->where('meta_key', 'ketua_bumdes')->first()['meta_value'] ?? 'NAMA KETUA';
        $bendahara = $pengaturanModel->where('meta_key', 'bendahara_bumdes')->first()['meta_value'] ?? 'NAMA BENDAHARA';
        $lokasi = $pengaturanModel->where('meta_key', 'lokasi_laporan')->first()['meta_value'] ?? 'LOKASI';
        $bulanIndonesia = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $namaBulan = $bulanIndonesia[(int)$laporan['bulan']];
        $spreadsheet = new Spreadsheet();
        /** @var Worksheet $sheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('BKU ' . $namaBulan . ' ' . $laporan['tahun']);

        // =======================================================================================
        // BAGIAN YANG DIMODIFIKASI
        // =======================================================================================

        // 3. Setup Kolom Dinamis (Logika Baru)
        $kategoriHierarki = [];
        $kategoriColumnMap = [];
        $kategoriSudahDiProses = [];

        $kategoriByName = [];
        foreach ($masterKategori as $kat) {
            $kategoriByName[$kat['nama_kategori']] = $kat;
        }

        foreach ($masterKategori as $kat) {
            $namaKategori = $kat['nama_kategori'];
            if (in_array($namaKategori, $kategoriSudahDiProses)) {
                continue;
            }
            if (isset($konfigurasiHierarki[$namaKategori])) {
                $childrenData = [];
                foreach ($konfigurasiHierarki[$namaKategori] as $namaAnak) {
                    if (isset($kategoriByName[$namaAnak])) {
                        $childrenData[] = $kategoriByName[$namaAnak];
                        $kategoriSudahDiProses[] = $namaAnak;
                    }
                }
                $kat['children'] = $childrenData;
                $kategoriHierarki[] = $kat;
            } else {
                $kat['children'] = [];
                $kategoriHierarki[] = $kat;
            }
        }

        $totalKolomPengeluaran = 0;
        foreach ($kategoriHierarki as $kat) {
            $childCount = count($kat['children']);
            $totalKolomPengeluaran += ($childCount > 0) ? $childCount : 1;
        }

        $startColPengeluaran = 5; // Kolom E
        $endColPengeluaran = ($totalKolomPengeluaran > 0) ? $startColPengeluaran + $totalKolomPengeluaran - 1 : $startColPengeluaran;
        $komulatifCol = $endColPengeluaran + 1;
        $saldoCol = $endColPengeluaran + 2;
        $endColTotal = $saldoCol;
        $endColTotalStr = Coordinate::stringFromColumnIndex($endColTotal);

        // 4. Buat Judul Laporan (Tidak ada perubahan)
        $sheet->mergeCells('A1:' . $endColTotalStr . '1')->setCellValue('A1', 'BUKU KAS UMUM BADAN USAHA MILIK DESA');
        $sheet->mergeCells('A2:' . $endColTotalStr . '2')->setCellValue('A2', '*BUMDES ALAM LESTARI*');
        $sheet->mergeCells('A3:' . $endColTotalStr . '3')->setCellValue('A3', 'DESA MELUNG KECAMATAN KEDUNGBANTENG');
        $sheet->mergeCells('A4:' . $endColTotalStr . '4')->setCellValue('A4', 'KABUPATEN BANYUMAS');
        $sheet->mergeCells('A5:' . $endColTotalStr . '5')->setCellValue('A5', 'PERIODE: ' . strtoupper($namaBulan . ' ' . $laporan['tahun']));
        $sheet->getStyle('A1:A5')->getFont()->setBold(true);
        $sheet->getStyle('A1:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 5. Buat Header Tabel (Logika Baru)
        $headerRow = 8;
        $subHeaderRow1 = 9;
        $subHeaderRow2 = 10;

        if ($totalKolomPengeluaran > 0) {
            $startColPengeluaranStr = Coordinate::stringFromColumnIndex($startColPengeluaran);
            $endColPengeluaranStr = Coordinate::stringFromColumnIndex($endColPengeluaran);
            $sheet->mergeCells($startColPengeluaranStr . $headerRow . ':' . $endColPengeluaranStr . $headerRow)->setCellValue($startColPengeluaranStr . $headerRow, 'PENGELUARAN');
        }

        $currentColIndex = $startColPengeluaran;
        foreach ($kategoriHierarki as $parentKat) {
            $colStr = Coordinate::stringFromColumnIndex($currentColIndex);
            if (empty($parentKat['children'])) {
                $sheet->mergeCells($colStr . $subHeaderRow1 . ':' . $colStr . $subHeaderRow2);
                $sheet->setCellValue($colStr . $subHeaderRow1, strtoupper($parentKat['nama_kategori']) . "\n" . $parentKat['persentase'] . '%');
                $kategoriColumnMap[$parentKat['id']] = $currentColIndex;
                $currentColIndex++;
            } else {
                $childCount = count($parentKat['children']);
                $endMergeColIndex = $currentColIndex + $childCount - 1;
                $endMergeColStr = Coordinate::stringFromColumnIndex($endMergeColIndex);
                $sheet->mergeCells($colStr . $subHeaderRow1 . ':' . $endMergeColStr . $subHeaderRow1);
                $sheet->setCellValue($colStr . $subHeaderRow1, strtoupper($parentKat['nama_kategori']) . "\n" . $parentKat['persentase'] . '%');
                foreach ($parentKat['children'] as $childKat) {
                    $childColStr = Coordinate::stringFromColumnIndex($currentColIndex);
                    $sheet->setCellValue($childColStr . $subHeaderRow2, strtoupper($childKat['nama_kategori']) . "\n" . $childKat['persentase'] . '%');
                    $kategoriColumnMap[$childKat['id']] = $currentColIndex;
                    $currentColIndex++;
                }
            }
        }

        $komulatifColStr = Coordinate::stringFromColumnIndex($komulatifCol);
        $saldoColStr = Coordinate::stringFromColumnIndex($saldoCol);
        $sheet->mergeCells('A' . $headerRow . ':A' . $subHeaderRow2)->setCellValue('A' . $headerRow, 'NO');
        $sheet->mergeCells('B' . $headerRow . ':B' . $subHeaderRow2)->setCellValue('B' . $headerRow, 'TANGGAL');
        $sheet->mergeCells('C' . $headerRow . ':C' . $subHeaderRow2)->setCellValue('C' . $headerRow, 'URAIAN');
        $sheet->mergeCells('D' . $headerRow . ':D' . $subHeaderRow2)->setCellValue('D' . $headerRow, 'PENDAPATAN');
        $sheet->mergeCells($komulatifColStr . $headerRow . ':' . $komulatifColStr . $subHeaderRow2)->setCellValue($komulatifColStr . $headerRow, 'KOMULATIF PENGELUARAN');
        $sheet->mergeCells($saldoColStr . $headerRow . ':' . $saldoColStr . $subHeaderRow2)->setCellValue($saldoColStr . $headerRow, 'SALDO');

        // 6. Isi Data Baris per Baris
        $row = $subHeaderRow2 + 1; // Data dimulai dari baris 11

        // =======================================================================================
        // AKHIR DARI BAGIAN YANG DIMODIFIKASI
        // =======================================================================================

        $nomor = 1;
        $saldo = 0;
        $komulatifPengeluaran = 0;

        $sheet->setCellValue('A' . $row, $nomor++);
        $sheet->setCellValue('C' . $row, 'Sisa Saldo Bulan Lalu');
        $sheet->setCellValue('D' . $row, $laporan['saldo_bulan_lalu']);
        $saldo += (float)$laporan['saldo_bulan_lalu'];
        $sheet->setCellValue($saldoColStr . $row, $saldo);
        $row++;

        foreach ($rincianPendapatan as $p) {
            $sheet->setCellValue('A' . $row, $nomor++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($p['created_at'])));
            $sheet->setCellValue('C' . $row, $p['nama_pendapatan']);
            $sheet->setCellValue('D' . $row, $p['jumlah']);
            $saldo += (float)$p['jumlah'];
            $sheet->setCellValue($saldoColStr . $row, $saldo);
            $row++;
        }

        $totalPendapatanRow = $row;
        $sheet->setCellValue('C' . $row, 'Total Pendapatan Bulan Ini');
        $sheet->setCellValue('D' . $row, $laporan['total_pendapatan']);
        foreach ($rincianAlokasi as $alokasi) {
            if (isset($kategoriColumnMap[$alokasi['master_kategori_id']])) {
                $colIndex = $kategoriColumnMap[$alokasi['master_kategori_id']];
                $colIndexStr = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colIndexStr . $row, $alokasi['jumlah_alokasi']);
            }
        }
        $row++;

        foreach ($rincianPengeluaran as $p) {
            $sheet->setCellValue('A' . $row, $nomor++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($p['created_at'])));
            $sheet->setCellValue('C' . $row, $p['deskripsi_pengeluaran']);
            if (isset($kategoriColumnMap[$p['master_kategori_id']])) {
                $colIndex = $kategoriColumnMap[$p['master_kategori_id']];
                $colIndexStr = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colIndexStr . $row, $p['jumlah']);
            }
            $komulatifPengeluaran += (float)$p['jumlah'];
            $saldo -= (float)$p['jumlah'];
            $sheet->setCellValue($komulatifColStr . $row, $komulatifPengeluaran);
            $sheet->setCellValue($saldoColStr . $row, $saldo);
            $row++;
        }

        $row++;
        $alokasiRow = $row;
        $sisaAlokasiRow = $row + 1;
        $jumlahPengeluaranRow = $row + 2;
        $sisaSaldoRow = $row + 3;
        $sheet->setCellValue('C' . $alokasiRow, 'Alokasi');
        $sheet->setCellValue('C' . $sisaAlokasiRow, 'Sisa Alokasi');
        $sheet->setCellValue('C' . $jumlahPengeluaranRow, 'Jumlah Pengeluaran Bulan Ini');
        $sheet->setCellValue('C' . $sisaSaldoRow, 'Sisa Saldo Bulan ini');

        foreach ($rincianAlokasi as $alokasi) {
            if (isset($kategoriColumnMap[$alokasi['master_kategori_id']])) {
                $colIndex = $kategoriColumnMap[$alokasi['master_kategori_id']];
                $colIndexStr = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colIndexStr . $alokasiRow, $alokasi['jumlah_alokasi']);
                $sheet->setCellValue($colIndexStr . $sisaAlokasiRow, $alokasi['sisa_alokasi']);
            }
        }
        $sheet->setCellValue($komulatifColStr . $jumlahPengeluaranRow, $laporan['total_pengeluaran']);
        $sheet->setCellValue($saldoColStr . $sisaSaldoRow, $laporan['saldo_akhir']);

        // 7. Blok Tanda Tangan (Tidak ada perubahan)
        $row = $sisaSaldoRow + 3;
        $sheet->mergeCells('A' . $row . ':D' . $row)->setCellValue('A' . $row, 'Mengetahui,');
        $sheet->mergeCells('A' . ($row + 1) . ':D' . ($row + 1))->setCellValue('A' . ($row + 1), 'Ketua BUMDES');
        $sheet->mergeCells('A' . ($row + 5) . ':D' . ($row + 5))->setCellValue('A' . ($row + 5), $ketua);
        $sheet->getStyle('A' . $row . ':D' . ($row + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . ($row + 5))->getFont()->setBold(true)->setUnderline(true);

        $startColKananIndex = max(6, $endColPengeluaran - 1);
        $startColKananStr = Coordinate::stringFromColumnIndex($startColKananIndex);
        $sheet->mergeCells($startColKananStr . $row . ':' . $endColTotalStr . $row)
            ->setCellValue($startColKananStr . $row, $lokasi . ', ' . date('d ') . $namaBulan . date(' Y'));
        $sheet->mergeCells($startColKananStr . ($row + 1) . ':' . $endColTotalStr . ($row + 1))
            ->setCellValue($startColKananStr . ($row + 1), 'Bendahara BUMDES');
        $sheet->mergeCells($startColKananStr . ($row + 5) . ':' . $endColTotalStr . ($row + 5))
            ->setCellValue($startColKananStr . ($row + 5), $bendahara);
        $sheet->getStyle($startColKananStr . $row . ':' . $endColTotalStr . ($row + 5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($startColKananStr . ($row + 5))->getFont()->setBold(true)->setUnderline(true);

        // 8. Styling (Sedikit Penyesuaian)
        $styleArray = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,],], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER,],];
        $sheet->getStyle('A' . $headerRow . ':' . $endColTotalStr . $sisaSaldoRow)->applyFromArray($styleArray);

        $sheet->getStyle('A' . $headerRow . ':' . $endColTotalStr . $subHeaderRow2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
        $sheet->getStyle('A' . $headerRow . ':' . $endColTotalStr . $subHeaderRow2)->getFont()->setBold(true);

        $sheet->getStyle('A' . $totalPendapatanRow . ':' . $endColTotalStr . $totalPendapatanRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $alokasiRow . ':' . $endColTotalStr . $sisaSaldoRow)->getFont()->setBold(true);
        $sheet->getStyle('D' . ($subHeaderRow2 + 1) . ':' . $endColTotalStr . $sisaSaldoRow)->getNumberFormat()->setFormatCode('#,##0');

        for ($i = 1; $i <= $endColTotal; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        // Tambahan: Atur tinggi baris header agar teks tidak terpotong
        $sheet->getRowDimension($headerRow)->setRowHeight(30);
        $sheet->getRowDimension($subHeaderRow1)->setRowHeight(30);
        $sheet->getRowDimension($subHeaderRow2)->setRowHeight(30);

        // 9. Siapkan nama file dan download (Tidak ada perubahan)
        $filename = 'BKU_Bulanan_' . $namaBulan . '_' . $laporan['tahun'] . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }


    /**
     * Menampilkan halaman detail laporan BKU
     */
    public function detail($id = null)
    {
        // Panggil semua model yang dibutuhkan
        $bkuModel = new BkuBulananModel();
        $detailPendapatanModel = new DetailPendapatanModel();
        $detailPengeluaranModel = new DetailPengeluaranModel();
        $detailAlokasiModel = new DetailAlokasiModel();

        $laporan = $bkuModel->find($id);
        if (!$laporan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Laporan BKU tidak ditemukan.');
        }

        // [PERBAIKAN] Hitung ulang total pendapatan untuk memastikan akurasi
        $laporan['total_pendapatan'] = (float)($laporan['saldo_bulan_lalu'] ?? 0) + (float)($laporan['penghasilan_bulan_ini'] ?? 0);

        $rincianPendapatan = $detailPendapatanModel
            ->select('detail_pendapatan.*, master_pendapatan.nama_pendapatan')
            ->join('master_pendapatan', 'master_pendapatan.id = detail_pendapatan.master_pendapatan_id')
            ->where('detail_pendapatan.bku_id', $id)->findAll();

        $rincianPengeluaran = $detailPengeluaranModel
            ->select('detail_pengeluaran.*, master_kategori_pengeluaran.nama_kategori')
            ->join('master_kategori_pengeluaran', 'master_kategori_pengeluaran.id = detail_pengeluaran.master_kategori_id')
            ->where('detail_pengeluaran.bku_id', $id)->findAll();

        $rincianAlokasi = $detailAlokasiModel
            ->select('detail_alokasi.*, master_kategori_pengeluaran.nama_kategori')
            ->join('master_kategori_pengeluaran', 'master_kategori_pengeluaran.id = detail_alokasi.master_kategori_id')
            ->where('detail_alokasi.bku_id', $id)
            ->findAll();

        $data = [
            'title' => 'Detail Laporan BKU',
            'laporan' => $laporan,
            'rincianPendapatan' => $rincianPendapatan,
            'rincianPengeluaran' => $rincianPengeluaran,
            'rincianAlokasi' => $rincianAlokasi
        ];

        return view('dashboard_keuangan/bku_bulanan/detail', $data);
    }
    /**
     * Menghapus data laporan BKU
     */
    public function delete($id = null)
    {
        $bulanIndonesia = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $bkuModel = new BkuBulananModel();

        $data = $bkuModel->find($id);
        if ($data) {
            // Catat log sebelum data hilang
            $namaBulan = $bulanIndonesia[(int)$data['bulan']];
            $this->logAktivitas('MENGHAPUS', "Menghapus laporan BKU periode {$namaBulan} {$data['tahun']}", $id);
            $bkuModel->delete($id);
            session()->setFlashdata('success', 'Data laporan berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Data laporan tidak ditemukan.');
        }

        return redirect()->to('/bku-bulanan');
    }

    public function new()
    {
        // Panggil model yang datanya dibutuhkan di form
        $masterPendapatanModel = new MasterPendapatanModel();
        $masterKategoriModel = new MasterKategoriPengeluaranModel();

        $data = [
            'title' => 'Buat Laporan BKU Bulanan Baru',
            'master_pendapatan' => $masterPendapatanModel->findAll(),
            'master_kategori' => $masterKategoriModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('dashboard_keuangan/bku_bulanan/new', $data);
    }

    /**
     * Mengambil sisa saldo dari bulan sebelumnya via AJAX
     */
    public function getSaldoBulanLalu()
    {
        if ($this->request->isAJAX()) {
            $bulan = $this->request->getGet('bulan');
            $tahun = $this->request->getGet('tahun');

            $bulanSebelumnya = $bulan - 1;
            $tahunSebelumnya = $tahun;
            if ($bulanSebelumnya == 0) {
                $bulanSebelumnya = 12;
                $tahunSebelumnya = $tahun - 1;
            }

            $bkuModel = new BkuBulananModel();
            $laporanSebelumnya = $bkuModel->where('bulan', $bulanSebelumnya)
                ->where('tahun', $tahunSebelumnya)
                ->first();

            $saldo = 0.00;
            if ($laporanSebelumnya && isset($laporanSebelumnya['saldo_akhir'])) {
                $saldo = (float) $laporanSebelumnya['saldo_akhir'];
            }

            // [IMPLEMENTASI DEBUG] Siapkan data tambahan untuk diperiksa di browser console
            $debug_data = [
                'mencari_bulan' => $bulanSebelumnya,
                'mencari_tahun' => $tahunSebelumnya,
                'hasil_query_mentah' => $laporanSebelumnya // Kirim seluruh hasil query
            ];

            // Kirim respons JSON beserta data debug
            return $this->response->setJSON([
                'saldo' => $saldo,
                'debug' => $debug_data
            ]);
        }
        return $this->response->setStatusCode(403);
    }

    public function create()
    {
        $bulanIndonesia = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        // 1. Validasi Input Dasar
        $validation = \Config\Services::validation();
        $validation->setRules([
            'bulan' => 'required',
            'tahun' => 'required',
            'pendapatan' => 'required', // Pastikan minimal ada 1 baris pendapatan
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Ambil semua data dari form
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $pendapatanItems = $this->request->getPost('pendapatan');
        $pengeluaranItems = $this->request->getPost('pengeluaran');

        // Panggil semua model yang dibutuhkan
        $bkuModel = new BkuBulananModel();
        $detailPendapatanModel = new DetailPendapatanModel();
        $detailPengeluaranModel = new DetailPengeluaranModel();
        $detailAlokasiModel = new DetailAlokasiModel();
        $masterKategoriModel = new MasterKategoriPengeluaranModel();

        // Pengecekan data duplikat di awal sebelum transaksi
        $dataSudahAda = $bkuModel->where('bulan', $bulan)->where('tahun', $tahun)->first();
        if ($dataSudahAda) {
            return redirect()->back()->withInput()->with('error', "Maaf, data pada periode yang Anda pilih sudah dibuat.");
        }

        // Gunakan DB Transaction (Best Practice)
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Ambil Saldo Bulan Lalu (verifikasi sisi server)
            $bulanSebelumnya = $bulan - 1;
            $tahunSebelumnya = $tahun;
            if ($bulanSebelumnya == 0) {
                $bulanSebelumnya = 12;
                $tahunSebelumnya = $tahun - 1;
            }
            $laporanSebelumnya = $bkuModel->where('bulan', $bulanSebelumnya)->where('tahun', $tahunSebelumnya)->first();
            $saldoBulanLalu = $laporanSebelumnya['saldo_akhir'] ?? 0;

            // Hitung Penghasilan Bulan Ini (hanya dari item dinamis)
            $penghasilanBulanIni = 0;
            if ($pendapatanItems) {
                foreach ($pendapatanItems as $item) {
                    $penghasilanBulanIni += (float) preg_replace('/[^0-9]/', '', $item['jumlah']);
                }
            }

            // Hitung Total Pendapatan
            $totalPendapatan = $saldoBulanLalu + $penghasilanBulanIni;

            // Hitung total pengeluaran dan rekap per kategori
            $totalPengeluaran = 0;
            $pengeluaranPerKategori = [];
            if ($pengeluaranItems) {
                foreach ($pengeluaranItems as $item) {
                    $jumlahBersih = (float) preg_replace('/[^0-9]/', '', $item['jumlah']);
                    $totalPengeluaran += $jumlahBersih;
                    $kategoriId = $item['kategori_id'];
                    if (!isset($pengeluaranPerKategori[$kategoriId])) {
                        $pengeluaranPerKategori[$kategoriId] = 0;
                    }
                    $pengeluaranPerKategori[$kategoriId] += $jumlahBersih;
                }
            }

            $saldoAkhir = $totalPendapatan - $totalPengeluaran;

            $bkuModel->insert([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'saldo_bulan_lalu' => $saldoBulanLalu,
                'penghasilan_bulan_ini' => $penghasilanBulanIni,
                'total_pendapatan' => $totalPendapatan,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo_akhir' => $saldoAkhir,
            ]);

            $bkuId = $bkuModel->getInsertID();

            if ($pendapatanItems) {
                foreach ($pendapatanItems as $item) {
                    $detailPendapatanModel->insert([
                        'bku_id' => $bkuId,
                        'master_pendapatan_id' => $item['id'],
                        'jumlah' => (float) preg_replace('/[^0-9]/', '', $item['jumlah']),
                    ]);
                }
            }

            if ($pengeluaranItems) {
                foreach ($pengeluaranItems as $item) {
                    $detailPengeluaranModel->insert([
                        'bku_id' => $bkuId,
                        'master_kategori_id' => $item['kategori_id'],
                        'deskripsi_pengeluaran' => $item['deskripsi'],
                        'jumlah' => (float) preg_replace('/[^0-9]/', '', $item['jumlah']),
                    ]);
                }
            }

            $semuaKategori = $masterKategoriModel->findAll();
            foreach ($semuaKategori as $kategori) {
                $jumlahAlokasi = $totalPendapatan * ($kategori['persentase'] / 100);
                $jumlahRealisasi = $pengeluaranPerKategori[$kategori['id']] ?? 0.00;
                $sisaAlokasi = $jumlahAlokasi - $jumlahRealisasi;
                $detailAlokasiModel->insert([
                    'bku_id' => $bkuId,
                    'master_kategori_id' => $kategori['id'],
                    'persentase_saat_itu' => $kategori['persentase'],
                    'jumlah_alokasi' => $jumlahAlokasi,
                    'jumlah_realisasi' => $jumlahRealisasi,
                    'sisa_alokasi' => $sisaAlokasi,
                ]);
            }

            $db->transCommit();
            $namaBulan = $bulanIndonesia[(int)$bulan];
            $this->logAktivitas('MEMBUAT', "Membuat laporan BKU untuk periode {$namaBulan} {$tahun}", $bkuId);
            return redirect()->to('/bku-bulanan')->with('success', 'Laporan BKU berhasil dibuat!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $bkuModel = new BkuBulananModel();
        $detailPendapatanModel = new DetailPendapatanModel();
        $detailPengeluaranModel = new DetailPengeluaranModel();
        $masterPendapatanModel = new MasterPendapatanModel();
        $masterKategoriModel = new MasterKategoriPengeluaranModel();

        $laporan = $bkuModel->find($id);
        if (!$laporan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Laporan BKU tidak ditemukan.');
        }

        // Hitung ulang total pendapatan untuk memastikan data yang dikirim ke view akurat
        $laporan['total_pendapatan'] = (float)($laporan['saldo_bulan_lalu'] ?? 0) + (float)($laporan['penghasilan_bulan_ini'] ?? 0);

        $rincianPendapatan = $detailPendapatanModel->where('bku_id', $id)->findAll();
        $rincianPengeluaran = $detailPengeluaranModel->where('bku_id', $id)->findAll();

        // [FIX] Hapus bagian desimal dari jumlah untuk mencegah bug parsing di JavaScript
        foreach ($rincianPendapatan as &$item) {
            $item['jumlah'] = (int)$item['jumlah'];
        }
        foreach ($rincianPengeluaran as &$item) {
            $item['jumlah'] = (int)$item['jumlah'];
        }

        $data = [
            'title' => 'Edit Laporan BKU Bulanan',
            'laporan' => $laporan,
            'rincianPendapatan' => $rincianPendapatan,
            'rincianPengeluaran' => $rincianPengeluaran,
            'master_pendapatan' => $masterPendapatanModel->findAll(),
            'master_kategori' => $masterKategoriModel->findAll(),
            'validation' => \Config\Services::validation()
        ];

        return view('dashboard_keuangan/bku_bulanan/edit', $data);
    }

    public function update($id = null)
    {
        $bulanIndonesia = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        if (!$this->validate(['pendapatan' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Minimal harus ada satu baris pendapatan.');
        }

        $pendapatanItems = $this->request->getPost('pendapatan');
        $pengeluaranItems = $this->request->getPost('pengeluaran');

        $bkuModel = new BkuBulananModel();
        $detailPendapatanModel = new DetailPendapatanModel();
        $detailPengeluaranModel = new DetailPengeluaranModel();
        $detailAlokasiModel = new DetailAlokasiModel();
        $masterKategoriModel = new MasterKategoriPengeluaranModel();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $detailPendapatanModel->where('bku_id', $id)->delete();
            $detailPengeluaranModel->where('bku_id', $id)->delete();
            $detailAlokasiModel->where('bku_id', $id)->delete();

            $laporanLama = $bkuModel->find($id);
            $saldoBulanLalu = $laporanLama['saldo_bulan_lalu'] ?? 0;

            $penghasilanBulanIni = 0;
            if ($pendapatanItems) {
                foreach ($pendapatanItems as $item) {
                    $penghasilanBulanIni += (float) preg_replace('/[^0-9]/', '', $item['jumlah']);
                }
            }
            $totalPendapatan = $saldoBulanLalu + $penghasilanBulanIni;

            $totalPengeluaran = 0;
            $pengeluaranPerKategori = [];
            if ($pengeluaranItems) {
                foreach ($pengeluaranItems as $item) {
                    $jumlahBersih = (float) preg_replace('/[^0-9]/', '', $item['jumlah']);
                    $totalPengeluaran += $jumlahBersih;
                    $kategoriId = $item['kategori_id'];
                    if (!isset($pengeluaranPerKategori[$kategoriId])) {
                        $pengeluaranPerKategori[$kategoriId] = 0;
                    }
                    $pengeluaranPerKategori[$kategoriId] += $jumlahBersih;
                }
            }
            $saldoAkhir = $totalPendapatan - $totalPengeluaran;

            $bkuModel->update($id, [
                'penghasilan_bulan_ini' => $penghasilanBulanIni,
                'total_pendapatan' => $totalPendapatan,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo_akhir' => $saldoAkhir,
            ]);

            if ($pendapatanItems) {
                foreach ($pendapatanItems as $item) {
                    $detailPendapatanModel->insert([
                        'bku_id' => $id,
                        'master_pendapatan_id' => $item['id'],
                        'jumlah' => (float) preg_replace('/[^0-9]/', '', $item['jumlah']),
                    ]);
                }
            }
            if ($pengeluaranItems) {
                foreach ($pengeluaranItems as $item) {
                    $detailPengeluaranModel->insert([
                        'bku_id' => $id,
                        'master_kategori_id' => $item['kategori_id'],
                        'deskripsi_pengeluaran' => $item['deskripsi'],
                        'jumlah' => (float) preg_replace('/[^0-9]/', '', $item['jumlah']),
                    ]);
                }
            }

            $semuaKategori = $masterKategoriModel->findAll();
            foreach ($semuaKategori as $kategori) {
                $jumlahAlokasi = $totalPendapatan * ($kategori['persentase'] / 100);
                $jumlahRealisasi = $pengeluaranPerKategori[$kategori['id']] ?? 0.00;
                $sisaAlokasi = $jumlahAlokasi - $jumlahRealisasi;
                $detailAlokasiModel->insert([
                    'bku_id' => $id,
                    'master_kategori_id' => $kategori['id'],
                    'persentase_saat_itu' => $kategori['persentase'],
                    'jumlah_alokasi' => $jumlahAlokasi,
                    'jumlah_realisasi' => $jumlahRealisasi,
                    'sisa_alokasi' => $sisaAlokasi,
                ]);
            }

            $db->transCommit();
            $laporan = $bkuModel->find($id); // Ambil data untuk deskripsi log
            $namaBulan = $bulanIndonesia[(int)$laporan['bulan']];
            $this->logAktivitas('MENGUPDATE', "Memperbarui laporan BKU untuk periode {$namaBulan} {$laporan['tahun']}", $id);
            return redirect()->to('/bku-bulanan/detail/' . $id)->with('success', 'Laporan BKU berhasil diperbarui!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }
}
