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
            margin-bottom: 1rem;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 0.5rem;
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

        .container {
            width: 100%;
        }

        .col-6 {
            width: 48%;
            float: left;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <h2>NERACA KEUANGAN</h2>
    <h3>PERIODE TAHUN <?= esc($tahunDipilih); ?></h3>

    <div class="container clearfix">
        <div class="col-6" style="margin-right: 4%;">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2">AKTIVA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2"><strong>Aktiva Lancar</strong></td>
                    </tr>
                    <?php $totalAktivaLancar = 0; ?>
                    <?php foreach ($komponen['aktiva_lancar'] as $item): ?>
                        <tr>
                            <td><?= esc($item['nama_komponen']); ?></td>
                            <td class="text-end"><?= number_format($item['jumlah'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php $totalAktivaLancar += $item['jumlah']; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>JUMLAH AKTIVA LANCAR</strong></td>
                        <td class="text-end"><strong><?= number_format($totalAktivaLancar, 0, ',', '.'); ?></strong></td>
                    </tr>
                    <!-- Lanjutkan untuk Aktiva Tetap -->
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2">PASIVA</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tampilkan semua komponen Pasiva di sini -->
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>