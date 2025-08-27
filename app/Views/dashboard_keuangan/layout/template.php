<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title); ?> | Dashboard Keuangan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link rel="stylesheet" href="/css/style.css">
    <style>
        /* Menambahkan font Poppins sebagai default */
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body>

    <div id="wrapper">

        <?= $this->include('dashboard_keuangan/layout/sidebar'); ?>

        <div id="content-wrapper">

            <?= $this->include('dashboard_keuangan/layout/navbar'); ?>

            <div class="main-content">
                <div class="container-fluid">
                    <?= $this->renderSection('content'); ?>
                </div>
            </div>

            <?= $this->include('dashboard_keuangan/layout/footer'); ?>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sidebar Toggle Functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('wrapper').classList.toggle('sidebar-toggled');
                });
            }

            // Dropdown Arrow Rotation
            const collapseElements = document.querySelectorAll('.sidebar .collapse');
            collapseElements.forEach(function(collapseEl) {
                // Saat dropdown mulai ditampilkan
                collapseEl.addEventListener('show.bs.collapse', function() {
                    this.parentElement.classList.add('menu-open');
                });
                // Saat dropdown selesai disembunyikan
                collapseEl.addEventListener('hide.bs.collapse', function() {
                    this.parentElement.classList.remove('menu-open');
                });
            });
        });
    </script>

</body>

</html>