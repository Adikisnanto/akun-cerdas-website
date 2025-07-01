<?php
echo "<h1>Reset Password Pengguna Spesifik</h1>";

require 'config.php'; // Memanggil koneksi database

// --- KITA PERBAIKI EMAIL DI SINI ---

$email_to_reset = 'meli@gmail.com';
$new_password = 'meli123';
// ----------------------------------------------------


echo "<p>Mencoba mereset password untuk email: <b>" . htmlspecialchars($email_to_reset) . "</b></p>";
echo "<p>Password baru akan diatur menjadi: <b>" . htmlspecialchars($new_password) . "</b></p><hr>";

// 1. Buat hash dari password baru
$new_hash = password_hash($new_password, PASSWORD_BCRYPT);
echo "Hash baru berhasil dibuat: " . $new_hash . "<br><hr>";

// 2. Update hash baru ini ke database
echo "Menjalankan perintah UPDATE ke database...<br>";
$stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE email = ?");
mysqli_stmt_bind_param($stmt, "ss", $new_hash, $email_to_reset);

if (mysqli_stmt_execute($stmt)) {
    // Cek apakah ada baris yang terpengaruh
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    if ($affected_rows > 0) {
        echo "<h2 style='color: green;'>✅ BERHASIL MERESET PASSWORD!</h2>";
        echo "<p>Password untuk <b>" . htmlspecialchars($email_to_reset) . "</b> telah diubah menjadi <b>'" . htmlspecialchars($new_password) . "'</b>.</p>";
        echo "<p>Silakan coba login kembali sekarang.</p>";
    } else {
        echo "<h2 style='color: red;'>❌ GAGAL: Tidak ada pengguna dengan email '" . htmlspecialchars($email_to_reset) . "' yang ditemukan di database.</h2>";
    }
} else {
    echo "<h2 style='color: red;'>❌ GAGAL: Terjadi kesalahan saat menjalankan query UPDATE.</h2>";
}

mysqli_close($conn);
?>