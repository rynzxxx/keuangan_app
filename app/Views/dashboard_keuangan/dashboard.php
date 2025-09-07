<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .stat-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .stat-card .card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-card-icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Keuangan</h1>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($totalPendapatan ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <i class="fas fa-wallet fa-2x text-gray-300 stat-card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengeluaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($totalPengeluaran ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <i class="fas fa-receipt fa-2x text-gray-300 stat-card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Surplus / Defisit</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format(($totalPendapatan ?? 0) - ($totalPengeluaran ?? 0), 0, ',', '.'); ?></div>
                    </div>
                    <i class="fas fa-balance-scale fa-2x text-gray-300 stat-card-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Rentang Waktu</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('dashboard'); ?>" method="get">
                <div class="row align-items-end g-3">
                    <div class="col-md-4 col-sm-6">
                        <label for="start_date" class="form-label">Tanggal Mulai:</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= esc($tanggalMulai); ?>">
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label for="end_date" class="form-label">Tanggal Selesai:</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= esc($tanggalSelesai); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-check me-2"></i>Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line me-2"></i>Tren Pendapatan vs Pengeluaran</h6>
        </div>
        <div class="card-body">
            <div class="chart-area" id="line-chart-container" style="position: relative; height:40vh; min-height: 300px;">
                <canvas id="pendapatanVsPengeluaranChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Komponen Pendapatan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4" id="pendapatan-chart-container" style="position: relative; height:40vh; min-height: 300px;">
                        <canvas id="komponenPendapatanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Komponen Pengeluaran</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4" id="pengeluaran-chart-container" style="position: relative; height:40vh; min-height: 300px;">
                        <canvas id="komponenPengeluaranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        function displayNoDataMessage(containerId, message) {
            const container = document.getElementById(containerId);
            container.innerHTML = `<div class="d-flex align-items-center justify-content-center h-100 text-center text-muted p-5"><div><i class="fas fa-info-circle fa-3x mb-2"></i><br>${message}</div></div>`;
        }

        const dataGrafikLine = <?= json_encode($grafikLine ?? ['labels' => [], 'pendapatan' => [], 'pengeluaran' => []]); ?>;
        const dataDonatPendapatan = <?= json_encode($komponenPendapatan ?? []); ?>;
        const dataDonatPengeluaran = <?= json_encode($komponenPengeluaran ?? []); ?>;
        const paletWarna = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
        // [DIUBAH] Posisi legenda dibuat responsif
        const legendPosition = window.innerWidth < 768 ? 'bottom' : 'right';

        // Grafik Line
        if (dataGrafikLine && dataGrafikLine.labels.length > 0) {
            const ctxLine = document.getElementById('pendapatanVsPengeluaranChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: dataGrafikLine.labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: dataGrafikLine.pendapatan,
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        fill: true,
                        tension: 0.3
                    }, {
                        label: 'Pengeluaran',
                        data: dataGrafikLine.pengeluaran,
                        borderColor: '#e74a3b',
                        backgroundColor: 'rgba(231, 74, 59, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'month',
                                parser: 'yyyy-MM',
                                tooltipFormat: 'MMMM yyyy',
                                displayFormats: {
                                    month: 'MMM yyyy'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Periode'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah (Rp)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            displayNoDataMessage('line-chart-container', 'Tidak ada data tren untuk ditampilkan pada rentang waktu ini.');
        }

        // Grafik Donat Pendapatan
        if (dataDonatPendapatan && dataDonatPendapatan.length > 0) {
            const ctxDonatPendapatan = document.getElementById('komponenPendapatanChart').getContext('2d');
            new Chart(ctxDonatPendapatan, {
                type: 'doughnut',
                data: {
                    labels: dataDonatPendapatan.map(item => item.nama_pendapatan),
                    datasets: [{
                        data: dataDonatPendapatan.map(item => item.total),
                        backgroundColor: paletWarna
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: legendPosition
                        }
                    }
                }
            });
        } else {
            displayNoDataMessage('pendapatan-chart-container', 'Tidak ada data pendapatan untuk ditampilkan.');
        }

        // Grafik Donat Pengeluaran
        if (dataDonatPengeluaran && dataDonatPengeluaran.length > 0) {
            const ctxDonatPengeluaran = document.getElementById('komponenPengeluaranChart').getContext('2d');
            new Chart(ctxDonatPengeluaran, {
                type: 'doughnut',
                data: {
                    labels: dataDonatPengeluaran.map(item => item.nama_kategori),
                    datasets: [{
                        data: dataDonatPengeluaran.map(item => item.total),
                        backgroundColor: paletWarna.slice().reverse()
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: legendPosition
                        }
                    }
                }
            });
        } else {
            displayNoDataMessage('pengeluaran-chart-container', 'Tidak ada data pengeluaran untuk ditampilkan.');
        }
    });
</script>
<?= $this->endSection(); ?>