-- ============================================
-- DATABASE: pengaduan_sekolah
-- Sistem Pengaduan Sarana Sekolah
-- ============================================

CREATE DATABASE IF NOT EXISTS pengaduan_sekolah;
USE pengaduan_sekolah;

-- ============================================
-- TABEL: users
-- Menyimpan data pengguna (admin & siswa)
-- ============================================
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa',
    kelas VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL: pengaduan
-- Menyimpan data pengaduan dari siswa
-- ============================================
CREATE TABLE pengaduan (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT NOT NULL,
    kategori ENUM('Listrik', 'Meja-Kursi', 'AC/Fan', 'Komputer', 'Toilet', 'Lainnya') NOT NULL,
    status ENUM('menunggu', 'diproses', 'selesai') NOT NULL DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABEL: feedback
-- Menyimpan balasan/admin dari admin
-- ============================================
CREATE TABLE feedback (
    id INT(11) NOT NULL AUTO_INCREMENT,
    pengaduan_id INT(11) NOT NULL,
    admin_id INT(11) NOT NULL,
    isi_feedback TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (pengaduan_id) REFERENCES pengaduan(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATA AWAL: Admin default
-- Password: admin123 (menggunakan password_hash)
-- ============================================
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================
-- DATA AWAL: Contoh siswa
-- Password: siswa123 (menggunakan password_hash)
-- ============================================
INSERT INTO users (nama, username, password, role, kelas) VALUES
('Ahmad Fauzi', 'ahmad', '$2y$10$jU4V6VKqZGFMQtPKJEqMlOYxJMZP7sTJpVZHV0J5YqKVcxE0QyOPa', 'siswa', 'XII RPL 1'),
('Siti Nurhaliza', 'siti', '$2y$10$jU4V6VKqZGFMQtPKJEqMlOYxJMZP7sTJpVZHV0J5YqKVcxE0QyOPa', 'siswa', 'XI TKJ 2'),
('Budi Santoso', 'budi', '$2y$10$jU4V6VKqZGFMQtPKJEqMlOYxJMZP7sTJpVZHV0J5YqKVcxE0QyOPa', 'siswa', 'X MM 1');

-- ============================================
-- DATA CONTOH: Pengaduan awal
-- ============================================
INSERT INTO pengaduan (user_id, judul, deskripsi, kategori, status) VALUES
(2, 'AC Ruang Kelas Mati', 'AC di ruang kelas XII RPL 1 sudah 3 hari tidak menyala. Suasana kelas sangat panas sehingga mengganggu proses belajar mengajar.', 'AC/Fan', 'menunggu'),
(3, 'Komputer Lab Rusak', 'Komputer nomor 5 di lab komputer tidak bisa menyala. Sudah dicoba restart berkali-kali tetap tidak ada respon.', 'Komputer', 'diproses'),
(4, 'Keran Toilet Bocor', 'Keran air di toilet lantai 2 terus mengalir meskipun sudah diputar ke posisi off. Boros air.', 'Toilet', 'selesai');

-- ============================================
-- DATA CONTOH: Feedback dari admin
-- ============================================
INSERT INTO feedback (pengaduan_id, admin_id, isi_feedback) VALUES
(2, 1, 'Sudah dilaporkan ke tim teknisi. Komputer akan dicek besok pagi.'),
(3, 1, 'Keran sudah diperbaiki oleh petugas kebersihan. Terima kasih atas laporannya.');
