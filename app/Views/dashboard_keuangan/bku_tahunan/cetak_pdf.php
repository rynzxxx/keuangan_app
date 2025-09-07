<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Tahunan BUMDES <?= $tahun; ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .header-section p {
            margin: 0;
        }

        .table-container {
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            white-space: pre-line;
        }

        .rowspan-header {
            background-color: #f2f2f2;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 30px;
            width: 100%;
            font-size: 10px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            border: none;
            text-align: center;
            padding: 0;
        }

        .signature-space {
            height: 60px;
        }
    </style>
</head>

<body>

    <div class="header-section">
        <p>BUKU KAS UMUM BADAN USAHA MILIK DESA (TAHUNAN)</p>
        <p>*BUMDES ALAM LESTARI*</p>
        <p>DESA MELUNG KECAMATAN KEDUNG BANTENG</p>
        <p>KABUPATEN BANYUMAS</p>
        <p>PERIODE TAHUN <?= $tahun; ?></p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th rowspan="3" class="rowspan-header">NO</th>
                    <th rowspan="3" class="rowspan-header">URAIAN</th>
                    <th rowspan="3" class="rowspan-header">PENDAPATAN</th>
                    <th colspan="<?= $totalKolomPengeluaran; ?>">PENGELUARAN</th>
                    <th rowspan="3" class="rowspan-header">KOMULATIF<br>PENGELUARAN</th>
                    <th rowspan="3" class="rowspan-header">SALDO</th>
                </tr>
                <tr>
                    <?php foreach ($kategoriHierarki as $parentKat) : ?>
                        <?php if (!empty($parentKat['children'])) : ?>
                            <th colspan="<?= count($parentKat['children']); ?>"><?= strtoupper($parentKat['nama_kategori']); ?> <?= $parentKat['persentase']; ?>%</th>
                        <?php else : ?>
                            <th rowspan="2"><?= strtoupper($parentKat['nama_kategori']); ?> <?= $parentKat['persentase']; ?>%</th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <?php foreach ($kategoriHierarki as $parentKat) : ?>
                        <?php if (!empty($parentKat['children'])) : ?>
                            <?php foreach ($parentKat['children'] as $childKat) : ?>
                                <th><?= strtoupper($childKat['nama_kategori']); ?> <?= $childKat['persentase']; ?>%</th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php $nomor = 1; // Inisialisasi nomor 
                ?>
                <tr>
                    <td><?= $nomor++; ?></td>
                    <td class="text-left">Akumulasi Pendapatan Tahun <?= $tahun; ?></td>
                    <td class="text-right"><?= number_format($hasil['totalPendapatan'], 0, ',', '.'); ?></td>
                    <td colspan="<?= $totalKolomPengeluaran; ?>"></td>
                    <td></td>
                    <td class="text-right font-bold"><?= number_format($hasil['totalPendapatan'], 0, ',', '.'); ?></td>
                </tr>
                <tr class="font-bold">
                    <td><?= $nomor++; ?></td>
                    <td class="text-left">Alokasi Pendapatan</td>
                    <td></td>
                    <?php foreach (array_values($kategoriColumnMap) as $kat) : ?>
                        <td class="text-right"><?= number_format($hasil['totalPendapatan'] * ($kat['persentase'] / 100), 0, ',', '.'); ?></td>
                    <?php endforeach; ?>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?= $nomor++; ?></td>
                    <td class="text-left">Realisasi Pengeluaran</td>
                    <td></td>
                    <?php foreach (array_values($kategoriColumnMap) as $kat) : ?>
                        <?php $realisasi = $realisasiMap[$kat['nama_kategori']] ?? 0; ?>
                        <td class="text-right"><?= $realisasi > 0 ? number_format($realisasi, 0, ',', '.') : '-'; ?></td>
                    <?php endforeach; ?>
                    <td class="text-right font-bold"><?= number_format($hasil['totalPengeluaran'], 0, ',', '.'); ?></td>
                    <td></td>
                </tr>
                <tr class="font-bold">
                    <td><?= $nomor++; ?></td>
                    <td class="text-left">Sisa Alokasi</td>
                    <td></td>
                    <?php foreach (array_values($kategoriColumnMap) as $kat) : ?>
                        <?php
                        $alokasi = $hasil['totalPendapatan'] * ($kat['persentase'] / 100);
                        $realisasi = $realisasiMap[$kat['nama_kategori']] ?? 0;
                        $sisa = $alokasi - $realisasi;
                        ?>
                        <td class="text-right"><?= number_format($sisa, 0, ',', '.'); ?></td>
                    <?php endforeach; ?>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot class="font-bold">
                <tr>
                    <td colspan="2" class="text-left">Jumlah Pengeluaran Tahun Ini</td>
                    <td colspan="<?= 1 + $totalKolomPengeluaran; ?>"></td>
                    <td class="text-right"><?= number_format($hasil['totalPengeluaran'], 0, ',', '.'); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-left">Sisa Saldo Tahun ini</td>
                    <td colspan="<?= 1 + $totalKolomPengeluaran; ?>"></td>
                    <td></td>
                    <td class="text-right"><?= number_format($hasil['saldoAkhirTahun'], 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="padding-bottom: 5px;">
                    <?= $lokasi; ?>, <?= date('j') . ' ' . $bulanIndonesia[date('n')] . ' ' . date('Y'); ?>
                </td>
            </tr>
            <tr>
                <td>Mengetahui Kepala Desa Melung</td>
                <td>Penasihat</td>
                <td>Pengawas</td>
                <td>Ketua BUM-Des</td>
                <td>Bendahara BUMDES</td>
            </tr>
            <tr>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
            </tr>
            <tr>
                <td><strong><u><?= $kepala_desa; ?></u></strong></td>
                <td><strong><u><?= $penasihat; ?></u></strong></td>
                <td><strong><u><?= $pengawas; ?></u></strong></td>
                <td><strong><u><?= $ketua; ?></u></strong></td>
                <td><strong><u><?= $bendahara; ?></u></strong></td>
            </tr>
        </table>
    </div>

</body>

</html>