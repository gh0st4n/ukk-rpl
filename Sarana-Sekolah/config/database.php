<?php
// ============================================
// KONFIGURASI DATABASE
// File ini menghubungkan aplikasi ke MySQL
// ============================================

// Pengaturan koneksi database
 $DB_HOST = '127.0.0.1';      // Host database (default XAMPP: localhost)
 $DB_USER = 'ukk';           // Username MySQL (default XAMPP: root)
 $DB_PASS = 'ukk123';               // Password MySQL (default XAMPP: kosong)
 $DB_NAME = 'pengaduan_sekolah'; // Nama database

// Membuat koneksi menggunakan mysqli
// $koneksi = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
 $koneksi = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Cek koneksi berhasil atau tidak
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke utf8mb4 agar mendukung emoji dan karakter khusus
mysqli_set_charset($koneksi, "utf8mb4");

// ============================================
// FUNGSI-FUNGSI HELPER DATABASE
// ============================================

/**
 * Fungsi untuk menjalankan query SELECT dan mengembalikan array
 * @param string $sql - Query SQL
 * @return array - Hasil query dalam bentuk array
 */
function query($sql) {
    global $koneksi;
    $result = mysqli_query($koneksi, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * Fungsi untuk mengambil satu baris data
 * @param string $sql - Query SQL
 * @return array|null - Satu baris data atau null
 */
function query_single($sql) {
    global $koneksi;
    $result = mysqli_query($koneksi, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Fungsi untuk menjalankan query INSERT, UPDATE, DELETE
 * @param string $sql - Query SQL
 * @return bool - True jika berhasil, False jika gagal
 */
function execute($sql) {
    global $koneksi;
    return mysqli_query($koneksi, $sql);
}

/**
 * Fungsi untuk mendapatkan ID terakhir yang di-insert
 * @return int - ID terakhir
 */
function get_last_id() {
    global $koneksi;
    return mysqli_insert_id($koneksi);
}

/**
 * Fungsi untuk mencegah SQL Injection
 * @param string $data - Data yang akan dibersihkan
 * @return string - Data yang sudah aman
 */
function escape($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}
?>
