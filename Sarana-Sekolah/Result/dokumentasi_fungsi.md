---
title: "Dokumentasi Fungsi dan Prosedur"
subtitle: "Aplikasi Pengaduan Sarana Sekolah"
author: "Tan-dev"
date: "\today"
geometry: margin=2.5cm
---

# DOKUMENTASI FUNGSI DAN PROSEDUR
## Aplikasi Pengaduan Sarana Sekolah

Dokumen ini menjelaskan spesifikasi teknis dari fungsi-fungsi dan prosedur inti yang digunakan pada bagian *backend* sistem. Seluruh fungsi ditulis menggunakan bahasa pemrograman PHP Native prosedural dan berinteraksi langsung dengan database MySQL melalui ekstensi *mysqli*.

---

### 1. Prosedur Koneksi Database (`koneksi_db`)

**Lokasi File:** `config/database.php`

**Deskripsi:**  
Prosedur ini bertugas melakukan inisialisasi koneksi antara aplikasi PHP dengan server Database MySQL. Prosedur ini menggunakan fungsi bawaan PHP `mysqli_connect()` yang dibungkus ke dalam variabel global `$koneksi`. Variabel ini akan dipanggil oleh seluruh fungsi operasional di sistem. Prosedur ini juga menerapkan pengaturan *character set* `utf8mb4` untuk mendukung penyimpanan karakter universal termasuk emoji.

**Parameter:**
- `$DB_HOST` *(String)*: Alamat host server database (contoh: `localhost`).
- `$DB_USER` *(String)*: Username untuk autentikasi database.
- `$DB_PASS` *(String)*: Password untuk autentikasi database.
- `$DB_NAME` *(String)*: Nama database target yang akan diakses.

**Return Value:**
- *Object* `mysqli`: Objek koneksi database jika berhasil.
- *String* `die()`: Pesan error dan penghentian program (`exit`) jika koneksi gagal.

---

### 2. Fungsi `login($username, $password)`

**Lokasi File:** `includes/functions.php`

**Deskripsi:**  
Fungsi ini digunakan untuk memverifikasi kredensial yang dimasukkan oleh pengguna pada halaman login. Fungsi ini menerapkan keamanan dua tahap: pertama, mencari apakah `username` terdaftar di database; kedua, memverifikasi apakah password yang diinput cocok dengan *hash* yang tersimpan menggunakan fungsi bawaan PHP `password_verify()`.

**Parameter:**
- `$username` *(String)*: Username yang diinput oleh pengguna.
- `$password` *(String)*: Password dalam bentuk plain text yang diinput pengguna.

**Return Value:**
- *Array* `assoc`: Berisi data lengkap user (id, nama, role, dll) jika login berhasil.
- *Boolean* `false`: Jika username tidak ditemukan atau password salah.

---

### 3. Fungsi `buat_pengaduan($user_id, $judul, $deskripsi, $kategori)`

**Lokasi File:** `includes/functions.php`

**Deskripsi:**  
Fungsi ini bertugas menerima data dari formulir pengaduan yang diisi oleh siswa dan menyimpannya sebagai record baru ke dalam tabel `pengaduan`. Sebelum data dikirim ke database, fungsi ini melakukan sanitasi terhadap seluruh parameter menggunakan fungsi `escape()` untuk mencegah serangan SQL Injection.

**Parameter:**
- `$user_id` *(Integer)*: ID unik siswa yang sedang login (diambil dari sesi aktif).
- `$judul` *(String)*: Judul singkat pengaduan (maksimal 200 karakter).
- `$deskripsi` *(String)*: Penjelasan detail mengenai kondisi kerusakan.
- `$kategori` *(String)*: Klasifikasi kerusakan (Listrik, Meja-Kursi, AC/Fan, Komputer, Toilet, Lainnya).

**Return Value:**
- *Boolean* `true`: Jika perintah `INSERT INTO` berhasil dieksekusi.
- *Boolean* `false`: Jika terjadi kegagalan saat eksekusi query.

---

### 4. Fungsi `update_status($id, $status)`

**Lokasi File:** `includes/functions.php`

**Deskripsi:**  
Fungsi ini digunakan oleh admin untuk memperbarui tahapan penanganan suatu pengaduan. Fungsi ini memiliki mekanisme validasi internal di mana hanya menerima masukan berupa string yang sudah ditentukan (`menunggu`, `diproses`, `selesai`). Jika input status di luar ketiga kata tersebut, fungsi akan langsung mengembalikan nilai `false` untuk menjaga integritas data.

**Parameter:**
- `$id` *(Integer)*: ID unik pengaduan yang akan diperbarui statusnya.
- `$status` *(String)*: Status penanganan baru yang diizinkan.

**Return Value:**
- *Boolean* `true`: Jika perintah `UPDATE` berhasil dieksekusi.
- *Boolean* `false`: Jika status tidak valid atau query gagal dieksekusi.

---

### 5. Fungsi `tambah_feedback($pengaduan_id, $admin_id, $isi_feedback)`

**Lokasi File:** `includes/functions.php`

**Deskripsi:**  
Fungsi ini menangani penyimpanan pesan tanggapan dari admin terhadap sebuah pengaduan. Fungsi ini merekam dua buah Foreign Key (`pengaduan_id` dan `admin_id`) untuk menciptakan jejak audit (*audit trail*) agar diketahui siapa admin yang memberikan respons dan pada pengaduan mana respons tersebut diberikan. Input teks akan disanitasi terlebih dahulu sebelum masuk database.

**Parameter:**
- `$pengaduan_id` *(Integer)*: ID pengaduan yang sedang ditanggapi.
- `$admin_id` *(Integer)*: ID admin yang sedang login dan mengirim feedback.
- `$isi_feedback` *(String)*: Isi pesan tanggapan dari admin.

**Return Value:**
- *Boolean* `true`: Jika perintah `INSERT INTO` berhasil dieksekusi.
- *Boolean* `false`: Jika terjadi kegagalan saat eksekusi query.

---

### 6. Fungsi `hapus_pengaduan($id, $user_id)`

**Lokasi File:** `includes/functions.php`

**Deskripsi:**  
Fungsi ini mengimplementasikan fitur penghapusan data pengaduan oleh siswa. Untuk menjamin keamanan data antar pengguna, fungsi ini menerapkan pengecekan kepemilikan terlebih dahulu. Sistem akan mencocokkan `$id` pengaduan dengan `$user_id` pemohon. Jika data bukan miliknya, fungsi akan dibatalkan. Penghapusan pada tabel `pengaduan` secara otomatis akan menghapus data terkait di tabel `feedback` berkat mekanisme `ON DELETE CASCADE` pada level database.

**Parameter:**
- `$id` *(Integer)*: ID unik pengaduan yang akan dihapus.
- `$user_id` *(Integer)*: ID siswa yang mengirim permintaan hapus.

**Return Value:**
- *Boolean* `true`: Jika data berhasil ditemukan, validasi milik user lolos, dan `DELETE` berhasil dieksekusi.
- *Boolean* `false`: Jika data tidak ditemukan, bukan milik user, atau query gagal dieksekusi.
