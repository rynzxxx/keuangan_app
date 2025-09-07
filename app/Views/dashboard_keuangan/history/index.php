<?= $this->extend('dashboard_keuangan/layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .activity-feed {
        list-style: none;
        padding-left: 1.5rem;
        border-left: 3px solid #e3e6f0;
        /* Garis timeline utama */
    }

    .feed-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .feed-item:last-child {
        padding-bottom: 0;
    }

    .feed-icon {
        position: absolute;
        left: -28px;
        /* Posisi ikon di atas garis timeline */
        top: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #fff;
        border: 3px solid #fff;
    }

    .feed-content {
        padding: 0.5rem 1rem;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        background-color: #fff;
        transition: box-shadow 0.2s ease;
    }

    .feed-content:hover {
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
    }

    .feed-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        /* Agar rapi di mobile */
        gap: 0.5rem;
    }

    .feed-description {
        font-weight: 600;
        color: #5a5c69;
    }

    .feed-time {
        font-size: 0.8rem;
        color: #858796;
        white-space: nowrap;
    }

    .feed-user {
        font-size: 0.9rem;
        color: #5a5c69;
    }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Daftar Semua Aktivitas</h6>
        </div>
        <div class="card-body">
            <?php if (empty($logs)): ?>
                <div class="text-center text-muted p-5">
                    <i class="fas fa-ghost fa-3x mb-2"></i><br>
                    Belum ada aktivitas yang tercatat.
                </div>
            <?php else: ?>
                <ul class="activity-feed">
                    <?php foreach ($logs as $log): ?>
                        <li class="feed-item">
                            <?php
                            // Menentukan warna dan ikon berdasarkan aktivitas
                            $icon_class = 'fa-info-circle';
                            $badge_class = 'bg-secondary';
                            if ($log['aktivitas'] == 'MEMBUAT') {
                                $icon_class = 'fa-plus';
                                $badge_class = 'bg-success';
                            }
                            if ($log['aktivitas'] == 'MENGUPDATE') {
                                $icon_class = 'fa-pencil-alt';
                                $badge_class = 'bg-warning';
                            }
                            if ($log['aktivitas'] == 'MENGHAPUS') {
                                $icon_class = 'fa-trash-alt';
                                $badge_class = 'bg-danger';
                            }
                            ?>
                            <div class="feed-icon <?= $badge_class; ?>">
                                <i class="fas <?= $icon_class; ?>"></i>
                            </div>

                            <div class="feed-content">
                                <div class="feed-header">
                                    <span class="feed-description"><?= esc($log['deskripsi']); ?></span>
                                    <span class="feed-time"><?= date('d F Y, H:i', strtotime($log['created_at'])); ?></span>
                                </div>
                                <hr class="my-2">
                                <div class="feed-user">
                                    Oleh: <strong><?= esc($log['username']); ?></strong>
                                    <span class="badge rounded-pill <?= $badge_class; ?> ms-2"><?= esc($log['aktivitas']); ?></span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>