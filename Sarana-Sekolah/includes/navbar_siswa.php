<?php
// ============================================
// NAVBAR SISWA
// Menu navigasi khusus halaman siswa
// ============================================
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Brand / Logo -->
        <a class="navbar-brand fw-bold" href="<?php echo base_url(); ?>siswa/dashboard.php">
            <i class="bi bi-megaphone me-2"></i>Pengaduan Saya
        </a>

        <!-- Tombol toggle untuk mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSiswa">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu navigasi -->
        <div class="collapse navbar-collapse" id="navbarSiswa">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url(); ?>siswa/dashboard.php">
                        <i class="bi bi-house me-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url(); ?>siswa/buat_pengaduan.php">
                        <i class="bi bi-plus-circle me-1"></i> Buat Pengaduan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url(); ?>siswa/histori.php">
                        <i class="bi bi-clock-history me-1"></i> Histori
                    </a>
                </li>
            </ul>

            <!-- Info user & logout -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link text-light">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo escape($_SESSION['nama']); ?>
                        <small class="opacity-75">(<?php echo escape($_SESSION['kelas']); ?>)</small>
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
