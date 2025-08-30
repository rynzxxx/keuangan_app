<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<!-- Form Filter Rentang Waktu -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Filter Rentang Waktu</h6>
    </div>
    <div class="card-body">
        <form action="<?= site_url('dashboard'); ?>" method="get">
            <div class="row align-items-end g-2">
                <div class="col-md-4 col-sm-6">
                    <label for="start_date" class="form-label">Tanggal Mulai:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= esc($tanggalMulai); ?>">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label for="end_date" class="form-label">Tanggal Selesai:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?= esc($tanggalSelesai); ?>">
                </div>
                <!-- [PERBAIKAN RESPONSIVE] Tombol dibuat full-width di layar kecil -->
                <div class="col-md-4 col-12 d-grid">
                    <button type="submit" class="btn btn-info mt-3 mt-md-0"><i class="fas fa-filter me-2"></i>Terapkan Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Grafik Garis (Line Chart) Pendapatan vs Pengeluaran -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Grafik Pendapatan vs Pengeluaran</h6>
    </div>
    <div class="card-body">
        <!-- [PERBAIKAN RESPONSIVE] Tinggi chart diatur secara responsif -->
        <div class="chart-area" id="line-chart-container" style="position: relative; height:40vh; min-height: 300px;">
            <canvas id="pendapatanVsPengeluaranChart"></canvas>
        </div>
    </div>
</div>

<!-- Grafik Donat Komponen Pendapatan & Pengeluaran -->
<div class="row">
    <!-- [PERBAIKAN RESPONSIVE] Diubah dari col-lg-6 menjadi col-md-6 -->
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Komponen Pendapatan</h6>
            </div>
            <div class="card-body">
                <!-- [PERBAIKAN RESPONSIVE] Tinggi chart diatur secara responsif -->
                <div class="chart-pie pt-4" id="pendapatan-chart-container" style="position: relative; height:40vh; min-height: 300px;">
                    <canvas id="komponenPendapatanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Komponen Pengeluaran</h6>
            </div>
            <div class="card-body">
                <!-- [PERBAIKAN RESPONSIVE] Tinggi chart diatur secara responsif -->
                <div class="chart-pie pt-4" id="pengeluaran-chart-container" style="position: relative; height:40vh; min-height: 300px;">
                    <canvas id="komponenPengeluaranChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Library Chart.js dan Date Adapter -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>


<!-- Kode JavaScript untuk Menggambar Grafik (Tidak ada perubahan logika) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function displayNoDataMessage(containerId, message) {
            const container = document.getElementById(containerId);
            container.innerHTML = `<div class="text-center text-muted p-5">
                                <i class="fas fa-chart-bar fa-3x mb-2"></i><br>
                                ${message}
                               </div>`;
        }

        const dataGrafikLine = <?= json_encode($grafikLine); ?>;
        const dataDonatPendapatan = <?= json_encode($komponenPendapatan); ?>;
        const dataDonatPengeluaran = <?= json_encode($komponenPengeluaran); ?>;
        const paletWarna = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];

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
            displayNoDataMessage('line-chart-container', 'Tidak ada data untuk ditampilkan pada rentang waktu ini.');
        }

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
                    maintainAspectRatio: false
                }
            });
        } else {
            displayNoDataMessage('pendapatan-chart-container', 'Tidak ada data pendapatan untuk ditampilkan.');
        }

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
                    maintainAspectRatio: false
                }
            });
        } else {
            displayNoDataMessage('pengeluaran-chart-container', 'Tidak ada data pengeluaran untuk ditampilkan.');
        }
    });
</script>
<?= $this->endSection(); ?>