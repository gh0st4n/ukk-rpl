---
title: "Deskripsi Program"
subtitle: "Aplikasi Pengaduan Sarana Sekolah"
author: "Tan-dev"
date: "\today"
geometry: margin=2.5cm
---

# DESKRIPSI PROGRAM
## Aplikasi Pengaduan Sarana Sekolah

### 1. Latar Belakang

Pengelolaan sarana dan prasarana merupakan salah satu aspek penting dalam menunjang kegiatan belajar mengajar di sekolah. Namun, proses pelaporan kerusakan sarana di banyak institusi masih menggunakan metode manual, seperti melapor secara lisan kepada petugas atau menulis di buku pengaduan. Metode ini sering kali menyebabkan informasi tersendat, data tidak terdokumentasi dengan baik, serta sulit untuk memantau perkembangan penanganan secara *real-time*.

Berdasarkan permasalahan tersebut, dikembangkanlah "Aplikasi Pengaduan Sarana Sekolah", sebuah sistem informasi berbasis web yang bertujuan untuk mendigitalkan dan mempermudah proses pelaporan hingga penanganan kerusakan sarana sekolah.

### 2. Tujuan Program

Aplikasi ini dibuat dengan tujuan untuk:

1. Menyediakan media pelaporan kerusakan sarana sekolah yang cepat, mudah, dan dapat diakses kapan saja oleh siswa.
2. Mempermudah pihak admin (Tata Usaha/Teknisi) dalam menerima, mengelola, dan mendokumentasikan seluruh pengaduan secara terpusat.
3. Menjamin transparansi penanganan kerusakan melalui fitur pemantauan status (Menunggu, Diproses, Selesai).
4. Membangun komunikasi dua arah antara pelapor (siswa) dan penanggung jawab (admin) melalui fitur *feedback*.
5. Menghasilkan data statistik pengaduan yang akurat untuk bahan evaluasi pihak sekolah dalam mengalokasikan anggaran perbaikan.

### 3. Spesifikasi Teknis

Aplikasi ini dibangun menggunakan stack teknologi standar industri yang mudah diimplementasikan dan efisien, yang terdiri dari:

- **Bahasa Pemrograman Backend:** PHP Native (Prosedural) versi 8.x.
- **Basis Data (Database):** MySQL (RDBMS) dengan engine *InnoDB* untuk mendukung *Foreign Key* dan *Transactional*.
- **Antarmuka (Frontend):** HTML5, CSS3, JavaScript Vanilla, dan framework CSS Bootstrap 5 untuk mempercepat desain responsif.
- **Keamanan:** Penerapan fungsi *password hashing* (bcrypt), sanitasi input (`mysqli_real_escape_string`) untuk mencegah SQL Injection, serta mekanisme *Session* untuk otorisasi halaman.

### 4. Target Pengguna dan Hak Akses

Sistem ini mengimplementasikan konsep *Role-Based Access Control* (RBAC) sederhana, yang membagi pengguna menjadi dua level utama:

**1. Siswa (Pelapor)**
- Melakukan autentikasi (login) untuk mengakses sistem.
- Mengisi formulir pengaduan baru (judul, deskripsi, dan kategori kerusakan).
- Melihat daftar histori pengaduan yang pernah dibuat.
- Memantau status penanganan pengaduan.
- Membaca respons/feedback yang diberikan oleh admin.
- Menghapus data pengaduan yang belum diproses (dengan validasi kepemilikan data).

**2. Admin (Pengelola)**
- Melihat ringkasan statistik (total pengaduan, grafik per kategori) pada *Dashboard*.
- Melihat seluruh daftar pengaduan dari semua siswa.
- Melakukan penyaringan (filter) data berdasarkan kategori, status, tanggal, dan kata kunci pencarian.
- Mengubah status pengaduan menjadi "Diproses" atau "Selesai".
- Memberikan feedback/tanggapan resmi pada setiap pengaduan.

### 5. Fitur Utama Sistem

1. **Sistem Autentikasi & Otorisasi:** Halaman login aman yang mengarahkan pengguna ke dashboard sesuai dengan role masing-masing.
2. **Pencatatan Pengaduan (CRUD):** Form input tervalidasi untuk membuat laporan, serta fitur untuk melihat dan menghapus laporan.
3. **Alur Kerja Status:** Perubahan status secara bertahap untuk memantau progress penanganan kerusakan.
4. **Sistem Interaksi Feedback:** Fitur komentar yang memungkinkan admin memberikan penjelasan lanjutan kepada siswa mengenai tindak lanjut kerusakan.
5. **Dashboard Analitik:** Visualisasi data menggunakan progress bar untuk menampilkan distribusi pengaduan berdasarkan kategori dan identifikasi siswa yang paling aktif melapor.
6. **Pencarian dan Filter Multi-Kriteria:** Fitur pada halaman admin untuk menelusuri data pengaduan secara spesifik berdasarkan kebutuhan.

### 6. Kesimpulan

Secara keseluruhan, "Aplikasi Pengaduan Sarana Sekolah" ini dirancang untuk mengubah proses pelaporan manual menjadi sistem terdigitalisasi yang terintegrasi. Dengan adanya aplikasi ini, diharapkan proses penanganan sarana prasarana sekolah dapat berjalan lebih efektif, efisien, transparan, serta menghasilkan dokumentasi data yang akurat untuk kepentingan evaluasi institusi sekolah.
