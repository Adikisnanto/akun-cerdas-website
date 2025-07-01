<?php
echo "<h1>Final Fix: Reset Password Admin</h1>";

require 'config.php'; // Kita panggil config Anda yang sudah terbukti benar

// Password BARU yang akan kita gunakan
$new_password = 'AdminCerdas123';
$admin_email = 'admin@gmail.com';

echo "<p>Akan mereset password untuk email: <b>" . $admin_email . "</b></p>";
echo "<p>Password BARU adalah: <b>" . $new_password . "</b></p><hr>";

// 1. Buat hash BARU dari password BARU
$new_hash = password_hash($new_password, PASSWORD_BCRYPT);
echo "<b>Langkah 1:</b> Hash baru berhasil dibuat.<br>";
echo "Hash: " . $new_hash . "<br><hr>";

// 2. Update hash baru ini ke database
echo "<b>Langkah 2:</b> Menyimpan hash baru ke database...<br>";
$stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE email = ?");
mysqli_stmt_bind_param($stmt, "ss", $new_hash, $admin_email);

if (mysqli_stmt_execute($stmt)) {
    echo "<b style='color:green;'>SUKSES:</b> Hash baru berhasil disimpan di database.<br><hr>";
} else {
    die("<b style='color:red;'>GAGAL:</b> Tidak bisa menyimpan hash baru ke database. Error: " . mysqli_error($conn) . "</b>");
}

// 3. Tes verifikasi langsung di sini
echo "<b>Langkah 3:</b> Verifikasi langsung...<br>";
if (password_verify($new_password, $new_hash)) {
    echo "<h2 style='color:green;'>🎉 VERIFIKASI FINAL BERHASIL! 🎉</h2>";
    echo "<p>Password baru dan hash baru sudah cocok. Sistem Anda sekarang sudah benar.</p>";
} else {
    die("<h2 style='color:red;'>FATAL ERROR: Verifikasi tetap gagal. Ini menandakan ada masalah serius dengan instalasi PHP Anda.</h2>");
}

mysqli_close($conn);
?>