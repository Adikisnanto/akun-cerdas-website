<?php
echo "<h1>Tes Verifikasi Password Pengguna Biasa</h1><hr>";

// --- GANTI PASSWORD DI BAWAH INI ---
$passwordYgDiketik = 'password_untuk_dian_disini';

// Hash ini saya ambil dari screenshot Anda untuk user 'dian'
$hashDariDatabase = '$2y$10$.H3jH2aRZaRfylyEH417ThheKOF0B1i4/fgCCHuMsceB5i'; 

// --------------------------------------------------

echo "<b>Password yang diuji:</b> " . $passwordYgDiketik . "<br>";
echo "<b>Hash dari Database:</b> " . htmlspecialchars($hashDariDatabase) . "<br><br>";

if (password_verify($passwordYgDiketik, $hashDariDatabase)) {
    echo '<h3 style="color: green;">VERIFIKASI BERHASIL: Password cocok dengan hash.</h3>';
    echo '<p>Jika ini berhasil, berarti masalahnya ada di proses logout atau sesi.</p>';
} else {
    echo '<h3 style="color: red;">VERIFIKASI GAGAL: Password TIDAK cocok dengan hash.</h3>';
    echo '<p>Ini membuktikan password yang Anda masukkan untuk login salah. Solusinya adalah mereset password pengguna ini.</p>';
}
?>