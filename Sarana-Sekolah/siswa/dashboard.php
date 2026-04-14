<?php
// ============================================
// DASHBOARD SISWA
// Menampilkan ringkasan dan pengaduan terbaru
// ============================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek akses: hanya siswa
require_role('siswa');

// Ambil data pengaduan milik siswa ini
 $pengaduan_saya = get_pengaduan_siswa($_SESSION['user_id']);

// Hitung statistik sederhana menggunakan array
 $stat_saya = [
    'total'   => count($pengaduan_saya),
    'menunggu'=> 0,
    'diproses'=> 0,
    'selesai' => 0
];

foreach ($pengaduan_saya as $p) {
    if ($p['status'] === 'menunggu') $stat_saya['menunggu']++;
    elseif ($p['status'] === 'diproses') $stat_saya['diproses']++;
    elseif ($p['status'] === 'selesai') $stat_saya['selesai']++;
}

// Ambil 3 pengaduan terbaru untuk ditampilkan
 $pengaduan_terbaru = array_slice($pengaduan_saya, 0, 3);

 $page_title = "Beranda Siswa";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_siswa.php';
?>

<div class="container py-4">
    <!-- Flash Message -->
    <?php echo flash_message(); ?>

    <!-- Salam pembuka -->
    <div class="mb-4">
        <h4 class="fw-bold">Halo, <?php echo escape($_SESSION['nama']); ?>! <span class="wave-hand">&#128075;</span></h4>
        <p class="text-muted">Kelas <?php echo escape($_SESSION['kelas']); ?> — Sampaikan keluhan sarana sekolah Anda di sini.</p>
    </div>

    <!-- ============================================
         STATISTIK SINGKAT
         ============================================ -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-inbox text-primary" style="font-size:28px;"></i>
                <h4 class="fw-bold mt-2 mb-0"><?php echo $stat_saya['total']; ?></h4>
                <small class="text-muted">Total</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-hourglass-split text-warning" style="font-size:28px;"></i>
                <h4 class="fw-bold mt-2 mb-0 text-warning"><?php echo $stat_saya['menunggu']; ?></h4>
                <small class="text-muted">Menunggu</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-gear text-info" style="font-size:28px;"></i>
                <h4 class="fw-bold mt-2 mb-0 text-info"><?php echo $stat_saya['diproses']; ?></h4>
                <small class="text-muted">Diproses</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-check-circle text-success" style="font-size:28px;"></i>
                <h4 class="fw-bold mt-2 mb-0 text-success"><?php echo $stat_saya['selesai']; ?></h4>
                <small class="text-muted">Selesai</small>
            </div>
        </div>
    </div>

    <!-- Tombol Buat Pengaduan -->
    <a href="buat_pengaduan.php" class="btn btn-primary btn-lg fw-semibold mb-4">
        <i class="bi bi-plus-circle me-2"></i> Buat Pengaduan Baru
    </a>

    <!-- ============================================
         PENGADUAN TERBARU
         ============================================ -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Pengaduan Terbaru</h6>
            <a href="histori.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <?php if (empty($pengaduan_terbaru)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:48px;"></i>
                    <p class="mt-3 mb-0">Anda belum memiliki pengaduan.</p>
                    <a href="buat_pengaduan.php" class="btn btn-sm btn-primary mt-2">Buat Sekarang</a>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pengaduan_terbaru as $p): ?>
                        <?php
                        // Ambil feedback untuk pengaduan ini
                        $fb = get_feedback($p['id']);
                        $ada_feedback = !empty($fb);
                        ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="fw-bold mb-0"><?php echo escape($p['judul']); ?></h6>
                                        <?php echo badge_status($p['status']); ?>
                                        <?php if ($ada_feedback): ?>
                                            <span class="badge bg-dark" title="Ada feedback dari admin">
                                                <i class="bi bi-chat-dots-fill"></i> <?php echo count($fb); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted small mb-1">
                                        <?php echo escape(substr($p['deskripsi'], 0, 100)) . (strlen($p['deskripsi']) > 100 ? '...' : ''); ?>
                                    </p>
                                    <div class="d-flex gap-3">
                                        <span class="badge bg-secondary"><?php echo escape($p['kategori']); ?></span>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i><?php echo format_tanggal($p['created_at']); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Tampilkan feedback terbaru jika ada -->
                            <?php if ($ada_feedback): ?>
                                <div class="mt-2 p-2 bg-success bg-opacity-10 rounded-2">
                                    <p class="mb-0 small">
                                        <i class="bi bi-chat-left-quote text-success me-1"></i>
                                        <strong>Admin:</strong> <?php echo escape(substr($fb[count($fb)-1]['isi_feedback'], 0, 80)); ?>...
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
