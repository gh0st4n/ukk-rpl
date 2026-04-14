<?php
// ============================================
// FUNGSI BASE URL (WAJIB DI ATAS)
// Supaya bisa dipanggil sebelum <head>
// ============================================
function base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $dir = dirname($script_name);

    // Hilangkan folder admin/siswa/auth dari base_url
    if (basename($dir) === 'admin' || basename($dir) === 'siswa' || basename($dir) === 'auth') {
        $dir = dirname($dir);
    }

    $base = $protocol . '://' . $host . $dir;
    return rtrim($base, '/') . '/';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Sistem Pengaduan'; ?> - Pengaduan Sekolah</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
