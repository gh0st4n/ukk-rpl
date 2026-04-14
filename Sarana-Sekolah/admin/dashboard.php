<?php
// ============================================
// DASHBOARD ADMIN
// Menampilkan statistik dan daftar pengaduan
// ============================================
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek akses: hanya admin yang boleh masuk
require_role('admin');

// ============================================
// PROSES UPDATE STATUS (via AJAX atau POST)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $id = (int)$_POST['id'];
        $status = $_POST['status'];

        if (update_status($id, $status)) {
            $_SESSION['success'] = "Status pengaduan berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui status!";
        }
        header("Location: dashboard.php");
        exit();
    }

    // Proses tambah feedback
    if ($_POST['action'] === 'add_feedback') {
        $pengaduan_id = (int)$_POST['pengaduan_id'];
        $isi_feedback = $_POST['isi_feedback'];

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
        header("Location: pengaduan_detail.php?id=" . $pengaduan_id);
        exit();
    }
}

// ============================================
// AMBIL DATA UNTUK DASHBOARD
// ============================================

// Statistik umum
 $statistik = get_statistik();

// Filter pengaduan
 $filter = [
    'kategori'       => $_GET['kategori'] ?? '',
    'status'         => $_GET['status'] ?? '',
    'tanggal_mulai'  => $_GET['tanggal_mulai'] ?? '',
    'tanggal_selesai'=> $_GET['tanggal_selesai'] ?? '',
    'search'         => $_GET['search'] ?? ''
];

// Daftar pengaduan dengan filter
 $pengaduan = get_all_pengaduan($filter);

// Statistik per kategori (untuk chart)
 $stat_kategori = get_statistik_kategori();

// Statistik top siswa
 $stat_siswa = get_statistik_siswa();

 $page_title = "Dashboard Admin";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="container-fluid py-4">
    <!-- Flash Message -->
    <?php echo flash_message(); ?>

    <!-- ============================================
         KARTU STATISTIK
         ============================================ -->
    <div class="row g-3 mb-4">
        <!-- Total Pengaduan -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-inbox text-primary" style="font-size:28px;"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Total Pengaduan</p>
                            <h3 class="fw-bold mb-0"><?php echo $statistik['total']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menunggu -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-hourglass-split text-warning" style="font-size:28px;"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Menunggu</p>
                            <h3 class="fw-bold mb-0 text-warning"><?php echo $statistik['menunggu']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diproses -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 bg-info bg-opacity-10 p-3 me-3">
                            <i class="bi bi-gear text-info" style="font-size:28px;"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Diproses</p>
                            <h3 class="fw-bold mb-0 text-info"><?php echo $statistik['diproses']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-check-circle text-success" style="font-size:28px;"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Selesai</p>
                            <h3 class="fw-bold mb-0 text-success"><?php echo $statistik['selesai']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         CHART KATEGORI & TOP SISWA
         ============================================ -->
    <div class="row g-3 mb-4">
        <!-- Chart Kategori -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i>Pengaduan per Kategori</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($stat_kategori)): ?>
                        <?php
                        // Hitung nilai maksimum untuk skala bar
                        $max_jumlah = max(array_column($stat_kategori, 'jumlah'));
                        $warna = ['primary', 'success', 'danger', 'warning', 'info', 'secondary'];
                        ?>
                        <?php foreach ($stat_kategori as $i => $kat): ?>
                            <?php
                            $persen = $max_jumlah > 0 ? ($kat['jumlah'] / $max_jumlah * 100) : 0;
                            $warna_pilih = $warna[$i % count($warna)];
                            ?>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span><?php echo escape($kat['kategori']); ?></span>
                                    <span class="fw-bold"><?php echo $kat['jumlah']; ?></span>
                                </div>
                                <div class="progress" style="height:24px;">
                                    <div class="progress-bar bg-<?php echo $warna_pilih; ?>"
                                         role="progressbar"
                                         style="width: <?php echo $persen; ?>%"
                                         aria-valuenow="<?php echo $kat['jumlah']; ?>"
                                         aria-valuemin="0"
                                         aria-valuemax="<?php echo $max_jumlah; ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Belum ada data pengaduan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Siswa Pengadu -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2"></i>Top Pengadu</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($stat_siswa)): ?>
                        <?php foreach ($stat_siswa as $i => $sw): ?>
                            <?php if ($sw['jumlah'] > 0): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                                         style="width:36px;height:36px;font-size:14px;font-weight:700;">
                                        <?php echo $i + 1; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-semibold small"><?php echo escape($sw['nama']); ?></p>
                                        <p class="mb-0 text-muted" style="font-size:12px;"><?php echo escape($sw['kelas']); ?></p>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?php echo $sw['jumlah']; ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Belum ada data.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         FILTER & TABEL PENGADUAN
         ============================================ -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Pengaduan</h6>
                <!-- Tombol toggle filter -->
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterSection">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Form Filter (bisa di-collapse) -->
            <div class="collapse mb-3" id="filterSection">
                <form method="GET" action="" class="row g-2 p-3 border rounded-3 bg-light">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label small">Cari Nama/Judul</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Ketik untuk cari..."
                               value="<?php echo escape($filter['search']); ?>">
                    </div>
                    <!-- Kategori -->
                    <div class="col-md-2">
                        <label class="form-label small">Kategori</label>
                        <select name="kategori" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="Listrik" <?php echo $filter['kategori'] === 'Listrik' ? 'selected' : ''; ?>>Listrik</option>
                            <option value="Meja-Kursi" <?php echo $filter['kategori'] === 'Meja-Kursi' ? 'selected' : ''; ?>>Meja-Kursi</option>
                            <option value="AC/Fan" <?php echo $filter['kategori'] === 'AC/Fan' ? 'selected' : ''; ?>>AC/Fan</option>
                            <option value="Komputer" <?php echo $filter['kategori'] === 'Komputer' ? 'selected' : ''; ?>>Komputer</option>
                            <option value="Toilet" <?php echo $filter['kategori'] === 'Toilet' ? 'selected' : ''; ?>>Toilet</option>
                            <option value="Lainnya" <?php echo $filter['kategori'] === 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>
                    <!-- Status -->
                    <div class="col-md-2">
                        <label class="form-label small">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="menunggu" <?php echo $filter['status'] === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="diproses" <?php echo $filter['status'] === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                            <option value="selesai" <?php echo $filter['status'] === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>
                    <!-- Tanggal Mulai -->
                    <div class="col-md-2">
                        <label class="form-label small">Dari Tanggal</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                               value="<?php echo escape($filter['tanggal_mulai']); ?>">
                    </div>
                    <!-- Tanggal Selesai -->
                    <div class="col-md-2">
                        <label class="form-label small">Sampai Tanggal</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm"
                               value="<?php echo escape($filter['tanggal_selesai']); ?>">
                    </div>
                    <!-- Tombol Filter & Reset -->
                    <div class="col-md-1 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-sm btn-primary" title="Terapkan Filter">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabel Pengaduan -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px;">No</th>
                            <th>Pengadu</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th style="width:150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pengaduan)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size:40px;"></i>
                                    <p class="mt-2 mb-0">Tidak ada pengaduan ditemukan.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; ?>
                            <?php foreach ($pengaduan as $p): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <div class="fw-semibold small"><?php echo escape($p['nama']); ?></div>
                                        <div class="text-muted" style="font-size:12px;"><?php echo escape($p['kelas']); ?></div>
                                    </td>
                                    <td>
                                        <a href="pengaduan_detail.php?id=<?php echo $p['id']; ?>" class="text-decoration-none fw-semibold">
                                            <?php echo escape($p['judul']); ?>
                                        </a>
                                        <div class="text-muted small text-truncate" style="max-width:250px;">
                                            <?php echo escape(substr($p['deskripsi'], 0, 60)) . '...'; ?>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary"><?php echo escape($p['kategori']); ?></span></td>
                                    <td><?php echo badge_status($p['status']); ?></td>
                                    <td class="small text-muted"><?php echo format_tanggal($p['created_at']); ?></td>
                                    <td>
                                        <!-- Dropdown aksi -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="pengaduan_detail.php?id=<?php echo $p['id']; ?>">
                                                        <i class="bi bi-eye me-2"></i> Detail
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-warning"
                                                            onclick="ubahStatus(<?php echo $p['id']; ?>, 'menunggu')">
                                                        <i class="bi bi-hourglass me-2"></i> Menunggu
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-info"
                                                            onclick="ubahStatus(<?php echo $p['id']; ?>, 'diproses')">
                                                        <i class="bi bi-gear me-2"></i> Diproses
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-success"
                                                            onclick="ubahStatus(<?php echo $p['id']; ?>, 'selesai')">
                                                        <i class="bi bi-check-circle me-2"></i> Selesai
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Jumlah data -->
            <?php if (!empty($pengaduan)): ?>
                <div class="text-muted small">
                    Menampilkan <?php echo count($pengaduan); ?> data pengaduan
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Form tersembunyi untuk update status via JS -->
<form id="formStatus" method="POST" action="" style="display:none;">
    <input type="hidden" name="action" value="update_status">
    <input type="hidden" name="id" id="statusId">
    <input type="hidden" name="status" id="statusValue">
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
