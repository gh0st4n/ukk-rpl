<?php
// ============================================
// HISTORI PENGADUAN (SISWA)
// Menampilkan semua pengaduan yang pernah dibuat
// ============================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek akses: hanya siswa
require_role('siswa');

// ============================================
// PROSES HAPUS PENGADUAN
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'hapus') {
        $id = (int)$_POST['id_pengaduan'];
        
        // Panggil fungsi hapus (otomatis cek apakah milik user ini)
        if (hapus_pengaduan($id, $_SESSION['user_id'])) {
            $_SESSION['success'] = "Pengaduan berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus pengaduan atau data tidak ditemukan!";
        }
        
        // Redirect ke halaman ini sendiri agar tidak resubmit
        header("Location: histori.php" . (isset($_GET['status']) ? '?status=' . $_GET['status'] : ''));
        exit();
    }
}

// Ambil semua pengaduan siswa ini
 $pengaduan = get_pengaduan_siswa($_SESSION['user_id']);

// Filter status (opsional via GET)
 $filter_status = $_GET['status'] ?? '';
if (!empty($filter_status) && in_array($filter_status, ['menunggu', 'diproses', 'selesai'])) {
    $pengaduan = array_filter($pengaduan, function($p) use ($filter_status) {
        return $p['status'] === $filter_status;
    });
    // Reset index array setelah filter
    $pengaduan = array_values($pengaduan);
}

 $page_title = "Histori Pengaduan";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_siswa.php';
?>

<div class="container py-4">
    <!-- Flash Message -->
    <?php echo flash_message(); ?>

    <!-- Judul Halaman -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h4 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Histori Pengaduan</h4>
            <p class="text-muted small">Total: <?php echo count(get_pengaduan_siswa($_SESSION['user_id'])); ?> pengaduan</p>
        </div>
        <a href="buat_pengaduan.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Buat Baru
        </a>
    </div>

    <!-- Filter Status -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="histori.php" class="btn btn-sm <?php echo empty($filter_status) ? 'btn-primary' : 'btn-outline-primary'; ?>">
            Semua
        </a>
        <a href="histori.php?status=menunggu" class="btn btn-sm <?php echo $filter_status === 'menunggu' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'; ?>">
            <i class="bi bi-hourglass me-1"></i> Menunggu
        </a>
        <a href="histori.php?status=diproses" class="btn btn-sm <?php echo $filter_status === 'diproses' ? 'btn-info text-dark' : 'btn-outline-info text-dark'; ?>">
            <i class="bi bi-gear me-1"></i> Diproses
        </a>
        <a href="histori.php?status=selesai" class="btn btn-sm <?php echo $filter_status === 'selesai' ? 'btn-success' : 'btn-outline-success'; ?>">
            <i class="bi bi-check-circle me-1"></i> Selesai
        </a>
    </div>

    <!-- ============================================
         DAFTAR PENGADUAN
         ============================================ -->
    <?php if (empty($pengaduan)): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox" style="font-size:64px;"></i>
            <h5 class="mt-3">Tidak ada pengaduan</h5>
            <p class="mb-3">
                <?php echo !empty($filter_status) ? 'Tidak ada pengaduan dengan status "' . escape($filter_status) . '".' : 'Anda belum pernah membuat pengaduan.'; ?>
            </p>
            <?php if (empty($filter_status)): ?>
                <a href="buat_pengaduan.php" class="btn btn-primary">Buat Pengaduan Pertama</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($pengaduan as $p): ?>
                <?php
                // Ambil feedback untuk pengaduan ini
                $fb = get_feedback($p['id']);
                $ada_feedback = !empty($fb);
                ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <!-- Header: Judul & Status -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0"><?php echo escape($p['judul']); ?></h6>
                                <?php echo badge_status($p['status']); ?>
                            </div>

                            <!-- Kategori & Tanggal -->
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge bg-secondary"><?php echo escape($p['kategori']); ?></span>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i><?php echo format_tanggal($p['created_at']); ?>
                                </small>
                            </div>

                            <!-- Deskripsi -->
                            <p class="text-muted small mb-3">
                                <?php echo nl2br(escape($p['deskripsi'])); ?>
                            </p>

                            <!-- Feedback dari Admin (jika ada) -->
                            <?php if ($ada_feedback): ?>
                                <div class="border-top pt-3">
                                    <p class="fw-semibold small mb-2">
                                        <i class="bi bi-chat-dots text-success me-1"></i>
                                        Feedback dari Admin (<?php echo count($fb); ?>)
                                    </p>
                                    <?php foreach ($fb as $f): ?>
                                        <div class="p-2 mb-2 bg-success bg-opacity-10 rounded-2">
                                            <div class="d-flex justify-content-between small">
                                                <span class="fw-semibold text-success">Admin</span>
                                                <span class="text-muted" style="font-size:11px;">
                                                    <?php echo format_tanggal($f['created_at']); ?>
                                                </span>
                                            </div>
                                            <p class="small mb-0 mt-1"><?php echo nl2br(escape($f['isi_feedback'])); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-muted small">
                                    <i class="bi bi-chat-left me-1"></i> Belum ada feedback dari admin.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Footer Card: Tombol Hapus -->
                        <div class="card-footer bg-transparent border-top-0 py-2">
                            <form method="POST" action="" onsubmit="return confirmHapus(this, '<?php echo escape(addslashes($p['judul'])); ?>')">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id_pengaduan" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash3 me-1"></i> Hapus Pengaduan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Konfirmasi Hapus (Lebih Baik dari window.confirm bawaan) -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:48px;"></i>
                <h5 class="fw-bold mt-3">Hapus Pengaduan?</h5>
                <p class="text-muted small mb-0" id="hapusJudul"></p>
                <p class="text-danger small fw-semibold mb-0 mt-2">Data yang dihapus tidak bisa dikembalikan.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapus">
                    <i class="bi bi-trash3 me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
