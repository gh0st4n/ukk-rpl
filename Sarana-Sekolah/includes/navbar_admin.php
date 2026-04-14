<?php
// ============================================
// NAVBAR ADMIN
// Menu navigasi khusus halaman admin
// ============================================
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <!-- Brand / Logo -->
        <a class="navbar-brand fw-bold" href="<?php echo base_url(); ?>admin/dashboard.php">
            <i class="bi bi-building-gear me-2"></i>Admin Panel
        </a>

        <!-- Tombol toggle untuk mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu navigasi -->
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url(); ?>admin/dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
            </ul>

            <!-- Info user & logout -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link text-light">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo escape($_SESSION['nama']); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm mt-1 ms-2" href="<?php echo base_url(); ?>auth/logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
