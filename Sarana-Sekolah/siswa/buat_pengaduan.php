<?php
// ============================================
// HALAMAN BUAT PENGADUAN (SISWA)
// Form input pengaduan baru
// ============================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek akses: hanya siswa
require_role('siswa');

// ============================================
// PROSES SUBMIT PENGADUAN
// ============================================
 $errors = [];
 $old_input = [
    'judul' => '',
    'deskripsi' => '',
    'kategori' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simpan input lama untuk mengisi kembali form jika ada error
    $old_input = [
        'judul' => $_POST['judul'] ?? '',
        'deskripsi' => $_POST['deskripsi'] ?? '',
        'kategori' => $_POST['kategori'] ?? ''
    ];

    // Validasi input
    $errors = validasi_pengaduan($old_input);

    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $berhasil = buat_pengaduan(
            $_SESSION['user_id'],
            $old_input['judul'],
            $old_input['deskripsi'],
            $old_input['kategori']
        );

        if ($berhasil) {
            $_SESSION['success'] = "Pengaduan berhasil dikirim! Admin akan segera menindaklanjuti.";
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Terjadi kesalahan saat menyimpan. Silakan coba lagi.";
        }
    }
}

// Daftar kategori pengaduan
 $kategori_list = ['Listrik', 'Meja-Kursi', 'AC/Fan', 'Komputer', 'Toilet', 'Lainnya'];

 $page_title = "Buat Pengaduan";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_siswa.php';
?>

<div class="container py-4">
    <!-- Flash Message -->
    <?php echo flash_message(); ?>

    <!-- Judul Halaman -->
    <div class="mb-4">
        <h4 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Buat Pengaduan Baru</h4>
        <p class="text-muted">Isi formulir di bawah ini dengan lengkap dan jelas.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- ============================================
                 TAMPILKAN ERROR JIKA ADA
                 ============================================ -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Perbaiki kesalahan berikut:</h6>
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo escape($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form Pengaduan -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="" novalidate>
                        <!-- Input Judul -->
                        <div class="mb-3">
                            <label for="judul" class="form-label fw-semibold">
                                Judul Pengaduan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="judul" name="judul"
                                   placeholder="Contoh: AC Ruang Kelas Mati"
                                   value="<?php echo escape($old_input['judul']); ?>"
                                   maxlength="200" required>
                            <div class="form-text">Minimal 5 karakter, maksimal 200 karakter.</div>
                        </div>

                        <!-- Pilih Kategori -->
                        <div class="mb-3">
                            <label for="kategori" class="form-label fw-semibold">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="kategori" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori_list as $kat): ?>
                                    <option value="<?php echo $kat; ?>"
                                            <?php echo $old_input['kategori'] === $kat ? 'selected' : ''; ?>>
                                        <?php echo $kat; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Input Deskripsi -->
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-semibold">
                                Deskripsi Pengaduan <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi"
                                      rows="5" placeholder="Jelaskan secara detail tentang kerusakan yang Anda laporkan. Misalnya: lokasi, sejak kapan, dampaknya, dll."
                                      required><?php echo escape($old_input['deskripsi']); ?></textarea>
                            <div class="form-text">Minimal 10 karakter. Jelaskan detail lokasi dan kondisi kerusakan.</div>

                            <!-- Penghitung karakter -->
                            <div class="text-end">
                                <small class="text-muted">
                                    <span id="charCount">0</span> karakter
                                </small>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold px-4">
                                <i class="bi bi-send me-2"></i> Kirim Pengaduan
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary btn-lg px-4">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body p-3 bg-warning bg-opacity-10">
                    <h6 class="fw-bold small"><i class="bi bi-lightbulb me-1"></i> Tips Pengaduan yang Baik</h6>
                    <ul class="small text-muted mb-0">
                        <li>Jelaskan lokasi kerusakan secara spesifik (ruang, lantai, nomor)</li>
                        <li>Sebutkan sejak kapan kerusakan terjadi</li>
                        <li>Jelaskan dampak kerusakan terhadap kegiatan belajar</li>
                        <li>Gunakan bahasa yang sopan dan jelas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
