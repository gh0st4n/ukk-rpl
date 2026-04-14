<?php
// ============================================
// FUNGSI-FUNGSI UTAMA APLIKASI
// File ini berisi semua logic bisnis aplikasi
// Dipisah dari tampilan untuk clean code
// ============================================

// Pastikan koneksi database sudah ada
require_once __DIR__ . '/../config/database.php';

// ============================================
// FUNGSI AUTENTIKASI
// ============================================

/**
 * Fungsi login user
 * @param string $username
 * @param string $password
 * @return array|false - Data user jika berhasil, false jika gagal
 */
function login($username, $password) {
    $username = escape($username);
    $user = query_single("SELECT * FROM users WHERE username = '$username'");

    // Cek user ada dan password cocok
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

/**
 * Fungsi cek apakah user sudah login
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Fungsi cek role user
 * @param string $role - Role yang dicek
 * @return bool
 */
function is_role($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Fungsi redirect jika belum login
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = "Anda harus login terlebih dahulu!";
        header("Location: ../auth/login.php");
        exit();
    }
}

/**
 * Fungsi redirect jika bukan role tertentu
 * @param string $role
 */
function require_role($role) {
    require_login();
    if (!is_role($role)) {
        $_SESSION['error'] = "Anda tidak memiliki akses!";
        header("Location: ../auth/login.php");
        exit();
    }
}

// ============================================
// FUNGSI PENGADUAN (CRUD)
// ============================================

/**
 * Fungsi membuat pengaduan baru
 * @param int $user_id - ID siswa
 * @param string $judul - Judul pengaduan
 * @param string $deskripsi - Isi pengaduan
 * @param string $kategori - Kategori pengaduan
 * @return bool
 */
function buat_pengaduan($user_id, $judul, $deskripsi, $kategori) {
    $judul = escape($judul);
    $deskripsi = escape($deskripsi);
    $kategori = escape($kategori);

    $sql = "INSERT INTO pengaduan (user_id, judul, deskripsi, kategori) 
            VALUES ($user_id, '$judul', '$deskripsi', '$kategori')";

    return execute($sql);
}

/**
 * Fungsi mendapatkan semua pengaduan (untuk admin)
 * @param array $filter - Filter pencarian
 * @return array
 */
function get_all_pengaduan($filter = []) {
    $sql = "SELECT p.*, u.nama, u.kelas 
            FROM pengaduan p 
            JOIN users u ON p.user_id = u.id 
            WHERE 1=1";

    // Filter berdasarkan kategori
    if (!empty($filter['kategori'])) {
        $kategori = escape($filter['kategori']);
        $sql .= " AND p.kategori = '$kategori'";
    }

    // Filter berdasarkan status
    if (!empty($filter['status'])) {
        $status = escape($filter['status']);
        $sql .= " AND p.status = '$status'";
    }

    // Filter berdasarkan tanggal mulai
    if (!empty($filter['tanggal_mulai'])) {
        $tgl = escape($filter['tanggal_mulai']);
        $sql .= " AND DATE(p.created_at) >= '$tgl'";
    }

    // Filter berdasarkan tanggal selesai
    if (!empty($filter['tanggal_selesai'])) {
        $tgl = escape($filter['tanggal_selesai']);
        $sql .= " AND DATE(p.created_at) <= '$tgl'";
    }

    // Filter berdasarkan nama siswa (search)
    if (!empty($filter['search'])) {
        $search = escape($filter['search']);
        $sql .= " AND (u.nama LIKE '%$search%' OR p.judul LIKE '%$search%')";
    }

    // Urutkan dari terbaru
    $sql .= " ORDER BY p.created_at DESC";

    return query($sql);
}

/**
 * Fungsi mendapatkan pengaduan berdasarkan ID
 * @param int $id
 * @return array|null
 */
function get_pengaduan_by_id($id) {
    $id = (int)$id;
    return query_single("SELECT p.*, u.nama, u.kelas 
                         FROM pengaduan p 
                         JOIN users u ON p.user_id = u.id 
                         WHERE p.id = $id");
}

/**
 * Fungsi mendapatkan pengaduan milik siswa tertentu
 * @param int $user_id
 * @return array
 */
function get_pengaduan_siswa($user_id) {
    $user_id = (int)$user_id;
    return query("SELECT * FROM pengaduan 
                  WHERE user_id = $user_id 
                  ORDER BY created_at DESC");
}

/**
 * Fungsi update status pengaduan
 * @param int $id - ID pengaduan
 * @param string $status - Status baru
 * @return bool
 */
function update_status($id, $status) {
    $id = (int)$id;
    $status = escape($status);

    // Validasi status yang diizinkan
    $status_valid = ['menunggu', 'diproses', 'selesai'];
    if (!in_array($status, $status_valid)) {
        return false;
    }

    return execute("UPDATE pengaduan SET status = '$status' WHERE id = $id");
}

// ============================================
// FUNGSI FEEDBACK
// ============================================

/**
 * Fungsi menambah feedback dari admin
 * @param int $pengaduan_id - ID pengaduan
 * @param int $admin_id - ID admin
 * @param string $isi_feedback - Isi feedback
 * @return bool
 */
function tambah_feedback($pengaduan_id, $admin_id, $isi_feedback) {
    $pengaduan_id = (int)$pengaduan_id;
    $admin_id = (int)$admin_id;
    $isi_feedback = escape($isi_feedback);

    return execute("INSERT INTO feedback (pengaduan_id, admin_id, isi_feedback) 
                    VALUES ($pengaduan_id, $admin_id, '$isi_feedback')");
}

/**
 * Fungsi mendapatkan feedback suatu pengaduan
 * @param int $pengaduan_id
 * @return array
 */
function get_feedback($pengaduan_id) {
    $pengaduan_id = (int)$pengaduan_id;
    return query("SELECT f.*, u.nama AS admin_nama 
                  FROM feedback f 
                  JOIN users u ON f.admin_id = u.id 
                  WHERE f.pengaduan_id = $pengaduan_id 
                  ORDER BY f.created_at ASC");
}

// ============================================
// FUNGSI STATISTIK (DASHBOARD ADMIN)
// ============================================

/**
 * Fungsi mendapatkan statistik pengaduan
 * @return array
 */
function get_statistik() {
    $total = query_single("SELECT COUNT(*) AS total FROM pengaduan");
    $menunggu = query_single("SELECT COUNT(*) AS total FROM pengaduan WHERE status = 'menunggu'");
    $diproses = query_single("SELECT COUNT(*) AS total FROM pengaduan WHERE status = 'diproses'");
    $selesai = query_single("SELECT COUNT(*) AS total FROM pengaduan WHERE status = 'selesai'");

    return [
        'total' => $total['total'],
        'menunggu' => $menunggu['total'],
        'diproses' => $diproses['total'],
        'selesai' => $selesai['total']
    ];
}

/**
 * Fungsi mendapatkan statistik berdasarkan kategori
 * @return array
 */
function get_statistik_kategori() {
    return query("SELECT kategori, COUNT(*) AS jumlah 
                  FROM pengaduan 
                  GROUP BY kategori 
                  ORDER BY jumlah DESC");
}

/**
 * Fungsi mendapatkan statistik per siswa
 * @return array
 */
function get_statistik_siswa() {
    return query("SELECT u.nama, u.kelas, COUNT(p.id) AS jumlah 
                  FROM users u 
                  LEFT JOIN pengaduan p ON u.id = p.user_id 
                  WHERE u.role = 'siswa' 
                  GROUP BY u.id 
                  ORDER BY jumlah DESC 
                  LIMIT 5");
}

// ============================================
// FUNGSI VALIDASI
// ============================================

/**
 * Fungsi validasi form pengaduan
 * @param array $data - Data dari form
 * @return array - Array berisi error (kosong jika valid)
 */
function validasi_pengaduan($data) {
    $errors = [];

    // Validasi judul: wajib, minimal 5 karakter
    if (empty($data['judul'])) {
        $errors[] = "Judul pengaduan wajib diisi";
    } elseif (strlen($data['judul']) < 5) {
        $errors[] = "Judul minimal 5 karakter";
    } elseif (strlen($data['judul']) > 200) {
        $errors[] = "Judul maksimal 200 karakter";
    }

    // Validasi deskripsi: wajib, minimal 10 karakter
    if (empty($data['deskripsi'])) {
        $errors[] = "Deskripsi pengaduan wajib diisi";
    } elseif (strlen($data['deskripsi']) < 10) {
        $errors[] = "Deskripsi minimal 10 karakter";
    }

    // Validasi kategori: wajib dan harus salah satu dari pilihan
    $kategori_valid = ['Listrik', 'Meja-Kursi', 'AC/Fan', 'Komputer', 'Toilet', 'Lainnya'];
    if (empty($data['kategori'])) {
        $errors[] = "Kategori wajib dipilih";
    } elseif (!in_array($data['kategori'], $kategori_valid)) {
        $errors[] = "Kategori tidak valid";
    }

    return $errors;
}

// ============================================
// FUNGSI HAPUS PENGADUAN
// ============================================

/**
 * Fungsi hapus pengaduan (hanya untuk pemilik)
 * @param int $id - ID pengaduan
 * @param int $user_id - ID pemilik (untuk keamanan)
 * @return bool
 */
function hapus_pengaduan($id, $user_id) {
    $id = (int)$id;
    $user_id = (int)$user_id;

    // Keamanan: pastikan pengaduan benar-benar milik user ini
    $cek = query_single("SELECT id FROM pengaduan WHERE id = $id AND user_id = $user_id");

    if ($cek) {
        // Jika cocok, hapus dari database
        // (Feedback akan ikut terhapus otomatis karena ON DELETE CASCADE)
        return execute("DELETE FROM pengaduan WHERE id = $id AND user_id = $user_id");
    }

    return false;
}

/**
 * Fungsi validasi feedback
 * @param string $isi_feedback
 * @return array
 */
function validasi_feedback($isi_feedback) {
    $errors = [];

    if (empty($isi_feedback)) {
        $errors[] = "Feedback wajib diisi";
    } elseif (strlen($isi_feedback) < 5) {
        $errors[] = "Feedback minimal 5 karakter";
    }

    return $errors;
}

// ============================================
// FUNGSI HELPER TAMPILAN
// ============================================

/**
 * Fungsi menampilkan badge status dengan warna berbeda
 * @param string $status
 * @return string - HTML badge
 */
function badge_status($status) {
    $badge = [
        'menunggu' => '<span class="badge bg-warning text-dark">Menunggu</span>',
        'diproses' => '<span class="badge bg-info text-dark">Diproses</span>',
        'selesai'  => '<span class="badge bg-success">Selesai</span>'
    ];
    return $badge[$status] ?? $status;
}

/**
 * Fungsi menampilkan flash message
 * @return string - HTML alert atau kosong
 */
function flash_message() {
    if (isset($_SESSION['success'])) {
        $msg = $_SESSION['success'];
        unset($_SESSION['success']);
        return '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>' . escape($msg) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }

    if (isset($_SESSION['error'])) {
        $msg = $_SESSION['error'];
        unset($_SESSION['error']);
        return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>' . escape($msg) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }

    return '';
}

/**
 * Fungsi format tanggal Indonesia
 * @param string $tanggal - Format Y-m-d H:i:s
 * @return string - Format d M Y, H:i
 */
function format_tanggal($tanggal) {
    $bulan = [
        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar',
        '04' => 'Apr', '05' => 'Mei', '06' => 'Jun',
        '07' => 'Jul', '08' => 'Agu', '09' => 'Sep',
        '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
    ];

    $pecah = explode(' ', $tanggal);
    $tgl = explode('-', $pecah[0]);
    $jam = isset($pecah[1]) ? substr($pecah[1], 0, 5) : '';

    return $tgl[2] . ' ' . $bulan[$tgl[1]] . ' ' . $tgl[0] . (empty($jam) ? '' : ', ' . $jam);
}
?>
