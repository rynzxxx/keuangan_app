<nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
    <div class="container-fluid">

        <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle" type="button">
            <i class="fas fa-bars"></i>
        </button>

        <div class="ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-2"></i> Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">Profil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="<?= site_url('logout'); ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
</nav>