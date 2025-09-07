<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Perubahan Modal</title>
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
            border: 1px solid #000;
            padding: 8px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        h2,
        h3 {
            text-align: center;
            margin: 0;
        }

        .bg-gray {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>Laporan Perubahan Modal</h2>
    <h3>Periode Tahun <?= esc($tahun) ?></h3>
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th width="150px">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" class="font-bold">Penambahan:</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Laba Bersih Tahun Berjalan</td>
                <td class="text-right"><?= number_format($laba_rugi_bersih, 0, ',', '.') ?></td>
            </tr>
            <?php foreach ($komponen_penambahan as $item): ?>
                <tr>
                    <td style="padding-left: 20px;"><?= esc($item['nama_komponen']) ?></td>
                    <td class="text-right"><?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="2" class="font-bold">Pengurangan:</td>
            </tr>
            <?php if (empty($komponen_pengurangan)): ?>
                <tr>
                    <td style="padding-left: 20px;">-</td>
                    <td class="text-right">0</td>
                </tr>
            <?php else: ?>
                <?php foreach ($komponen_pengurangan as $item): ?>
                    <tr>
                        <td style="padding-left: 20px;"><?= esc($item['nama_komponen']) ?></td>
                        <td class="text-right"><?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr class="font-bold bg-gray">
                <td>Modal Akhir (per 31 Desember <?= esc($tahun) ?>)</td>
                <td class="text-right"><?= number_format($modal_akhir, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>
</body>

</html>