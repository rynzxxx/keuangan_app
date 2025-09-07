<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BkuBulananModel;
use App\Models\MasterLabaRugiModel;
use App\Models\DetailLabaRugiModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\LabaRugiTahunModel;

class LabaRugi extends BaseController
{
    public function index()
    {
        $bkuModel = new BkuBulananModel();

        $data = [
            'title' => 'Laporan Laba Rugi',
            'daftar_tahun' => $bkuModel->select('tahun')->distinct()->orderBy('tahun', 'DESC')->findAll()
        ];

        $tahunDipilih = $this->request->getGet('tahun');

        if ($tahunDipilih) {
            $data['tahunDipilih'] = $tahunDipilih;
            $laporanData = $this->getLaporanLabaRugiData($tahunDipilih);
            $data = array_merge($data, $laporanData);
        }

        return view('dashboard_keuangan/laba_rugi/index', $data);
    }

    private function getLaporanLabaRugiData($tahun)
    {
        $bkuModel = new BkuBulananModel();
        $masterLabaRugiModel = new MasterLabaRugiModel();
        $detailLabaRugiModel = new DetailLabaRugiModel(); // Model baru
        $db = \Config\Database::connect();

        // Ambil data permanen dari BKU
        $totalPenghasilanSetahun = $bkuModel->selectSum('penghasilan_bulan_ini')->where('tahun', $tahun)->get()->getRow()->penghasilan_bulan_ini ?? 0;
        $builder = $db->table('detail_alokasi as da');
        $builder->select('mk.nama_kategori, SUM(da.jumlah_realisasi) as total_per_kategori');
        $builder->join('bku_bulanan as bb', 'bb.id = da.bku_id');
        $builder->join('master_kategori_pengeluaran as mk', 'mk.id = da.master_kategori_id');
        $builder->where('bb.tahun', $tahun);
        $builder->groupBy('mk.nama_kategori');
        $pengeluaranBKU = $builder->get()->getResultArray();
        $pengeluaranBKUMap = array_column($pengeluaranBKU, 'total_per_kategori', 'nama_kategori');

        // Ambil komponen dinamis dari master
        $komponenPendapatan = $masterLabaRugiModel->where('kategori', 'pendapatan')->findAll();
        $komponenBiaya = $masterLabaRugiModel->where('kategori', 'biaya')->findAll();

        // [LOGIKA BARU] Ambil nilai yang sudah tersimpan untuk tahun ini
        $nilaiTersimpan = $detailLabaRugiModel->where('tahun', $tahun)->findAll();
        $nilaiTersimpanMap = array_column($nilaiTersimpan, 'jumlah', 'master_laba_rugi_id');

        // [LOGIKA BARU] Sisipkan nilai yang tersimpan ke dalam array komponen
        foreach ($komponenPendapatan as &$item) {
            $item['jumlah'] = $nilaiTersimpanMap[$item['id']] ?? 0;
        }
        foreach ($komponenBiaya as &$item) {
            $item['jumlah'] = $nilaiTersimpanMap[$item['id']] ?? 0;
        }

        return [
            'pendapatanUsaha' => $totalPenghasilanSetahun,
            'biayaBahanBaku' => $pengeluaranBKUMap['PENGEMBANGAN'] ?? 0,
            'biayaGaji' => $pengeluaranBKUMap['HONOR'] ?? 0,
            'pad' => $pengeluaranBKUMap['PAD'] ?? 0,
            'komponenPendapatan' => $komponenPendapatan,
            'komponenBiaya' => $komponenBiaya,
        ];
    }

    /**
     * [BARU] Method untuk menyimpan data input dari form Laba Rugi
     */
    // Di dalam Controller Anda (misal: LaporanController.php)

    public function simpan()
    {
        $tahun = $this->request->getPost('tahun');
        $jumlah = $this->request->getPost('jumlah');

        if (empty($tahun)) {
            return redirect()->to('/laba-rugi')->with('error', 'Tahun tidak valid.');
        }

        $detailLabaRugiModel = new DetailLabaRugiModel();
        $db = \Config\Database::connect();

        $db->transStart();

        // 1. Simpan data detail (tidak ada perubahan di sini)
        $detailLabaRugiModel->where('tahun', $tahun)->delete();
        if ($jumlah) {
            foreach ($jumlah as $master_id => $nilai) {
                $nilaiBersih = (float) preg_replace('/[^0-9]/', '', $nilai);
                if ($nilaiBersih > 0) {
                    $detailLabaRugiModel->insert([
                        'tahun' => $tahun,
                        'master_laba_rugi_id' => $master_id,
                        'jumlah' => $nilaiBersih
                    ]);
                }
            }
        }

        if ($db->transStatus() === FALSE) {
            $db->transRollback();
            return redirect()->to('/laba-rugi?tahun=' . $tahun)->with('error', 'Gagal menyimpan data detail.');
        } else {
            $db->transCommit();
        }

        // 2. [LANGKAH BARU] Panggil fungsi untuk memperbarui ringkasan
        $this->perbaruiLabaRugiTahun($tahun);

        // 3. Redirect
        return redirect()->to('/laba-rugi?tahun=' . $tahun)->with('success', 'Data Laba Rugi berhasil disimpan dan ringkasan tahunan telah diperbarui.');
    }

    private function perbaruiLabaRugiTahun($tahun)
    {
        // 1. Dapatkan semua komponen data
        $dataLaporan = $this->getLaporanLabaRugiData($tahun);

        // 2. Kalkulasi Total Pendapatan
        $totalPendapatan = $dataLaporan['pendapatanUsaha'];
        foreach ($dataLaporan['komponenPendapatan'] as $pendapatan) {
            $totalPendapatan += $pendapatan['jumlah'];
        }

        // 3. Kalkulasi Total Biaya
        $totalBiaya = $dataLaporan['biayaBahanBaku']
            + $dataLaporan['biayaGaji']
            + $dataLaporan['pad'];
        foreach ($dataLaporan['komponenBiaya'] as $biaya) {
            $totalBiaya += $biaya['jumlah'];
        }

        // 4. Kalkulasi Laba Rugi Bersih
        $labaRugiBersih = $totalPendapatan - $totalBiaya;

        // 5. Siapkan data
        $dataToSave = [
            'tahun'            => $tahun,
            'total_pendapatan' => $totalPendapatan,
            'total_biaya'      => $totalBiaya,
            'laba_rugi_bersih' => $labaRugiBersih,
        ];

        // 6. Simpan ke database (Update atau Insert)
        $labaRugiTahunModel = new LabaRugiTahunModel(); // Ganti nama model
        $existingData = $labaRugiTahunModel->where('tahun', $tahun)->first();

        if ($existingData) {
            $labaRugiTahunModel->update($existingData['id'], $dataToSave);
        } else {
            $labaRugiTahunModel->insert($dataToSave);
        }
    }


    public function cetakPdf($tahun = null)
    {
        if (!$tahun) return redirect()->to('/laba-rugi');

        $data = $this->getLaporanLabaRugiData($tahun);
        $data['title'] = "Laporan Laba Rugi Tahun {$tahun}";
        $data['tahunDipilih'] = $tahun;

        $filename = 'Laporan_Laba_Rugi_' . $tahun . '.pdf';
        $html = view('dashboard_keuangan/laba_rugi/cetak_pdf', $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => false]); // Pratinjau di browser
    }

    /**
     * [BARU] Method untuk membuat dan men-download laporan Laba Rugi dalam format Excel
     */
    public function cetakExcel($tahun = null)
    {
        if (!$tahun) return redirect()->to('/laba-rugi');

        $data = $this->getLaporanLabaRugiData($tahun);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laba Rugi ' . $tahun);

        // Judul
        $sheet->mergeCells('A1:B1')->setCellValue('A1', 'LAPORAN LABA RUGI');
        $sheet->mergeCells('A2:B2')->setCellValue('A2', 'PERIODE TAHUN ' . $tahun);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 4;
        // PENDAPATAN
        $sheet->mergeCells('A' . $row . ':B' . $row)->setCellValue('A' . $row, 'PENDAPATAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $startDataRow = $row;
        $sheet->setCellValue('A' . $row, 'Pendapatan Usaha');
        $sheet->setCellValue('B' . $row, $data['pendapatanUsaha']);
        $row++;
        $totalPendapatanLain = 0;
        foreach ($data['komponenPendapatan'] as $item) {
            $sheet->setCellValue('A' . $row, $item['nama_komponen']);
            $sheet->setCellValue('B' . $row, $item['jumlah']);
            $totalPendapatanLain += $item['jumlah'];
            $row++;
        }
        $totalPendapatan = $data['pendapatanUsaha'] + $totalPendapatanLain;
        $totalPendapatanRow = $row;
        $sheet->setCellValue('A' . $row, 'TOTAL PENDAPATAN');
        $sheet->setCellValue('B' . $row, $totalPendapatan);
        $row += 2;

        // BIAYA
        $sheet->mergeCells('A' . $row . ':B' . $row)->setCellValue('A' . $row, 'BIAYA-BIAYA');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Biaya Bahan Baku');
        $sheet->setCellValue('B' . $row, $data['biayaBahanBaku']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Biaya Gaji');
        $sheet->setCellValue('B' . $row, $data['biayaGaji']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Pendapatan Asli Desa (PAD)');
        $sheet->setCellValue('B' . $row, $data['pad']);
        $row++;
        $totalBiayaLain = 0;
        foreach ($data['komponenBiaya'] as $item) {
            $sheet->setCellValue('A' . $row, $item['nama_komponen']);
            $sheet->setCellValue('B' . $row, $item['jumlah']);
            $totalBiayaLain += $item['jumlah'];
            $row++;
        }
        $totalBiaya = $data['biayaBahanBaku'] + $data['biayaGaji'] + $data['pad'] + $totalBiayaLain;
        $totalBiayaRow = $row;
        $sheet->setCellValue('A' . $row, 'TOTAL BIAYA');
        $sheet->setCellValue('B' . $row, $totalBiaya);
        $row++;

        // LABA / RUGI
        $labaRugi = $totalPendapatan - $totalBiaya;
        $labaRugiRow = $row;
        $sheet->setCellValue('A' . $row, 'LABA / (RUGI) BERSIH');
        $sheet->setCellValue('B' . $row, $labaRugi);
        $lastDataRow = $row;

        // --- STYLING ---
        // Font Bold untuk Total
        $sheet->getStyle('A' . $totalPendapatanRow . ':B' . $totalPendapatanRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $totalBiayaRow . ':B' . $totalBiayaRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $labaRugiRow . ':B' . $labaRugiRow)->getFont()->setBold(true);

        // Format Angka
        $sheet->getStyle('B' . $startDataRow . ':B' . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Border untuk seluruh tabel
        $styleArray = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]];
        $sheet->getStyle('A4:B' . $lastDataRow)->applyFromArray($styleArray);

        // Warna Latar Belakang
        $totalFill = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE9ECEF']]]; // Abu-abu muda
        $sheet->getStyle('A' . $totalPendapatanRow . ':B' . $totalPendapatanRow)->applyFromArray($totalFill);
        $sheet->getStyle('A' . $totalBiayaRow . ':B' . $totalBiayaRow)->applyFromArray($totalFill);

        if ($labaRugi >= 0) {
            $labaRugiFill = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'D4EDDA']]]; // Hijau muda
        } else {
            $labaRugiFill = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'F8D7DA']]]; // Merah muda
        }
        $sheet->getStyle('A' . $labaRugiRow . ':B' . $labaRugiRow)->applyFromArray($labaRugiFill);

        // Atur lebar kolom
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(25);

        // Download
        $filename = 'Laporan_Laba_Rugi_' . $tahun . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function delete($id)
    {
        // Cari komponen berdasarkan ID, jika tidak ada tampilkan error
        if (!$this->masterLabaRugiModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data komponen tidak ditemukan.');
        }

        // Hapus data dari database
        $this->masterLabaRugiModel->delete($id);

        // Siapkan pesan sukses
        session()->setFlashdata('success', 'Komponen berhasil dihapus.');

        // Kembalikan ke halaman index
        return redirect()->to('/master-laba-rugi');
    }
}
