<?php
// Memanggil config.php akan otomatis memulai sesi
require 'config.php';

// Menghapus semua variabel sesi
session_unset();

// Menghancurkan sesi secara total
session_destroy();

// Mengarahkan pengguna kembali ke halaman depan
header("Location: index.php");
exit();
?>