<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Neraca Keuangan <?= $tahun; ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-section h3,
        .header-section p {
            margin: 0;
        }

        .container {
            width: 100%;
        }

        .column {
            width: 49%;
            float: left;
        }

        .column.left {
            margin-right: 2%;
        }

        .column table {
            width: 100%;
            border-collapse: collapse;
        }

        .column th,
        .column td {
            border: 1px solid #000;
            padding: 4px;
        }

        .main-header {
            font-weight: bold;
            text-align: center;
            background-color: #f2f2f2;
        }

        .sub-header {
            font-weight: bold;
            text-align: center;
            background-color: #f9f9f9;
        }

        .total-row td {
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .balance-check {
            margin-top: 15px;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
        }

        .balance-ok {
            background-color: #D4EDDA;
            color: #155724;
        }

        .balance-not-ok {
            background-color: #F8D7DA;
            color: #721C24;
        }

        .signature-section {
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <?php
    $komponen = $data['komponen'];
    $totalAktivaLancar = array_sum(array_column($komponen['aktiva_lancar'], 'jumlah'));
    $totalAktivaTetap = array_sum(array_column($komponen['aktiva_tetap'], 'jumlah'));
    $totalAktiva = $totalAktivaLancar + $totalAktivaTetap;

    $totalHutangLancar = array_sum(array_column($komponen['hutang_lancar'], 'jumlah'));
    $totalHutangJangkaPanjang = array_sum(array_column($komponen['hutang_jangka_panjang'], 'jumlah'));
    $totalModalDinamis = array_sum(array_column($komponen['modal'], 'jumlah'));
    $totalModal = $data['surplusDefisitDitahan'] + $totalModalDinamis;
    $totalPasiva = $totalHutangLancar + $totalHutangJangkaPanjang + $totalModal;
    ?>

    <div class="header-section">
        <h3>NERACA KEUANGAN</h3>
        <p>PERIODE TAHUN <?= $tahun; ?></p>
    </div>

    <div class="container clearfix">
        <div class="column left">
            <table>
                <thead>
                    <tr>
                        <th colspan="3" class="main-header">AKTIVA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" class="sub-header">Aktiva Lancar</td>
                    </tr>
                    <?php $nomor = 1;
                    foreach ($komponen['aktiva_lancar'] as $item): ?>
                        <tr>
                            <td style="width:10%; text-align:center;"><?= $nomor++; ?></td>
                            <td class="text-left"><?= $item['nama_komponen']; ?></td>
                            <td class="text-right" style="width:30%;"><?= number_format($item['jumlah'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" class="text-left">JUMLAH AKTIVA LANCAR</td>
                        <td class="text-right"><?= number_format($totalAktivaLancar, 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="sub-header">Aktiva Tetap</td>
                    </tr>
                    <?php $nomor = 1;
                    foreach ($komponen['aktiva_tetap'] as $item): ?>
                        <tr>
                            <td style="width:10%; text-align:center;"><?= $nomor++; ?></td>
                            <td class="text-left"><?= $item['nama_komponen']; ?></td>
                            <td class="text-right" style="width:30%;"><?= number_format($item['jumlah'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" class="text-left">JUMLAH AKTIVA TETAP</td>
                        <td class="text-right"><?= number_format($totalAktivaTetap, 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="total-row main-header">
                        <td colspan="2" class="text-left">TOTAL AKTIVA</td>
                        <td class="text-right"><?= number_format($totalAktiva, 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="column">
            <table>
                <thead>
                    <tr>
                        <th colspan="3" class="main-header">PASIVA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" class="sub-header">Hutang Lancar</td>
                    </tr>
                    <?php $nomor = 1;
                    foreach ($komponen['hutang_lancar'] as $item): ?>
                        <tr>
                            <td style="width:10%; text-align:center;"><?= $nomor++; ?></td>
                            <td class="text-left"><?= $item['nama_komponen']; ?></td>
                            <td class="text-right" style="width:30%;"><?= number_format($item['jumlah'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" class="text-left">JUMLAH HUTANG LANCAR</td>
                        <td class="text-right"><?= number_format($totalHutangLancar, 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>

                    <tr>
                        <td colspan="3" class="sub-header">Hutang Jangka Panjang</td>
                    </tr>
                    <?php $nomor = 1;
                    foreach ($komponen['hutang_jangka_panjang'] as $item): ?>
                        <tr>
                            <td style="width:10%; text-align:center;"><?= $nomor++; ?></td>
                            <td class="text-left"><?= $item['nama_komponen']; ?></td>
                            <td class="text-right" style="width:30%;"><?= number_format($item['jumlah'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" class="text-left">JUMLAH HUTANG JANGKA PANJANG</td>
                        <td class="text-right"><?= number_format($totalHutangJangkaPanjang, 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>

                    <tr>
                        <td colspan="3" class="sub-header">Modal</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="text-left">Surplus/Defisit Ditahan</td>
                        <td class="text-right"><?= number_format($data['surplusDefisitDitahan'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php $nomor = 1;
                    foreach ($komponen['modal'] as $item): ?>
                        <tr>
                            <td style="width:10%; text-align:center;"><?= $nomor++; ?></td>
                            <td class="text-left"><?= $item['nama_komponen']; ?></td>
                            <td class="text-right" style="width:30%;"><?= number_format($item['jumlah'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" class="text-left">JUMLAH MODAL</td>
                        <td class="text-right"><?= number_format($totalModal, 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="total-row main-header">
                        <td colspan="2" class="text-left">TOTAL PASIVA</td>
                        <td class="text-right"><?= number_format($totalPasiva, 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="balance-check <?= ($totalAktiva == $totalPasiva) ? 'balance-ok' : 'balance-not-ok'; ?>">
        <?php if ($totalAktiva == $totalPasiva): ?>
            CHECK BALANCE: SEIMBANG
        <?php else: ?>
            CHECK BALANCE: TIDAK SEIMBANG (Selisih: <?= number_format($totalAktiva - $totalPasiva, 0, ',', '.'); ?>)
        <?php endif; ?>
    </div>

    <div class="signature-section clearfix">
        <div class="column left" style="text-align:center;">
            <p>Mengetahui,</p>
            <p>Ketua BUMDES</p>
            <br><br><br><br>
            <p><strong><u><?= $ketua; ?></u></strong></p>
        </div>
        <div class="column" style="text-align:center;">
            <p><?= $lokasi; ?>, <?= date('d ') . $bulanIndonesia[(int)date('m')] . date(' Y'); ?></p>
            <p>Bendahara BUMDES</p>
            <br><br><br><br>
            <p><strong><u><?= $bendahara; ?></u></strong></p>
        </div>
    </div>

</body>

</html>