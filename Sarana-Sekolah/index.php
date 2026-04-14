<?php
// ============================================
// HALAMAN UTAMA (LANDING PAGE)
// Redirect user yang sudah login
// ============================================
session_start();

// Jika sudah login, redirect ke dashboard masing-masing
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: siswa/dashboard.php");
    }
    exit();
}

 $page_title = "Beranda";
require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================
     LANDING PAGE - HALAMAN UTAMA
     ============================================ -->
<div class="hero-bg">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-7">
                <!-- Judul Besar -->
                <h1 class="display-4 fw-bold text-white mb-3">
                    Sistem Pengaduan<br>
                    <span class="text-warning">Sarana Sekolah</span>
                </h1>
                <p class="lead text-white opacity-75 mb-4">
                    Sampaikan keluhan kerusakan sarana dan prasarana sekolah
                    dengan mudah dan cepat. Tim kami akan segera menindaklanjuti
                    setiap pengaduan yang masuk.
                </p>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-3 flex-wrap">
                    <a href="auth/login.php" class="btn btn-warning btn-lg fw-semibold px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sistem
                    </a>
                    <a href="#info" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-info-circle me-2"></i> Pelajari Lebih
                    </a>
                </div>

                <!-- Statistik singkat -->
                <div class="row mt-5 g-3">
                    <div class="col-4">
                        <div class="text-center text-white">
                            <h3 class="fw-bold mb-0">24/7</h3>
                            <small class="opacity-75">Selalu Aktif</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center text-white">
                            <h3 class="fw-bold mb-0">Cepat</h3>
                            <small class="opacity-75">Respons Cepat</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center text-white">
                            <h3 class="fw-bold mb-0">Mudah</h3>
                            <small class="opacity-75">Mudah Digunakan</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ilustrasi -->
            <div class="col-lg-5 d-none d-lg-block text-center">
                <div class="bg-white bg-opacity-10 rounded-5 p-5 backdrop-blur">
                    <i class="bi bi-clipboard2-check text-white" style="font-size: 180px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bagian Info -->
<section id="info" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Bagaimana Cara Kerjanya?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:60px;height:60px;">
                        <i class="bi bi-pencil-square text-white" style="font-size:24px;"></i>
                    </div>
                    <h5 class="fw-bold">1. Buat Pengaduan</h5>
                    <p class="text-muted">Isi formulir pengaduan dengan judul, deskripsi, dan kategori kerusakan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="bg-warning bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:60px;height:60px;">
                        <i class="bi bi-gear text-white" style="font-size:24px;"></i>
                    </div>
                    <h5 class="fw-bold">2. Diproses Admin</h5>
                    <p class="text-muted">Admin akan memeriksa dan memproses pengaduan yang Anda kirimkan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:60px;height:60px;">
                        <i class="bi bi-check-circle text-white" style="font-size:24px;"></i>
                    </div>
                    <h5 class="fw-bold">3. Terselesaikan</h5>
                    <p class="text-muted">Anda bisa melihat status dan feedback dari admin tentang pengaduan Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
