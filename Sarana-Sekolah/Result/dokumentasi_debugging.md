---
title: "Dokumentasi Debugging"
subtitle: "Aplikasi Pengaduan Sarana Sekolah"
author: "Tan-dev"
date: "\today"
geometry: margin=2.5cm
---

# DOKUMENTASI DEBUGGING
## Aplikasi Pengaduan Sarana Sekolah

Dokumen ini mencatat beberapa permasalahan teknis (error) yang ditemui selama proses pengembangan dan *deployment* aplikasi, beserta analisis penyebab dan langkah penyelesaiannya. Proses ini menjadi bagian penting dalam pengujian sistem untuk memastikan aplikasi berjalan dengan stabil.

---

### 1. Error: `Fatal error: Uncaught Error: Call to undefined function mysqli_connect()`

**Konteks:**  
Error terjadi saat aplikasi dijalankan dan mencoba memuat file `config/database.php` untuk melakukan koneksi ke database.

**Penyebab:**  
Meskipun paket database (`php-mysql`) telah terinstal di sistem operasi, ekstensi `mysqli` belum diaktifkan di dalam file konfigurasi PHP (`php.ini`). Tanpa baris konfigurasi ini, mesin PHP tidak akan memuat modul koneksi MySQL.

**Solusi:**  
1. Mengidentifikasi lokasi direktori konfigurasi tambahan PHP menggunakan perintah `php --ini` (ditemukan di `/etc/php8.4/conf.d/`).
2. Membuat file konfigurasi drop-in baru bernama `mysqli.ini` di dalam direktori tersebut.
3. Menambahkan baris `extension=mysqli` ke dalam file tersebut.
4. Melakukan restart pada service PHP Development Server agar perubahan konfigurasi terbaca.

**Pencegahan:**  
Sebelum memulai pengembangan, selalu verifikasi bahwa seluruh ekstensi yang dibutuhkan telah aktif menggunakan perintah `php -m | grep [nama_ekstensi]`.

---

### 2. Error: `Fatal error: Uncaught mysqli_sql_exception: Access denied for user 'root'@'localhost'`

**Konteks:**  
Error terjadi di baris `mysqli_connect()` setelah ekstensi `mysqli` berhasil diaktifkan.

**Penyebab:**  
Pada versi MariaDB terbaru (v11.x), autentikasi default untuk user `root` menggunakan plugin `unix_socket`. Artinya, user `root` hanya dapat mengakses database melalui terminal (menggunakan perintah `sudo mariadb`), sedangkan aplikasi PHP yang berjalan via web server dianggap sebagai user sistem biasa, sehingga akses ditolak.

**Solusi:**  
1. Membuat user database baru yang secara khusus diperuntukkan bagi aplikasi web (bukan menggunakan akun `root`).
2. Memberikan hak akses (privilege) yang terbatas hanya pada database `pengaduan_sekolah` kepada user baru tersebut.
3. Memperbarui variabel kredensial (`$DB_USER` dan `$DB_PASS`) pada file `config/database.php` sesuai dengan user baru yang telah dibuat.

**Pencegahan:**  
Hindari penggunaan akun `root` untuk koneksi aplikasi web keamanan (Best Practice). Selalu buat akun terpisah dengan hak akses minimal sesuai kebutuhan database.

---

### 3. Error: `ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails`

**Konteks:**  
Error terjadi saat menjalankan perintah *import* file `database.sql` yang mengandung perintah `INSERT` ke tabel `feedback`.

**Penyebab:**  
Pelanggaran integritas referensial (*Referential Integrity*). Perintah SQL mencoba menyisipkan data feedback untuk `pengaduan_id = 4`, namun pada urutan eksekusi sebelumnya, data yang di-*insert* ke tabel `pengaduan` hanya sebanyak 3 baris (sehingga ID yang tersedia hanya 1, 2, dan 3). Mesin database menolak operasi ini karena tidak dapat menemukan data induk (parent) yang dirujuk oleh Foreign Key.

**Solusi:**  
1. Melakukan pengecekan ulang logika urutan ID pada skrip SQL.
2. Memperbaiki nilai `pengaduan_id` pada skrip `INSERT` tabel `feedback` agar sesuai dengan ID yang memang ada di tabel `pengaduan`.
3. Melakukan *drop* dan *re-import* ulang database setelah perbaikan skrip dilakukan.

**Pencegahan:**  
Saat membuat skrip data awal (dummy data) secara manual, selalu pastikan urutan *insert* dimulai dari tabel induk (parent) baru kemudian tabel anak (child), dan pastikan nilai Foreign Key yang dirujuk benar-benar eksis.

---

### 4. Error: `Parse error: syntax error, unexpected identifier "proses"`

**Konteks:**  
Error muncul pada halaman `auth/login.php` yang mengakibatkan seluruh halaman menampilkan *HTTP Error 500*.

**Penyebab:**  
Kesalahan penulisan sintaks komentar pada baris kedua file PHP. Kode yang dituliskan adalah `- Menangani proses...` di dalam blok komentar. Tanda strip (`-`) tanpa diawali tanda garis miring ganda (`//`) membuat interpreter PHP menganggap kata `"proses"` sebagai sebuah perintah atau identifier, yang tentu saja tidak dikenali oleh mesin PHP.

**Solusi:**  
1. Membuka file yang disebutkan pada pesan error (baris ke-4).
2. Menambahkan tanda komentar PHP yang benar (`//`) di awal baris sehingga menjadi `// Menangani proses...`.
3. Melakukan hard refresh pada browser untuk memastikan cache halaman error tergantikan.

**Pencegahan:**  
Gunakan fitur *Syntax Highlighting* dan *Linting* pada editor teks (seperti VS Code atau Nano dengan paket nanosyntax) untuk mendeteksi kesalahan penulisan sintaks secara langsung sebelum kode dijalankan.
