<?php
echo "<h1>Ultimate Debugging Test - Akun Cerdas</h1>";
echo "Waktu Tes: " . date('Y-m-d H:i:s') . "<br>";
echo "Versi PHP Anda: " . phpversion() . "<br><hr>";

// ===================================================================
// 1. Detail Koneksi Database (Langsung di sini, mengabaikan config.php)
// ===================================================================
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_akun_cerdas';

echo "<h2>Langkah 1: Mencoba Koneksi ke Database...</h2>";
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    echo '<p style="color: red; font-weight: bold;">KONEKSI GAGAL!</p>';
    echo "<p>Pesan Error: " . mysqli_connect_error() . "</p>";
    echo "<p><b>SOLUSI:</b> Pastikan detail koneksi di atas (terutama `db_name`) sudah benar dan server MySQL Anda di XAMPP sudah berjalan.</p>";
    die(); // Hentikan skrip jika koneksi gagal
}

echo '<p style="color: green; font-weight: bold;">KONEKSI BERHASIL!</p><hr>';

// ===================================================================
// 2. Detail Akun yang akan di Tes
// ===================================================================
$email_to_test = 'admin@gmail.com';
$password_to_test = '112233';

echo "<h2>Langkah 2: Mencari User di Database...</h2>";
echo "<p>Mencari user dengan email: <b>" . $email_to_test . "</b></p>";

$sql = "SELECT id, nama, password, role FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email_to_test);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo '<p style="color: red; font-weight: bold;">USER TIDAK DITEMUKAN!</p>';
    echo "<p><b>SOLUSI:</b> Ini berarti data user dengan email '" . $email_to_test . "' tidak ada di database. Silakan jalankan kembali skrip SQL untuk membuat ulang tabel `users` dari jawaban saya sebelumnya.</p>";
    die();
}

echo '<p style="color: green; font-weight: bold;">USER DITEMUKAN!</p>';
echo "<pre>";
print_r($user);
echo "</pre><hr>";

// ===================================================================
// 3. Tes Verifikasi Password
// ===================================================================
echo "<h2>Langkah 3: Memverifikasi Password...</h2>";
echo "<p>Password yang diketik: <b>" . $password_to_test . "</b></p>";
echo "<p>Hash dari database: <b>" . htmlspecialchars($user['password']) . "</b></p>";

$is_verified = password_verify($password_to_test, $user['password']);

if ($is_verified) {
    echo '<h3 style="color: green; font-weight: bold;">VERIFIKASI BERHASIL!</h3>';
    echo "<p>Password dan Hash COCOK. Jika tes ini berhasil tapi login tetap gagal, berarti ada masalah fundamental pada sistem sesi (session) atau file `config.php` Anda.</p>";
} else {
    echo '<h3 style="color: red; font-weight: bold;">VERIFIKASI GAGAL!</h3>';
    echo "<p>Password dan Hash TIDAK COCOK. Ini sangat aneh jika Anda sudah menjalankan skrip reset database. Ini bisa terjadi jika ada masalah dengan versi PHP Anda atau ada karakter aneh yang tidak terlihat.</p>";
}

mysqli_close($conn);
?>