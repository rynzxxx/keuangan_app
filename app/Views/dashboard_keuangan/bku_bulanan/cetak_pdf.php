<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan BKU Bulanan - <?= $namaBulan . ' ' . $laporan['tahun']; ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #333;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-section p {
            margin: 0;
            font-weight: bold;
            font-size: 12px;
        }

        .header-section .sub-header {
            font-size: 14px;
        }

        .table-container {
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            white-space: pre-line;
        }

        /* DIUBAH: Menambahkan class ini untuk menutupi artefak render */
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

        .footer-section {
            margin-top: 30px;
            width: 100%;
        }

        .signature-block {
            width: 40%;
            float: left;
            text-align: center;
        }

        .signature-block.right {
            float: right;
        }

        .signature-space {
            height: 50px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>

    <div class="header-section">
        <p>BUKU KAS UMUM BADAN USAHA MILIK DESA</p>
        <p class="sub-header">*BUMDES ALAM LESTARI*</p>
        <p>DESA MELUNG KECAMATAN KEDUNGBANTENG</p>
        <p>KABUPATEN BANYUMAS</p>
        <p>PERIODE: <?= strtoupper($namaBulan . ' ' . $laporan['tahun']); ?></p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th rowspan="3" class="rowspan-header">NO</th>
                    <th rowspan="3" class="rowspan-header">TANGGAL</th>
                    <th rowspan="3" class="rowspan-header">URAIAN</th>
                    <th rowspan="3" class="rowspan-header">PENDAPATAN</th>
                    <th colspan="<?= $totalKolomPengeluaran; ?>">PENGELUARAN</th>
                    <th rowspan="3" class="rowspan-header">KOMULATIF<br>PENGELUARAN</th>
                    <th rowspan="3" class="rowspan-header">SALDO</th>
                </tr>
                <tr>
                    <?php foreach ($kategoriHierarki as $parentKat) : ?>
                        <?php if (!empty($parentKat['children'])) : ?>
                            <th colspan="<?= count($parentKat['children']); ?>">
                                <?= strtoupper($parentKat['nama_kategori']); ?> <?= $parentKat['persentase']; ?>%
                            </th>
                        <?php else : ?>
                            <th rowspan="2">
                                <?= strtoupper($parentKat['nama_kategori']); ?> <?= $parentKat['persentase']; ?>%
                            </th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <?php foreach ($kategoriHierarki as $parentKat) : ?>
                        <?php if (!empty($parentKat['children'])) : ?>
                            <?php foreach ($parentKat['children'] as $childKat) : ?>
                                <th>
                                    <?= strtoupper($childKat['nama_kategori']); ?> <?= $childKat['persentase']; ?>%
                                </th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $nomor = 1;
                $saldo = (float)$laporan['saldo_bulan_lalu'];
                $komulatifPengeluaran = 0;
                ?>
                <tr>
                    <td><?= $nomor++; ?></td>
                    <td></td>
                    <td class="text-left">Sisa Saldo Bulan Lalu</td>
                    <td class="text-right"><?= number_format($laporan['saldo_bulan_lalu'], 0, ',', '.'); ?></td>
                    <td colspan="<?= $totalKolomPengeluaran; ?>"></td>
                    <td></td>
                    <td class="text-right"><?= number_format($saldo, 0, ',', '.'); ?></td>
                </tr>

                <?php foreach ($rincianPendapatan as $p) : ?>
                    <?php $saldo += (float)$p['jumlah']; ?>
                    <tr>
                        <td><?= $nomor++; ?></td>
                        <td><?= date('d-m-Y', strtotime($p['created_at'])); ?></td>
                        <td class="text-left"><?= $p['nama_pendapatan']; ?></td>
                        <td class="text-right"><?= number_format($p['jumlah'], 0, ',', '.'); ?></td>
                        <td colspan="<?= $totalKolomPengeluaran; ?>"></td>
                        <td></td>
                        <td class="text-right"><?= number_format($saldo, 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>

                <tr class="font-bold">
                    <td></td>
                    <td></td>
                    <td class="text-left">Total Pendapatan Bulan Ini</td>
                    <td class="text-right"><?= number_format($laporan['total_pendapatan'], 0, ',', '.'); ?></td>
                    <?php
                    $alokasiCols = array_fill(1, $totalKolomPengeluaran, '');
                    foreach ($rincianAlokasi as $alokasi) {
                        $colIndex = $kategoriColumnMap[$alokasi['master_kategori_id']] ?? null;
                        if ($colIndex) {
                            $alokasiCols[$colIndex] = number_format($alokasi['jumlah_alokasi'], 0, ',', '.');
                        }
                    }
                    ?>
                    <?php foreach ($alokasiCols as $nilai) : ?>
                        <td class="text-right"><?= $nilai; ?></td>
                    <?php endforeach; ?>
                    <td></td>
                    <td class="text-right"><?= number_format($saldo, 0, ',', '.'); ?></td>
                </tr>

                <?php foreach ($rincianPengeluaran as $p) : ?>
                    <?php
                    $saldo -= (float)$p['jumlah'];
                    $komulatifPengeluaran += (float)$p['jumlah'];
                    ?>
                    <tr>
                        <td><?= $nomor++; ?></td>
                        <td><?= date('d-m-Y', strtotime($p['created_at'])); ?></td>
                        <td class="text-left"><?= $p['deskripsi_pengeluaran']; ?></td>
                        <td></td>
                        <?php
                        $pengeluaranCols = array_fill(1, $totalKolomPengeluaran, '');
                        $colIndex = $kategoriColumnMap[$p['master_kategori_id']] ?? null;
                        if ($colIndex) {
                            $pengeluaranCols[$colIndex] = number_format($p['jumlah'], 0, ',', '.');
                        }
                        ?>
                        <?php foreach ($pengeluaranCols as $nilai) : ?>
                            <td class="text-right"><?= $nilai; ?></td>
                        <?php endforeach; ?>
                        <td class="text-right"><?= number_format($komulatifPengeluaran, 0, ',', '.'); ?></td>
                        <td class="text-right"><?= number_format($saldo, 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td colspan="3" class="text-left">Alokasi</td>
                    <td></td>
                    <?php
                    $alokasiFootCols = array_fill(1, $totalKolomPengeluaran, '');
                    foreach ($rincianAlokasi as $alokasi) {
                        $colIndex = $kategoriColumnMap[$alokasi['master_kategori_id']] ?? null;
                        if ($colIndex) {
                            $alokasiFootCols[$colIndex] = number_format($alokasi['jumlah_alokasi'], 0, ',', '.');
                        }
                    }
                    ?>
                    <?php foreach ($alokasiFootCols as $nilai) : ?>
                        <td class="text-right"><?= $nilai; ?></td>
                    <?php endforeach; ?>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="font-bold">
                    <td colspan="3" class="text-left">Sisa Alokasi</td>
                    <td></td>
                    <?php
                    $sisaAlokasiCols = array_fill(1, $totalKolomPengeluaran, '');
                    foreach ($rincianAlokasi as $alokasi) {
                        $colIndex = $kategoriColumnMap[$alokasi['master_kategori_id']] ?? null;
                        if ($colIndex) {
                            $sisaAlokasiCols[$colIndex] = number_format($alokasi['sisa_alokasi'], 0, ',', '.');
                        }
                    }
                    ?>
                    <?php foreach ($sisaAlokasiCols as $nilai) : ?>
                        <td class="text-right"><?= $nilai; ?></td>
                    <?php endforeach; ?>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="font-bold">
                    <td colspan="3" class="text-left">Jumlah Pengeluaran Bulan Ini</td>
                    <td colspan="<?= 1 + $totalKolomPengeluaran; ?>"></td>
                    <td class="text-right"><?= number_format($laporan['total_pengeluaran'], 0, ',', '.'); ?></td>
                    <td></td>
                </tr>
                <tr class="font-bold">
                    <td colspan="3" class="text-left">Sisa Saldo Bulan ini</td>
                    <td colspan="<?= 1 + $totalKolomPengeluaran; ?>"></td>
                    <td></td>
                    <td class="text-right"><?= number_format($laporan['saldo_akhir'], 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer-section clearfix">
        <div class="signature-block">
            <p>Mengetahui,</p>
            <p>Ketua BUMDES</p>
            <div class="signature-space"></div>
            <p><strong><u><?= $ketua; ?></u></strong></p>
        </div>
        <div class="signature-block right">
            <p><?= $lokasi; ?>, 02 Agustus 2025</p>
            <p>Bendahara BUMDES</p>
            <div class="signature-space"></div>
            <p><strong><u><?= $bendahara; ?></u></strong></p>
        </div>
    </div>

</body>

</html>