<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan BKU Bulanan</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        h2,
        h3 {
            text-align: center;
            margin: 5px 0;
        }

        h3 {
            margin-bottom: 15px;
        }

        h4 {
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .badge {
            display: inline-block;
            padding: .35em .65em;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            background-color: #6c757d;
        }
    </style>
</head>

<body>
    <h2>LAPORAN BUKU KAS UMUM (BKU) BULANAN</h2>
    <h3>PERIODE: <?= strtoupper(date('F Y', mktime(0, 0, 0, $laporan['bulan'], 1))); ?></h3>

    <h4>A. Ringkasan Keuangan</h4>
    <table class="table mb-4">
        <thead>
            <tr>
                <th>Total Pendapatan</th>
                <th>Total Pengeluaran</th>
                <th>Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-end">Rp <?= number_format($laporan['total_pendapatan'], 0, ',', '.'); ?></td>
                <td class="text-end">Rp <?= number_format($laporan['total_pengeluaran'], 0, ',', '.'); ?></td>
                <td class="text-end fw-bold">Rp <?= number_format($laporan['saldo_akhir'], 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <h4>B. Rincian Alokasi & Realisasi Dana</h4>
    <table class="table mb-4">
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="text-center">Persentase</th>
                <th class="text-end">Alokasi Dana</th>
                <th class="text-end">Realisasi</th>
                <th class="text-end">Sisa Alokasi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rincianAlokasi as $a): ?>
                <tr>
                    <td><?= esc($a['nama_kategori']); ?></td>
                    <td class="text-center"><?= number_format($a['persentase_saat_itu'], 2); ?>%</td>
                    <td class="text-end">Rp <?= number_format($a['jumlah_alokasi'], 0, ',', '.'); ?></td>
                    <td class="text-end">Rp <?= number_format($a['jumlah_realisasi'], 0, ',', '.'); ?></td>
                    <td class="text-end fw-bold">Rp <?= number_format($a['sisa_alokasi'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>C. Rincian Pendapatan</h4>
    <table class="table mb-4">
        <thead>
            <tr>
                <th>Jenis Pendapatan</th>
                <th class="text-end">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rincianPendapatan as $p): ?>
                <tr>
                    <td><?= esc($p['nama_pendapatan']); ?></td>
                    <td class="text-end">Rp <?= number_format($p['jumlah'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>D. Rincian Pengeluaran</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th class="text-end">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rincianPengeluaran as $p): ?>
                <tr>
                    <td><?= esc($p['deskripsi_pengeluaran']); ?></td>
                    <td class="text-center"><span class="badge"><?= esc($p['nama_kategori']); ?></span></td>
                    <td class="text-end">Rp <?= number_format($p['jumlah'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>