<?php
// ============================================
// DETAIL PENGADUAN (ADMIN)
// Melihat detail, update status, dan beri feedback
// ============================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek akses admin
require_role('admin');

// ============================================
// PROSES TAMBAH FEEDBACK
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_feedback') {
        $pengaduan_id = (int)$_POST['pengaduan_id'];
        $isi_feedback = trim($_POST['isi_feedback'] ?? '');

        $errors = validasi_feedback($isi_feedback);
        if (empty($errors)) {
            if (tambah_feedback($pengaduan_id, $_SESSION['user_id'], $isi_feedback)) {
                $_SESSION['success'] = "Feedback berhasil ditambahkan!";
            } else {
                $_SESSION['error'] = "Gagal menambahkan feedback!";
            }
        } else {
            $_SESSION['error'] = $errors[0];
        }
        // Redirect ke halaman yang sama (agar tidak resubmit)
        header("Location: pengaduan_detail.php?id=" . $pengaduan_id);
        exit();
    }

    // Proses update status
    if ($_POST['action'] === 'update_status') {
        $id = (int)$_POST['id'];
        $status = $_POST['status'];
        if (update_status($id, $status)) {
            $_SESSION['success'] = "Status berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui status!";
        }
        header("Location: pengaduan_detail.php?id=" . $id);
        exit();
    }
}

// ============================================
// AMBIL DATA PENGADUAN
// ============================================
 $id = (int)($_GET['id'] ?? 0);
 $detail = get_pengaduan_by_id($id);

// Jika pengaduan tidak ditemukan
if (!$detail) {
    $_SESSION['error'] = "Pengaduan tidak ditemukan!";
    header("Location: dashboard.php");
    exit();
}

// Ambil feedback untuk pengaduan ini
 $feedback_list = get_feedback($id);

 $page_title = "Detail Pengaduan";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="container-fluid py-4">
    <!-- Flash Message -->
    <?php echo flash_message(); ?>

    <!-- Tombol kembali -->
    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>

    <div class="row g-4">
        <!-- ============================================
             KOLOM KIRI: DETAIL PENGADUAN
             ============================================ -->
        <div class="col-lg-8">
            <!-- Card Detail -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-file-text me-2"></i>Detail Pengaduan</h6>
                    <?php echo badge_status($detail['status']); ?>
                </div>
                <div class="card-body">
                    <!-- Judul -->
                    <h4 class="fw-bold mb-3"><?php echo escape($detail['judul']); ?></h4>

                    <!-- Info pengirim -->
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <p class="mb-1 small text-muted">Pengadu</p>
                            <p class="fw-semibold mb-0"><?php echo escape($detail['nama']); ?></p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-1 small text-muted">Kelas</p>
                            <p class="fw-semibold mb-0"><?php echo escape($detail['kelas']); ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <p class="mb-1 small text-muted">Kategori</p>
                            <span class="badge bg-secondary"><?php echo escape($detail['kategori']); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-1 small text-muted">Tanggal Pengaduan</p>
                            <p class="fw-semibold mb-0 small"><?php echo format_tanggal($detail['created_at']); ?></p>
                        </div>
                    </div>

                    <hr>

                    <!-- Deskripsi -->
                    <p class="mb-1 small text-muted">Deskripsi Pengaduan</p>
                    <div class="p-3 bg-light rounded-3">
                        <p class="mb-0"><?php echo nl2br(escape($detail['deskripsi'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- ============================================
                 DAFTAR FEEDBACK
                 ============================================ -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Feedback
                        <span class="badge bg-primary rounded-pill ms-1"><?php echo count($feedback_list); ?></span>
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($feedback_list)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-chat-left-text" style="font-size:40px;"></i>
                            <p class="mt-2 mb-0">Belum ada feedback.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($feedback_list as $fb): ?>
                            <div class="d-flex mb-3 p-3 border rounded-3 bg-light">
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                     style="width:40px;height:40px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold small"><?php echo escape($fb['admin_nama']); ?></span>
                                        <span class="text-muted" style="font-size:12px;">
                                            <?php echo format_tanggal($fb['created_at']); ?>
                                        </span>
                                    </div>
                                    <p class="mb-0 small mt-1"><?php echo nl2br(escape($fb['isi_feedback'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Form Tambah Feedback -->
                    <hr>
                    <h6 class="fw-bold small"><i class="bi bi-reply me-1"></i>Tambah Feedback</h6>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add_feedback">
                        <input type="hidden" name="pengaduan_id" value="<?php echo $detail['id']; ?>">
                        <div class="mb-3">
                            <textarea name="isi_feedback" class="form-control" rows="3"
                                      placeholder="Tulis feedback atau tanggapan Anda..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-send me-1"></i> Kirim Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================
             KOLOM KANAN: UPDATE STATUS
             ============================================ -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold mb-0"><i class="bi bi-arrow-repeat me-2"></i>Update Status</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $detail['id']; ?>">

                        <!-- Pilihan Status -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Pilih Status</label>
                            <?php
                            $status_list = [
                                'menunggu' => ['Menunggu', 'bi-hourglass-split', 'warning'],
                                'diproses' => ['Diproses', 'bi-gear', 'info'],
                                'selesai'  => ['Selesai', 'bi-check-circle', 'success']
                            ];
                            ?>
                            <?php foreach ($status_list as $val => $info): ?>
                                <div class="form-check mb-2 p-3 border rounded-3 <?php echo $detail['status'] === $val ? 'border-primary bg-primary bg-opacity-5' : ''; ?>">
                                    <input class="form-check-input" type="radio" name="status"
                                           id="status_<?php echo $val; ?>" value="<?php echo $val; ?>"
                                           <?php echo $detail['status'] === $val ? 'checked' : ''; ?>
                                           onchange="this.form.submit()">
                                    <label class="form-check-label w-100" for="status_<?php echo $val; ?>">
                                        <span class="d-flex align-items-center">
                                            <i class="bi <?php echo $info[1]; ?> text-<?php echo $info[2]; ?> me-2"></i>
                                            <span class="fw-semibold"><?php echo $info[0]; ?></span>
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <p class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            Klik radio button untuk langsung mengubah status.
                        </p>
                    </form>
                </div>
            </div>

            <!-- Info Waktu -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold small mb-3"><i class="bi bi-clock me-2"></i>Timeline</h6>
                    <div class="d-flex align-items-start mb-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                             style="width:10px;height:10px;margin-top:6px;"></div>
                        <div>
                            <p class="mb-0 small fw-semibold">Pengaduan Dibuat</p>
                            <p class="mb-0 text-muted" style="font-size:12px;"><?php echo format_tanggal($detail['created_at']); ?></p>
                        </div>
                    </div>
                    <?php if ($detail['status'] !== 'menunggu'): ?>
                        <div class="d-flex align-items-start mb-3">
                            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                 style="width:10px;height:10px;margin-top:6px;"></div>
                            <div>
                                <p class="mb-0 small fw-semibold">Status Diproses</p>
                                <p class="mb-0 text-muted" style="font-size:12px;">Diperbarui pada: <?php echo format_tanggal($detail['updated_at']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($detail['status'] === 'selesai'): ?>
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                 style="width:10px;height:10px;margin-top:6px;"></div>
                            <div>
                                <p class="mb-0 small fw-semibold">Pengaduan Selesai</p>
                                <p class="mb-0 text-muted" style="font-size:12px;"><?php echo format_tanggal($detail['updated_at']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
