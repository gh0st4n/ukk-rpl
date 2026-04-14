<?php
// ============================================
// HALAMAN LOGIN
// Menangani proses login admin & siswa
// ============================================
session_start();

// Include file yang dibutuhkan
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Jika sudah login, langsung redirect
if (is_logged_in()) {
    if (is_role('admin')) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../siswa/dashboard.php");
    }
    exit();
}

// ============================================
// PROSES LOGIN SAJA FORM DI-SUBMIT
// ============================================
 $login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi sederhana
    if (empty($username) || empty($password)) {
        $login_error = "Username dan password wajib diisi!";
    } else {
        // Cocokkan dengan database
        $user = login($username, $password);

        if ($user) {
            // Simpan data user ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['kelas'] = $user['kelas'] ?? '';

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                $_SESSION['success'] = "Selamat datang, Administrator!";
                header("Location: ../admin/dashboard.php");
            } else {
                $_SESSION['success'] = "Selamat datang, " . $user['nama'] . "!";
                header("Location: ../siswa/dashboard.php");
            }
            exit();
        } else {
            $login_error = "Username atau password salah!";
        }
    }
}

 $page_title = "Login";
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ============================================
     TAMPILAN FORM LOGIN
     ============================================ -->
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <!-- Card Login -->
            <div class="card shadow-lg border-0 rounded-4">
                <!-- Header Card -->
                <div class="card-body p-4 p-md-5">
                    <!-- Logo & Judul -->
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width:70px;height:70px;">
                            <i class="bi bi-building text-white" style="font-size:32px;"></i>
                        </div>
                        <h3 class="fw-bold text-dark">Pengaduan Sekolah</h3>
                        <p class="text-muted small">Masuk untuk melanjutkan</p>
                    </div>

                    <!-- Tampilkan error jika ada -->
                    <?php if (!empty($login_error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo escape($login_error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Flash message -->
                    <?php echo flash_message(); ?>

                    <!-- Form Login -->
                    <form method="POST" action="" novalidate>
                        <!-- Input Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i> Username
                            </label>
                            <input type="text" class="form-control form-control-lg" id="username" name="username"
                                   placeholder="Masukkan username" required autofocus
                                   value="<?php echo isset($_POST['username']) ? escape($_POST['username']) : ''; ?>">
                        </div>

                        <!-- Input Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i> Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-lg" id="password" name="password"
                                       placeholder="Masukkan password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                        </button>
                    </form>

                    <!-- Info akun demo -->
                    <div class="border rounded-3 p-3 bg-light">
                        <p class="mb-2 fw-semibold small text-muted">
                            <i class="bi bi-info-circle me-1"></i> Akun:
                        </p>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Admin:</span>
                                <code>admin / admin123</code>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Siswa:</span>
                                <code>ahmad / siswa123</code>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Siswa:</span>
                                <code>budi / siswa123</code>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Siswa:</span>
                                <code>siti / siswa123</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <p class="text-center text-muted small mt-3">
                &copy; <?php echo date('Y'); ?> Sistem Pengaduan Sarana Sekolah
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
