<?php
// Pastikan config.php sudah di-include sebelumnya
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, tendang ke halaman login
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    // Jika sudah login tapi bukan admin, tendang ke dashboard user
    // atau tampilkan pesan error
    die("Akses ditolak. Anda bukan admin.");
}
?>