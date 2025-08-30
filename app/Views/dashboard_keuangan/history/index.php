<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Aktivitas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= date('d F Y, H:i:s', strtotime($log['created_at'])); ?></td>
                                <td><?= esc($log['username']); ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'bg-secondary';
                                    if ($log['aktivitas'] == 'MEMBUAT') $badge_class = 'bg-success';
                                    if ($log['aktivitas'] == 'MENGUPDATE') $badge_class = 'bg-warning text-dark';
                                    if ($log['aktivitas'] == 'MENGHAPUS') $badge_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge_class; ?>"><?= esc($log['aktivitas']); ?></span>
                                </td>
                                <td><?= esc($log['deskripsi']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>