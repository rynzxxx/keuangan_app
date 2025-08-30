<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BkuBulananModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\PengaturanModel;
use App\Models\MasterKategoriPengeluaranModel;

class BkuTahunan extends BaseController
{
    /**
     * Menampilkan halaman utama dengan dropdown tahun.
     */
    public function index()
    {
        $bkuModel = new BkuBulananModel();

        $data = [
            'title' => 'Laporan BKU Tahunan',
            'daftar_tahun' => $bkuModel->select('tahun')->distinct()->orderBy('tahun', 'DESC')->findAll()
        ];

        $tahunDipilih = $this->request->getGet('tahun');

        if ($tahunDipilih) {
            $data['tahunDipilih'] = $tahunDipilih;
            // Panggil helper method untuk mendapatkan data yang sudah dihitung dengan benar
            $data['hasil'] = $this->getLaporanTahunanData($tahunDipilih);
        }

        return view('dashboard_keuangan/bku_tahunan/index', $data);
    }

    /**
     * [PERBAIKAN] Method helper untuk mengambil dan menghitung data laporan tahunan
     */
    private function getLaporanTahunanData($tahun)
    {
        $bkuModel = new BkuBulananModel();
        $db = \Config\Database::connect();

        // 1. Ambil saldo awal tahun dari laporan bulan pertama di tahun tersebut
        $laporanBulanPertama = $bkuModel->where('tahun', $tahun)->orderBy('bulan', 'ASC')->first();
        $saldoAwalTahun = $laporanBulanPertama['saldo_bulan_lalu'] ?? 0;

        // 2. Jumlahkan HANYA 'penghasilan_bulan_ini' selama setahun
        $totalPenghasilanSetahun = $bkuModel->selectSum('penghasilan_bulan_ini')->where('tahun', $tahun)->first()['penghasilan_bulan_ini'] ?? 0;

        // 3. Total Pendapatan Tahunan yang valid
        $totalPendapatanTahunan = $saldoAwalTahun + $totalPenghasilanSetahun;

        // 4. Hitung total pengeluaran per kategori (logika ini sudah benar)
        $builder = $db->table('detail_pengeluaran as dp');
        $builder->select('mkp.nama_kategori, SUM(dp.jumlah) as total_per_kategori');
        $builder->join('bku_bulanan as bb', 'bb.id = dp.bku_id');
        $builder->join('master_kategori_pengeluaran as mkp', 'mkp.id = dp.master_kategori_id');
        $builder->where('bb.tahun', $tahun);
        $builder->groupBy('dp.master_kategori_id');
        $pengeluaranPerKategori = $builder->get()->getResultArray();

        // 5. Hitung total semua pengeluaran (logika ini sudah benar)
        $totalPengeluaran = array_sum(array_column($pengeluaranPerKategori, 'total_per_kategori'));

        // 6. Hitung saldo akhir tahun dengan total pendapatan yang sudah benar
        $saldoAkhirTahun = $totalPendapatanTahunan - $totalPengeluaran;

        return [
            'totalPendapatan' => $totalPendapatanTahunan,
            'pengeluaranPerKategori' => $pengeluaranPerKategori,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoAkhirTahun' => $saldoAkhirTahun,
        ];
    }

    /**
     * Mencetak laporan tahunan dalam format PDF
     */
    public function cetakExcel($tahun = null)
    {
        if (!$tahun) {
            return redirect()->to('/bku-tahunan');
        }

        // 1. Ambil semua data yang dibutuhkan
        $hasil = $this->getLaporanTahunanData($tahun);
        $pengaturanModel = new PengaturanModel();
        $masterKategoriModel = new MasterKategoriPengeluaranModel();
        $masterKategori = $masterKategoriModel->findAll();
        $pengaturan = $pengaturanModel->getAllAsArray();

        // Menggunakan nilai dari array $pengaturan
        $ketua = $pengaturan['ketua_bumdes'] ?? 'NAMA KETUA';
        $bendahara = $pengaturan['bendahara_bumdes'] ?? 'NAMA BENDAHARA';
        $lokasi = $pengaturan['lokasi_laporan'] ?? 'LOKASI';
        $kepala_desa = $pengaturan['kepala_desa'] ?? 'Nama Kepala Desa';
        $penasihat = $pengaturan['penasihat'] ?? 'Nama Penasihat';
        $pengawas = $pengaturan['pengawas'] ?? 'Nama Pengawas';
        $bulanIndonesia = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        $spreadsheet = new Spreadsheet();
        /** @var Worksheet $sheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Tahunan ' . $tahun);

        // Konfigurasi Hierarki & Setup Kolom Dinamis (Diadaptasi dari BKU Bulanan)
        $konfigurasiHierarki = ['OPERASIONAL PENGELOLAAN' => ['KESEKRETARIATAN', 'PROMOSI']];
        $kategoriByName = array_column($masterKategori, null, 'nama_kategori');
        $semuaAnakKategori = [];
        foreach ($konfigurasiHierarki as $children) {
            $semuaAnakKategori = array_merge($semuaAnakKategori, $children);
        }
        $kategoriHierarki = [];
        foreach ($masterKategori as $kat) {
            $namaKategori = $kat['nama_kategori'];
            if (in_array($namaKategori, $semuaAnakKategori)) continue;
            if (isset($konfigurasiHierarki[$namaKategori])) {
                $childrenData = [];
                foreach ($konfigurasiHierarki[$namaKategori] as $namaAnak) {
                    if (isset($kategoriByName[$namaAnak])) $childrenData[] = $kategoriByName[$namaAnak];
                }
                $kat['children'] = $childrenData;
                $kategoriHierarki[] = $kat;
            } else {
                $kat['children'] = [];
                $kategoriHierarki[] = $kat;
            }
        }
        $totalKolomPengeluaran = count($masterKategori) - count($konfigurasiHierarki);
        $startColPengeluaran = 4; // Kolom D
        $endColPengeluaran = ($totalKolomPengeluaran > 0) ? $startColPengeluaran + $totalKolomPengeluaran - 1 : $startColPengeluaran - 1;
        $komulatifCol = $endColPengeluaran + 1;
        $saldoCol = $endColPengeluaran + 2;
        $endColTotal = $saldoCol;
        $endColTotalStr = Coordinate::stringFromColumnIndex($endColTotal);

        // Buat Judul Laporan
        $sheet->mergeCells('A1:' . $endColTotalStr . '1')->setCellValue('A1', 'BUKU KAS UMUM BADAN USAHA MILIK DESA (TAHUNAN)');
        $sheet->mergeCells('A2:' . $endColTotalStr . '2')->setCellValue('A2', '*BUMDES ALAM LESTARI*');
        $sheet->mergeCells('A3:' . $endColTotalStr . '3')->setCellValue('A3', 'DESA MELUNG KECAMATAN KEDUNG BANTENG');
        $sheet->mergeCells('A4:' . $endColTotalStr . '4')->setCellValue('A4', 'KABUPATEN BANYUMAS');
        $sheet->mergeCells('A5:' . $endColTotalStr . '5')->setCellValue('A5', 'PERIODE TAHUN ' . $tahun);
        $sheet->getStyle('A1:A5')->getFont()->setBold(true);
        $sheet->getStyle('A1:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat Header Tabel (Diadaptasi dari BKU Bulanan)
        $headerRow = 8;
        $subHeaderRow1 = 9;
        $subHeaderRow2 = 10;
        if ($totalKolomPengeluaran > 0) {
            $startColPengeluaranStr = Coordinate::stringFromColumnIndex($startColPengeluaran);
            $endColPengeluaranStr = Coordinate::stringFromColumnIndex($endColPengeluaran);
            $sheet->mergeCells($startColPengeluaranStr . $headerRow . ':' . $endColPengeluaranStr . $headerRow)->setCellValue($startColPengeluaranStr . $headerRow, 'PENGELUARAN');
        }
        $kategoriColumnMap = [];
        $currentColIndex = $startColPengeluaran;
        foreach ($kategoriHierarki as $parentKat) {
            $colStr = Coordinate::stringFromColumnIndex($currentColIndex);
            if (empty($parentKat['children'])) {
                $sheet->mergeCells($colStr . $subHeaderRow1 . ':' . $colStr . $subHeaderRow2)->setCellValue($colStr . $subHeaderRow1, strtoupper($parentKat['nama_kategori']) . "\n" . $parentKat['persentase'] . '%');
                $kategoriColumnMap[$parentKat['id']] = $currentColIndex;
                $currentColIndex++;
            } else {
                $childCount = count($parentKat['children']);
                $endMergeColIndex = $currentColIndex + $childCount - 1;
                $endMergeColStr = Coordinate::stringFromColumnIndex($endMergeColIndex);
                $sheet->mergeCells($colStr . $subHeaderRow1 . ':' . $endMergeColStr . $subHeaderRow1)->setCellValue($colStr . $subHeaderRow1, strtoupper($parentKat['nama_kategori']) . "\n" . $parentKat['persentase'] . '%');
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
        $sheet->mergeCells('B' . $headerRow . ':B' . $subHeaderRow2)->setCellValue('B' . $headerRow, 'URAIAN');
        $sheet->mergeCells('C' . $headerRow . ':C' . $subHeaderRow2)->setCellValue('C' . $headerRow, 'PENDAPATAN');
        $sheet->mergeCells($komulatifColStr . $headerRow . ':' . $komulatifColStr . $subHeaderRow2)->setCellValue($komulatifColStr . $headerRow, 'KOMULATIF PENGELUARAN');
        $sheet->mergeCells($saldoColStr . $headerRow . ':' . $saldoColStr . $subHeaderRow2)->setCellValue($saldoColStr . $headerRow, 'SALDO');

        // Isi Data Baris per Baris (Logika Baru untuk Tahunan)
        $row = $subHeaderRow2 + 1;
        $nomor = 1;
        $saldo = 0;
        $komulatifPengeluaran = 0;
        $sheet->setCellValue('A' . $row, $nomor++)->setCellValue('B' . $row, "Akumulasi Pendapatan Tahun " . $tahun)->setCellValue('C' . $row, $hasil['totalPendapatan']);
        $saldo = (float)$hasil['totalPendapatan'];
        $sheet->setCellValue($saldoColStr . $row, $saldo);
        $row++;

        // Buat peta realisasi untuk lookup yang mudah
        $realisasiMap = array_column($hasil['pengeluaranPerKategori'], 'total_per_kategori', 'nama_kategori');

        // Total Pendapatan
        $totalPendapatanRow = $row;
        $sheet->setCellValue('B' . $row, 'Total Pendapatan Tahun Ini')->setCellValue('C' . $row, $hasil['totalPendapatan']);
        foreach ($masterKategori as $kat) {
            if (isset($kategoriColumnMap[$kat['id']])) {
                $colIndex = $kategoriColumnMap[$kat['id']];
                $alokasi = $hasil['totalPendapatan'] * ($kat['persentase'] / 100);
                $colStr = Coordinate::stringFromColumnIndex((int) $colIndex);
                $sheet->setCellValue($colStr . (int) $row, $alokasi);
            }
        }
        $row++;

        // Pengeluaran (sebagai transaksi)
        foreach ($masterKategori as $kat) {
            $realisasi = $realisasiMap[$kat['nama_kategori']] ?? 0;
            if ($realisasi > 0) {
                $sheet->setCellValue('A' . $row, $nomor++)->setCellValue('B' . $row, "Akumulasi Pengeluaran: " . $kat['nama_kategori']);
                if (isset($kategoriColumnMap[$kat['id']])) {
                    $colIndex = $kategoriColumnMap[$kat['id']];
                    $colStr = Coordinate::stringFromColumnIndex((int) $colIndex);
                    $sheet->setCellValue($colStr . (int) $row, $realisasi);
                }
                $komulatifPengeluaran += (float)$realisasi;
                $saldo -= (float)$realisasi;
                $sheet->setCellValue($komulatifColStr . $row, $komulatifPengeluaran);
                $sheet->setCellValue($saldoColStr . $row, $saldo);
                $row++;
            }
        }

        // Ringkasan Bawah
        $row++;
        $alokasiRow = $row;
        $sisaAlokasiRow = $row + 1;
        $jumlahPengeluaranRow = $row + 2;
        $sisaSaldoRow = $row + 3;

        $sheet->setCellValue('B' . $alokasiRow, 'Alokasi');
        $sheet->setCellValue('B' . $sisaAlokasiRow, 'Sisa Alokasi');
        $sheet->setCellValue('B' . $jumlahPengeluaranRow, 'Jumlah Pengeluaran Tahun Ini');
        $sheet->setCellValue('B' . $sisaSaldoRow, 'Sisa Saldo Tahun ini');

        $sheet->getStyle('B' . $alokasiRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . $sisaAlokasiRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . $jumlahPengeluaranRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . $sisaSaldoRow)->getFont()->setBold(true);

        foreach ($masterKategori as $kat) {
            if (isset($kategoriColumnMap[$kat['id']])) {
                $colIndex = $kategoriColumnMap[$kat['id']];
                $alokasi = $hasil['totalPendapatan'] * ($kat['persentase'] / 100);
                $realisasi = $realisasiMap[$kat['nama_kategori']] ?? 0;
                $sisa = $alokasi - $realisasi;
                $colStr = Coordinate::stringFromColumnIndex((int) $colIndex);

                $sheet->setCellValue($colStr . (int) $alokasiRow, $alokasi);
                $sheet->getStyle($colStr . (int) $alokasiRow)->getFont()->setBold(true);

                $sheet->setCellValue($colStr . (int) $sisaAlokasiRow, $sisa);
                $sheet->getStyle($colStr . (int) $sisaAlokasiRow)->getFont()->setBold(true);
            }
        }
        $sheet->setCellValue($komulatifColStr . $jumlahPengeluaranRow, $hasil['totalPengeluaran']);
        $sheet->getStyle($komulatifColStr . $jumlahPengeluaranRow)->getFont()->setBold(true);

        $sheet->setCellValue($saldoColStr . $sisaSaldoRow, $hasil['saldoAkhirTahun']);
        $sheet->getStyle($saldoColStr . $sisaSaldoRow)->getFont()->setBold(true);

        $lastDataRow = $sisaSaldoRow;

        // Blok Tanda Tangan
        // Perubahan di sini: Mengatur posisi awal blok tanda tangan
        $row = $lastDataRow + 2; // Posisi 2 baris di bawah tabel

        // Perubahan: Menambahkan tanggal dinamis
        $tanggalSekarang = date('j') . ' ' . $bulanIndonesia[date('n')] . ' ' . date('Y');
        $sheet->setCellValue('J' . $row, $lokasi . ', ' . $tanggalSekarang);
        $row++;
        // Perubahan: Mengubah cell untuk jabatan menjadi string
        $sheet->setCellValue('B' . $row, 'Mengetahui Kepala Desa Melung');
        // Ganti nilai string statis dengan variabel dinamis
        $sheet->setCellValue('B' . ($row + 3), $kepala_desa);

        // Dan seterusnya untuk jabatan lainnya...
        $sheet->setCellValue('D' . $row, 'Penasihat');
        $sheet->setCellValue('D' . ($row + 3), $penasihat);

        $sheet->setCellValue('F' . $row, 'Pengawas');
        $sheet->setCellValue('F' . ($row + 3), $pengawas);

        $sheet->setCellValue('H' . $row, 'Ketua BUM-Des');
        $sheet->setCellValue('H' . ($row + 3), $ketua);

        $sheet->setCellValue('J' . $row, 'Bendahara BUMDES');
        $sheet->setCellValue('J' . ($row + 3), $bendahara);

        // Perubahan: Mengatur batas akhir tabel hanya sampai data terakhir
        $lastDataRow = $sisaSaldoRow;

        // Styling
        $styleArray = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]];
        // Perubahan di sini: Hanya menerapkan styling border pada tabel utama
        $sheet->getStyle('A' . $headerRow . ':' . $endColTotalStr . $lastDataRow)->applyFromArray($styleArray);

        // Tambahan untuk perataan teks pada header kolom
        $headerRange = 'A' . $headerRow . ':' . $endColTotalStr . $subHeaderRow2;
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);

        // Tambahan untuk auto-sizing lebar kolom
        for ($col = 1; $col <= $endColTotal; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // Tambahan untuk mengatur tinggi baris header agar teks tidak terpotong
        $sheet->getRowDimension($headerRow)->setRowHeight(30);
        $sheet->getRowDimension($subHeaderRow1)->setRowHeight(30);
        $sheet->getRowDimension($subHeaderRow2)->setRowHeight(30);

        // Finalisasi & Download
        $filename = 'Laporan_Tahunan_BUMDES_' . $tahun . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
