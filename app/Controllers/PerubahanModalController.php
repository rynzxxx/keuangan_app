<?php

namespace App\Controllers;

use App\Models\MasterPerubahanModalModel;
use App\Models\DetailPerubahanModalModel;
use App\Models\LabaRugiTahunModel;
use App\Models\BkuBulananModel; // Ditambahkan untuk mengambil daftar tahun
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class PerubahanModalController extends BaseController
{
    // [BEST PRACTICE] Inisialisasi semua model dan properti di sini
    protected $masterModel;
    protected $detailModel;
    protected $labaRugiModel;
    protected $bkuModel;
    protected $db;

    public function __construct()
    {
        $this->masterModel = new MasterPerubahanModalModel();
        $this->detailModel = new DetailPerubahanModalModel();
        $this->labaRugiModel = new LabaRugiTahunModel();
        $this->bkuModel = new BkuBulananModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // [BEST PRACTICE] Dropdown tahun dinamis berdasarkan data yang ada
        $daftarTahun = $this->bkuModel->select('tahun')->distinct()->orderBy('tahun', 'DESC')->findAll();

        // Tentukan tahun terpilih: dari URL, atau tahun terbaru yang ada data, atau tahun ini
        $tahunTerbaru = !empty($daftarTahun) ? $daftarTahun[0]['tahun'] : date('Y');
        $tahunTerpilih = $this->request->getGet('tahun') ?? $tahunTerbaru;

        // Ambil semua data yang relevan untuk laporan
        $laporanData = $this->_getLaporanData($tahunTerpilih);

        $data = [
            'title'             => 'Laporan Perubahan Modal',
            'daftar_tahun'      => $daftarTahun,
            'tahun_terpilih'    => $tahunTerpilih,
            'komponen'          => $laporanData['semua_komponen'],
            'laba_rugi_bersih'  => $laporanData['laba_rugi_bersih'],
            'detail_map'        => $laporanData['detail_map'],
        ];

        return view('dashboard_keuangan/perubahan_modal/index', $data);
    }

    public function simpan()
    {
        $validation = $this->validate([
            'tahun' => 'required|exact_length[4]|numeric'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('error', 'Tahun tidak valid.');
        }

        $tahun = $this->request->getPost('tahun');
        $jumlah = $this->request->getPost('jumlah') ?? [];
        $labaRugiBersih = (float) $this->request->getPost('laba_rugi_bersih');

        $this->db->transStart();

        // 1. Hapus data detail lama
        $this->detailModel->where('tahun', $tahun)->delete();

        $totalPenambahan = $labaRugiBersih;
        $totalPengurangan = 0;

        $komponenMaster = $this->masterModel->findAll();
        $kategoriMap = array_column($komponenMaster, 'kategori', 'id');

        // 2. Simpan data detail baru dan hitung total
        if (!empty($jumlah)) {
            foreach ($jumlah as $master_id => $nilai) {
                $nilaiBersih = (float) preg_replace('/[^\d-]/', '', $nilai);
                if ($nilaiBersih != 0) {
                    $this->detailModel->insert([
                        'tahun' => $tahun,
                        'master_perubahan_modal_id' => $master_id,
                        'jumlah' => $nilaiBersih
                    ]);

                    if (isset($kategoriMap[$master_id])) {
                        if ($kategoriMap[$master_id] == 'penambahan') {
                            $totalPenambahan += $nilaiBersih;
                        } else {
                            $totalPengurangan += $nilaiBersih;
                        }
                    }
                }
            }
        }

        // 3. Hitung Saldo Modal Akhir
        $saldoModalAkhir = $totalPenambahan - $totalPengurangan;

        // 4. Simpan/Update Saldo Modal Akhir ke tabel ringkasan
        $existingData = $this->labaRugiModel->where('tahun', $tahun)->first();
        if ($existingData) {
            $this->labaRugiModel->update($existingData['id'], ['saldo_modal_akhir' => $saldoModalAkhir]);
        } else {
            $this->labaRugiModel->insert([
                'tahun' => $tahun,
                'laba_rugi_bersih' => $labaRugiBersih,
                'saldo_modal_akhir' => $saldoModalAkhir
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return redirect()->to('/perubahan-modal?tahun=' . $tahun)->with('error', 'Gagal menyimpan data.');
        }

        return redirect()->to('/perubahan-modal?tahun=' . $tahun)->with('success', 'Laporan Perubahan Modal berhasil disimpan.');
    }

    // Metode privat untuk mengambil data laporan secara terpusat
    private function _getLaporanData($tahun)
    {
        $semuaKomponen = $this->masterModel->orderBy('kategori', 'ASC')->findAll();
        $labaRugiData = $this->labaRugiModel->where('tahun', $tahun)->first();
        $labaRugiBersih = (int) ($labaRugiData['laba_rugi_bersih'] ?? 0);
        $detailTersimpan = $this->detailModel->where('tahun', $tahun)->findAll();
        $detailMap = array_column($detailTersimpan, 'jumlah', 'master_perubahan_modal_id');

        $penambahan = [];
        $pengurangan = [];
        $totalPenambahan = $labaRugiBersih;
        $totalPengurangan = 0;

        foreach ($semuaKomponen as $item) {
            $jumlah = (float) ($detailMap[$item['id']] ?? 0);
            $item['jumlah'] = $jumlah;
            if ($item['kategori'] == 'penambahan') {
                $penambahan[] = $item;
                $totalPenambahan += $jumlah;
            } else {
                $pengurangan[] = $item;
                $totalPengurangan += $jumlah;
            }
        }

        return [
            'tahun' => $tahun,
            'laba_rugi_bersih' => $labaRugiBersih,
            'semua_komponen' => $semuaKomponen,
            'komponen_penambahan' => $penambahan,
            'komponen_pengurangan' => $pengurangan,
            'detail_map' => $detailMap,
            'modal_akhir' => $totalPenambahan - $totalPengurangan
        ];
    }

    public function exportExcel($tahun)
    {
        $data = $this->_getLaporanData($tahun);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:C1')->setCellValue('A1', 'Laporan Perubahan Modal');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('A2:C2')->setCellValue('A2', 'Periode Tahun ' . $data['tahun']);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // Header Tabel
        $sheet->setCellValue('A4', 'Keterangan')->setCellValue('B4', 'Jumlah (Rp)');
        $sheet->getStyle('A4:B4')->getFont()->setBold(true);

        $row = 5;

        // PENAMBAHAN
        $sheet->setCellValue('A' . $row, 'Penambahan:')->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, '  Laba Bersih Tahun Berjalan')->setCellValue('B' . $row, $data['laba_rugi_bersih']);
        $row++;
        foreach ($data['komponen_penambahan'] as $item) {
            $sheet->setCellValue('A' . $row, '  ' . $item['nama_komponen'])->setCellValue('B' . $row, $item['jumlah']);
            $row++;
        }

        // PENGURANGAN
        $row++;
        $sheet->setCellValue('A' . $row, 'Pengurangan:')->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        foreach ($data['komponen_pengurangan'] as $item) {
            $sheet->setCellValue('A' . $row, '  ' . $item['nama_komponen'])->setCellValue('B' . $row, $item['jumlah']);
            $row++;
        }

        // MODAL AKHIR
        $row++;
        $sheet->setCellValue('A' . $row, 'Modal Akhir (per 31 Desember ' . $data['tahun'] . ')')->setCellValue('B' . $row, $data['modal_akhir']);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3');

        // Styling
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getStyle('B4:B' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A4:B' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Output
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-perubahan-modal-' . $data['tahun'] . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function exportPdf($tahun)
    {
        $data = $this->_getLaporanData($tahun);
        $dompdf = new Dompdf();
        $html = view('dashboard_keuangan/perubahan_modal/cetak_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = 'laporan-perubahan-modal-' . $data['tahun'] . '.pdf';
        $dompdf->stream($filename, ['Attachment' => 0]);
    }
}
