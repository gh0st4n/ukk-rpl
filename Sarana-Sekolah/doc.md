# DOKUMENTASI SETUP DAN DEPLOYMENT
## Aplikasi Pengaduan Sarana Sekolah

Dokumen ini berisi panduan teknis langkah demi langkah untuk melakukan instalasi, konfigurasi, dan *deployment* aplikasi pada tiga lingkungan sistem operasi yang berbeda: Windows (XAMPP), Linux dengan layanan SystemD, dan Linux dengan layanan Runit (Void Linux/T4n OS).

## A. Setup di Windows (Menggunakan XAMPP)

Lingkungan Windows menggunakan server web Apache dan database MySQL bawaan dari paket XAMPP.

### 1. Persiapan
- Pastikan aplikasi **XAMPP** telah terinstal di komputer.
- Copy seluruh folder project ke dalam direktori `C:\xampp\htdocs\`.

### 2. Konfigurasi Database
1. Buka aplikasi **XAMPP Control Panel**, klik **Start** pada menu *Apache* dan *MySQL*.
2. Buka browser, akses alamat `http://localhost/phpmyadmin`.
3. Buat database baru dengan nama `pengaduan_sekolah`.
4. Pilih database tersebut, klik tab **Import**, unggah file `database.sql` yang ada di folder project, lalu klik **Go**.

### 3. Konfigurasi Kredensial & Password
Secara default, XAMPP menggunakan user `root` tanpa password. Namun, hash password di file SQL bersifat dummy sehingga perlu diperbarui:
1. Buka kembali phpMyAdmin, masuk ke database `pengaduan_sekolah`.
2. Buka tabel `users`.
3. Klik tombol **Edit** pada baris user `admin`.
4. Pada kolom `password`, ubah fungsi di dropdown sebelah kiri menjadi `MD5` (atau `PASSWORD`), lalu masukkan `admin123` pada kolom value. Lakukan hal serupa untuk user siswa (`ahmad`, `siti`, `budi`) dengan password `siswa123`.
5. Pastikan file `config/database.php` menggunakan konfigurasi:
   - `$DB_USER = 'root';`
   - `$DB_PASS = '';`

### 4. Akses Aplikasi
Buka browser dan akses: `http://localhost/nama_folder_project`

## B. Setup di Linux SystemD (Debian/Ubuntu/CentOS)

Lingkungan ini menggunakan layanan *systemd* sebagai manajer servis dan paket distribusi standar (APT atau YUM).

### 1. Instalasi Paket
```bash
# Contoh untuk Debian/Ubuntu
sudo apt update
sudo apt install apache2 mariadb-server php php-mysql libapache2-mod-php -y
```

### 2. Konfigurasi Database
1. Hidupkan service database:
   ```bash
   sudo systemctl start mariadb
   sudo systemctl enable mariadb
   ```
2. Amankan instalasi database (opsional tapi disarankan):
   ```bash
   sudo mysql_secure_installation
   ```
3. Import struktur database dan buat user khusus:
   ```bash
   sudo mariadb < database.sql
   sudo mariadb -e "CREATE USER 'ukk'@'localhost' IDENTIFIED BY 'ukk123'; GRANT ALL PRIVILEGES ON pengaduan_sekolah.* TO 'ukk'@'localhost'; FLUSH PRIVILEGES;"
   ```

### 3. Generate Password (Hash Bcrypt)
Karena PHP tidak bisa menghasilkan hash yang sama secara manual, gunakan PHP CLI:
```bash
HASH_ADMIN=$(php -r "echo password_hash('admin123', PASSWORD_DEFAULT);")
HASH_SISWA=$(php -r "echo password_hash('siswa123', PASSWORD_DEFAULT);")
sudo mariadb -e "USE pengaduan_sekolah; UPDATE users SET password = '$HASH_ADMIN' WHERE username = 'admin'; UPDATE users SET password = '$HASH_SISWA' WHERE username IN ('ahmad', 'siti', 'budi');"
```

### 4. Konfigurasi Aplikasi
1. Copy folder project ke `/var/www/html/`.
2. Edit file `config/database.php`, sesuaikan kredensialnya:
   - `$DB_HOST = '127.0.0.1';` *(Direkomendasikan untuk menghindari error socket)*
   - `$DB_USER = 'ukk';`
   - `$DB_PASS = 'ukk123';`
3. Atur permission folder:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/nama_folder
   ```

### 5. Akses Aplikasi
Restart Apache `sudo systemctl restart apache2`, lalu akses `http://localhost/nama_folder`.

## C. Setup di Linux Runit (Void Linux / T4n OS)

Lingkungan ini memiliki karakteristik unik: tidak memiliki file servis bawaan untuk MariaDB, letak konfigurasi PHP yang berbeda, dan masalah *socket file* yang spesifik.

### 1. Instalasi Paket
```bash
sudo xbps-install -S mariadb mariadb-client php php-mysql
```

### 2. Membuat Service MariaDB Manual
Karena paket MariaDB di Void Linux tidak menyertakan skrip layanan, kita harus membuatnya secara manual:
```bash
sudo mkdir -p /etc/sv/mariadb
echo '#!/bin/sh' | sudo tee /etc/sv/mariadb/run
echo 'exec mariadbd --user=mysql --datadir=/var/lib/mysql' | sudo tee -a /etc/sv/mariadb/run
sudo chmod +x /etc/sv/mariadb/run
sudo ln -s /etc/sv/mariadb /var/service/
```
Tunggu beberapa detik, cek statusnya dengan `sudo sv status mariadb` (harus berstatus `run`).

### 3. Mengaktifkan Ekstensi PHP mysqli
Secara default, ekstensi `mysqli` tidak tersimpan di `php.ini` utama, melainkan harus dimuat secara terpisah:
```bash
sudo mkdir -p /etc/php8.4/conf.d
echo 'extension=mysqli' | sudo tee /etc/php8.4/conf.d/mysqli.ini
```
Verifikasi dengan perintah `php -m | grep mysqli`.

### 4. Setup Database (Menggunakan TCP/IP)
Pada sistem ini, koneksi via *socket* sering mengalami error path. Solusi paling aman adalah memaksa koneksi melalui jaringan lokal (TCP/IP `127.0.0.1`):
```bash
sudo mariadb -h 127.0.0.1 -u root < database.sql
sudo mariadb -h 127.0.0.1 -u root -e "CREATE USER 'ukk'@'localhost' IDENTIFIED BY 'ukk123'; GRANT ALL PRIVILEGES ON pengaduan_sekolah.* TO 'ukk'@'localhost'; FLUSH PRIVILEGES;"
```
Generate dan update password:
```bash
HASH_ADMIN=$(php -r "echo password_hash('admin123', PASSWORD_DEFAULT);")
HASH_SISWA=$(php -r "echo password_hash('siswa123', PASSWORD_DEFAULT);")
sudo mariadb -h 127.0.0.1 -u root -e "USE pengaduan_sekolah; UPDATE users SET password = '$HASH_ADMIN' WHERE username = 'admin'; UPDATE users SET password = '$HASH_SISWA' WHERE username IN ('ahmad', 'siti', 'budi');"
```

### 5. Konfigurasi Aplikasi
Edit file `config/database.php`, wajib menggunakan `127.0.0.1` (bukan `localhost`) agar terhubung via TCP:
- `$DB_HOST = '127.0.0.1';`
- `$DB_USER = 'ukk';`
- `$DB_PASS = 'ukk123';`

### 6. Menjalankan Aplikasi (PHP Built-in Server)
Karena tidak menggunakan Apache, jalankan server bawaan PHP:
```bash
cd /path/ke/project
php -S localhost:8000
```
Akses melalui browser: `http://localhost:8000`

## D. Akun Default Untuk Pengujian

Berikut adalah daftar akun yang telah dikonfigurasi untuk keperluan pengujian fitur sistem:

| Role    | Username | Password  |
|---------|----------|-----------|
| Admin   | admin    | admin123  |
| Siswa   | ahmad    | siswa123  |
| Siswa   | siti     | siswa123  |
| Siswa   | budi     | siswa123  |
```

---
