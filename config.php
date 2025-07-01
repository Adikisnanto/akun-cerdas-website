<?php
// Mulai session di setiap halaman yang menggunakan file ini
session_start();

// Detail koneksi database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Pastikan ini benar-benar kosong
$db_name = 'db_akun_cerdas';

// Buat koneksi
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>