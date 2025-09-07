<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BkuBulananModel;
use App\Models\MasterArusKasModel;
use App\Models\DetailArusKasModel;
use App\Models\RekapArusKasModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ArusKas extends BaseController
{
    protected $bkuModel;
    protected $masterModel;
    protected $detailModel;
    protected $rekapModel;
    protected $db;

    public function __construct()
    {
        $this->bkuModel = new BkuBulananModel();
        $this->masterModel = new MasterArusKasModel();
        $this->detailModel = new DetailArusKasModel();
        $this->rekapModel = new RekapArusKasModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Laporan Arus Kas',
            'daftar_tahun' => $this->bkuModel->select('tahun')->distinct()->orderBy('tahun', 'DESC')->findAll()
        ];
        $tahunDipilih = $this->request->getGet('tahun');
        if ($tahunDipilih) {
            $data['tahunDipilih'] = $tahunDipilih;
            $laporanData = $this->getArusKasData($tahunDipilih);
            $data = array_merge($data, $laporanData);
        }
        return view('dashboard_keuangan/arus_kas/index', $data);
    }

    private function getArusKasData($tahun)
    {
        $pendapatanUtama = (int) ($this->bkuModel->selectSum('penghasilan_bulan_ini')->where('tahun', $tahun)->get()->getRow()->penghasilan_bulan_ini ?? 0);
        $builder = $this->db->table('detail_alokasi as da');
        $builder->select('mk.nama_kategori, SUM(da.jumlah_realisasi) as total_per_kategori')->join('bku_bulanan as bb', 'bb.id = da.bku_id')->join('master_kategori_pengeluaran as mk', 'mk.id = da.master_kategori_id')->where('bb.tahun', $tahun)->groupBy('mk.nama_kategori');
        $pengeluaranBKU = $builder->get()->getResultArray();
        $pengeluaranMap = array_column($pengeluaranBKU, 'total_per_kategori', 'nama_kategori');
        $pembelianBarang = (int) ($pengeluaranMap['PENGEMBANGAN'] ?? 0);
        $bebanGaji = (int) ($pengeluaranMap['HONOR'] ?? 0);
        $pad = (int) ($pengeluaranMap['PAD'] ?? 0);

        $komponenMasuk = $this->masterModel->where('kategori', 'masuk')->findAll();
        $komponenKeluar = $this->masterModel->where('kategori', 'keluar')->findAll();
        $nilaiTersimpan = $this->detailModel->where('tahun', $tahun)->findAll();
        $nilaiTersimpanMap = array_column($nilaiTersimpan, 'jumlah', 'master_arus_kas_id');

        // [LOGIKA DIPERBAIKI] Tidak ada lagi logika khusus untuk Saldo Awal
        $totalDinamisMasuk = 0;
        foreach ($komponenMasuk as &$item) {
            $jumlah = (int) ($nilaiTersimpanMap[$item['id']] ?? 0);
            $item['jumlah'] = $jumlah;
            $totalDinamisMasuk += $jumlah; // Semua komponen masuk dijumlahkan
        }

        $totalDinamisKeluar = 0;
        foreach ($komponenKeluar as &$item) {
            $jumlah = (int) ($nilaiTersimpanMap[$item['id']] ?? 0);
            $item['jumlah'] = $jumlah;
            $totalDinamisKeluar += $jumlah;
        }

        // [LOGIKA DIPERBAIKI] Perhitungan total disederhanakan
        $totalKasMasuk = $pendapatanUtama + $totalDinamisMasuk;
        $totalKasKeluar = $pembelianBarang + $bebanGaji + $pad + $totalDinamisKeluar;
        $saldoAkhir = $totalKasMasuk - $totalKasKeluar;

        return [
            'pendapatanUtama' => $pendapatanUtama,
            'pembelianBarang' => $pembelianBarang,
            'bebanGaji' => $bebanGaji,
            'pad' => $pad,
            'komponenMasuk' => $komponenMasuk,
            'komponenKeluar' => $komponenKeluar,
            'totalKasMasuk' => $totalKasMasuk,
            'totalKasKeluar' => $totalKasKeluar,
            'saldoAkhir' => $saldoAkhir,
            'tahun' => $tahun
        ];
    }

    public function simpan()
    {
        $validation = $this->validate(['tahun' => 'required|exact_length[4]|numeric']);
        if (!$validation) {
            return redirect()->back()->withInput()->with('error', 'Tahun tidak valid.');
        }

        $tahun = $this->request->getPost('tahun');
        $jumlah = $this->request->getPost('jumlah') ?? [];

        $this->db->transStart();

        $this->detailModel->where('tahun', $tahun)->delete();
        if ($jumlah) {
            // [LOGIKA DIPERBAIKI] Logika 'if' yang mengecualikan Saldo Awal dihapus
            foreach ($jumlah as $master_id => $nilai_kotor) {
                $nilaiBersih = (float) preg_replace('/[^\d-]/', '', $nilai_kotor);
                if ($nilaiBersih >= 0) {
                    $this->detailModel->insert(['tahun' => $tahun, 'master_arus_kas_id' => $master_id, 'jumlah' => $nilaiBersih]);
                }
            }
        }

        $laporanData = $this->getArusKasData($tahun);
        $rekapData = ['tahun' => $tahun, 'total_kas_masuk' => $laporanData['totalKasMasuk'], 'total_kas_keluar' => $laporanData['totalKasKeluar'], 'saldo_akhir' => $laporanData['saldoAkhir'],];

        $existingRekap = $this->rekapModel->where('tahun', $tahun)->first();
        if ($existingRekap) {
            $rekapData['id'] = $existingRekap['id'];
        }
        $this->rekapModel->save($rekapData);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return redirect()->to('/arus-kas?tahun=' . $tahun)->with('error', 'Gagal menyimpan data laporan.');
        }

        return redirect()->to('/arus-kas?tahun=' . $tahun)->with('success', 'Laporan Arus Kas berhasil disimpan.');
    }

    public function exportExcel($tahun = null)
    {
        if (!$tahun) return redirect()->to('/arus-kas');
        $data = $this->getArusKasData($tahun);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arus Kas ' . $tahun);

        $headerStyle = ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F4F4F']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
        $totalRowStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9ECEF']]];
        $finalSaldoStyle = ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F4F4F']]];
        $allBorders = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]];

        $sheet->mergeCells('A1:C1')->setCellValue('A1', 'LAPORAN ARUS KAS');
        $sheet->mergeCells('A2:C2')->setCellValue('A2', 'PERIODE TAHUN ' . $tahun);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 4;
        $startDataRow = $row;

        $sheet->mergeCells('A' . $row . ':C' . $row)->setCellValue('A' . $row, 'ARUS KAS MASUK')->getStyle('A' . $row . ':C' . $row)->applyFromArray($headerStyle);
        $row++;
        $sheet->setCellValue('A' . $row, 'Penerimaan Pendapatan Operasional Utama')->setCellValue('C' . $row, $data['pendapatanUtama']);
        $row++;
        // [LOGIKA DIPERBAIKI] Loop sederhana untuk semua komponen masuk
        foreach ($data['komponenMasuk'] as $item) {
            $sheet->setCellValue('A' . $row, $item['nama_komponen'])->setCellValue('C' . $row, $item['jumlah']);
            $row++;
        }
        $sheet->setCellValue('B' . $row, 'Total Arus Kas Masuk')->setCellValue('C' . $row, $data['totalKasMasuk'])->getStyle('A' . $row . ':C' . $row)->applyFromArray($totalRowStyle);
        $row += 2;

        $sheet->mergeCells('A' . $row . ':C' . $row)->setCellValue('A' . $row, 'ARUS KAS KELUAR')->getStyle('A' . $row . ':C' . $row)->applyFromArray($headerStyle);
        $row++;
        $sheet->setCellValue('A' . $row, 'Pembelian Barang dan Jasa')->setCellValue('C' . $row, $data['pembelianBarang']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Pembayaran Beban Gaji')->setCellValue('C' . $row, $data['bebanGaji']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Pendapatan Asli Desa')->setCellValue('C' . $row, $data['pad']);
        $row++;
        foreach ($data['komponenKeluar'] as $item) {
            $sheet->setCellValue('A' . $row, $item['nama_komponen'])->setCellValue('C' . $row, $item['jumlah']);
            $row++;
        }
        $sheet->setCellValue('B' . $row, 'Total Arus Kas Keluar')->setCellValue('C' . $row, $data['totalKasKeluar'])->getStyle('A' . $row . ':C' . $row)->applyFromArray($totalRowStyle);
        $row += 2;

        $sheet->setCellValue('B' . $row, 'SALDO AKHIR')->setCellValue('C' . $row, $data['saldoAkhir'])->getStyle('A' . $row . ':C' . $row)->applyFromArray($finalSaldoStyle);
        $lastDataRow = $row;

        $sheet->getStyle('C' . ($startDataRow + 1) . ':C' . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $startDataRow . ':C' . $lastDataRow)->applyFromArray($allBorders);
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $filename = 'Laporan_Arus_Kas_' . $tahun . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function exportPdf($tahun = null)
    {
        if (!$tahun) return redirect()->to('/arus-kas');
        $data = $this->getArusKasData($tahun);
        $dompdf = new Dompdf();
        $html = view('dashboard_keuangan/arus_kas/cetak_pdf_modern', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = 'Laporan_Arus_Kas_' . $tahun . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 0]);
    }
}
