// ============================================
// JAVASCRIPT UTAMA
// Fungsi-fungsi JS yang dipakai di seluruh halaman
// ============================================

/**
 * Toggle visibility password
 * Digunakan di halaman login
 */
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('bi-eye');
        eyeIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('bi-eye-slash');
        eyeIcon.classList.add('bi-eye');
    }
}

/**
 * Ubah status pengaduan (admin)
 * Mengisi form tersembunyi lalu submit
 * @param {int} id - ID pengaduan
 * @param {string} status - Status baru
 */
function ubahStatus(id, status) {
    // Konfirmasi sebelum mengubah status
    const statusText = {
        'menunggu': 'Menunggu',
        'diproses': 'Diproses',
        'selesai': 'Selesai'
    };

    const konfirmasi = confirm(
        `Ubah status pengaduan menjadi "${statusText[status]}"?`
    );

    if (konfirmasi) {
        document.getElementById('statusId').value = id;
        document.getElementById('statusValue').value = status;
        document.getElementById('formStatus').submit();
    }
}

/**
 * Penghitung karakter untuk textarea
 * Digunakan di form buat pengaduan
 */
document.addEventListener('DOMContentLoaded', function() {
    const deskripsi = document.getElementById('deskripsi');
    const charCount = document.getElementById('charCount');

    // Jika elemen ada di halaman ini
    if (deskripsi && charCount) {
        // Hitung saat halaman load (jika ada isi sebelumnya)
        charCount.textContent = deskripsi.value.length;

        // Hitung saat user mengetik
        deskripsi.addEventListener('input', function() {
            const length = this.value.length;

            charCount.textContent = length;

            // Ubah warna jika mendekati batas
            if (length > 900) {
                charCount.classList.add('text-danger');
                charCount.classList.remove('text-warning', 'text-muted');
            } else if (length > 700) {
                charCount.classList.add('text-warning');
                charCount.classList.remove('text-danger', 'text-muted');
            } else {
                charCount.classList.remove('text-danger', 'text-warning');
                charCount.classList.add('text-muted');
            }
        });
    }

    // ============================================
    // Auto-hide flash message setelah 5 detik
    // ============================================
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    });

    // ============================================
    // Efek smooth scroll untuk anchor link
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            e.preventDefault();
            const target = document.querySelector(targetId);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

/**
 * Fungsi konfirmasi hapus menggunakan Modal Bootstrap
 * @param {HTMLElement} form - Elemen form yang akan di-submit
 * @param {string} judul - Judul pengaduan
 */
function confirmHapus(form, judul) {
    // Set teks judul di modal
    document.getElementById('hapusJudul').textContent = '"' + judul + '"';

    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('modalHapus'));
    modal.show();

    // Set aksi tombol konfirmasi
    document.getElementById('btnKonfirmasiHapus').onclick = function() {
        // Langsung submit form yang sudah disimpan
        form.onsubmit = null; // Cegah loop
        form.submit();         // Eksekusi hapus
        modal.hide();
    };

    // Cegah form submit langsung saat pertama kali klik
    return false;
}
