<?php
// ============================================
// PROSES LOGOUT
// Menghapus semua session dan redirect ke login
// ============================================
session_start();

// Hapus semua variabel session
 $_SESSION = [];

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>
