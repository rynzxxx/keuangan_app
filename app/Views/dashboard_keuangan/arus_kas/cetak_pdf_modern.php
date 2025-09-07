<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas</title>
    <style>
        @page {
            margin: 25px 35px;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2,
        .header h3 {
            margin: 0;
            padding: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            border: 1px solid #777;
            padding: 6px 8px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .category-header {
            background-color: #333;
            color: #fff;
            text-align: center;
            font-weight: bold;
            padding: 8px;
        }

        .total-row td {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .final-saldo td {
            background-color: #333;
            color: #fff;
            font-weight: bold;
            font-size: 12px;
        }

        /* Mengatur tiga kolom */
        .col-1 {
            width: 50%;
        }

        .col-2 {
            width: 25%;
        }

        .col-3 {
            width: 25%;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN ARUS KAS</h2>
        <h3>PERIODE TAHUN <?= esc($tahun) ?></h3>
    </div>

    <table class="table">
        <!-- ARUS KAS MASUK -->
        <tr>
            <td colspan="3" class="category-header">ARUS KAS MASUK</td>
        </tr>
        <tr>
            <td class="col-1">Penerimaan Pendapatan Operasional Utama</td>
            <td class="col-2"></td>
            <td class="col-3 text-right"><?= number_format($pendapatanUtama, 0, ',', '.') ?></td>
        </tr>
        <!-- [FIX] Loop sederhana untuk semua komponen masuk -->
        <?php foreach ($komponenMasuk as $item): ?>
            <tr>
                <td class="col-1"><?= esc($item['nama_komponen']) ?></td>
                <td class="col-2"></td>
                <td class="col-3 text-right"><?= number_format($item['jumlah'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2" class="text-right">Total Arus Kas Masuk</td>
            <td class="text-right"><?= number_format($totalKasMasuk, 0, ',', '.') ?></td>
        </tr>

        <!-- ARUS KAS KELUAR -->
        <tr>
            <td colspan="3" class="category-header">ARUS KAS KELUAR</td>
        </tr>
        <tr>
            <td class="col-1">Pembelian Barang dan Jasa</td>
            <td class="col-2"></td>
            <td class="col-3 text-right"><?= number_format($pembelianBarang, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="col-1">Pembayaran Beban Gaji</td>
            <td class="col-2"></td>
            <td class="col-3 text-right"><?= number_format($bebanGaji, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="col-1">Pendapatan Asli Desa</td>
            <td class="col-2"></td>
            <td class="col-3 text-right"><?= number_format($pad, 0, ',', '.') ?></td>
        </tr>
        <?php foreach ($komponenKeluar as $item): ?>
            <tr>
                <td class="col-1"><?= esc($item['nama_komponen']) ?></td>
                <td class="col-2"></td>
                <td class="col-3 text-right"><?= number_format($item['jumlah'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2" class="text-right">Total Arus Kas Keluar</td>
            <td class="text-right">(<?= number_format($totalKasKeluar, 0, ',', '.') ?>)</td>
        </tr>

        <!-- SALDO AKHIR -->
        <tr class="final-saldo">
            <td colspan="2" class="text-right">SALDO AKHIR</td>
            <td class="text-right"><?= number_format($saldoAkhir, 0, ',', '.') ?></td>
        </tr>
    </table>
</body>

</html>