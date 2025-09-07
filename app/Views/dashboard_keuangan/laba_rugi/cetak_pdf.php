<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title); ?></title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .text-end {
            text-align: right;
        }

        h2,
        h3 {
            text-align: center;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .laba {
            background-color: #d4edda;
            font-weight: bold;
        }

        .rugi {
            background-color: #f8d7da;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>LAPORAN LABA RUGI</h2>
    <h3>PERIODE TAHUN <?= esc($tahunDipilih); ?></h3>
    <br>
    <table class="table">
        <tr class="total-row">
            <th colspan="2">PENDAPATAN</th>
        </tr>
        <tr>
            <td>Pendapatan Usaha</td>
            <td class="text-end"><?= number_format($pendapatanUsaha, 0, ',', '.'); ?></td>
        </tr>
        <?php $totalPendapatanLain = 0; ?>
        <?php foreach ($komponenPendapatan as $item): ?>
            <tr>
                <td><?= esc($item['nama_komponen']); ?></td>
                <td class="text-end"><?= number_format($item['jumlah'], 0, ',', '.'); ?></td>
            </tr>
            <?php $totalPendapatanLain += $item['jumlah']; ?>
        <?php endforeach; ?>
        <tr class="total-row">
            <td class="text-end">TOTAL PENDAPATAN</td>
            <td class="text-end"><?= number_format($pendapatanUsaha + $totalPendapatanLain, 0, ',', '.'); ?></td>
        </tr>

        <tr class="total-row">
            <th colspan="2">BIAYA-BIAYA</th>
        </tr>
        <tr>
            <td>Biaya Bahan Baku</td>
            <td class="text-end"><?= number_format($biayaBahanBaku, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Biaya Gaji</td>
            <td class="text-end"><?= number_format($biayaGaji, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Pendapatan Asli Desa (PAD)</td>
            <td class="text-end"><?= number_format($pad, 0, ',', '.'); ?></td>
        </tr>
        <?php $totalBiayaLain = 0; ?>
        <?php foreach ($komponenBiaya as $item): ?>
            <tr>
                <td><?= esc($item['nama_komponen']); ?></td>
                <td class="text-end"><?= number_format($item['jumlah'], 0, ',', '.'); ?></td>
            </tr>
            <?php $totalBiayaLain += $item['jumlah']; ?>
        <?php endforeach; ?>
        <tr class="total-row">
            <td class="text-end">TOTAL BIAYA</td>
            <td class="text-end"><?= number_format($biayaBahanBaku + $biayaGaji + $pad + $totalBiayaLain, 0, ',', '.'); ?></td>
        </tr>

        <?php
        $totalPendapatan = $pendapatanUsaha + $totalPendapatanLain;
        $totalBiaya = $biayaBahanBaku + $biayaGaji + $pad + $totalBiayaLain;
        $labaRugi = $totalPendapatan - $totalBiaya;
        ?>
        <tr class="<?= ($labaRugi >= 0) ? 'laba' : 'rugi'; ?>">
            <td class="text-end">LABA / (RUGI) BERSIH</td>
            <td class="text-end"><?= number_format($labaRugi, 0, ',', '.'); ?></td>
        </tr>
    </table>
</body>

</html>