<?php
header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>Reset Database</title><style>body{font-family: sans-serif; line-height: 1.6;} .log{padding: 5px; margin-bottom: 5px; border-radius: 3px;} .success{background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;} .error{background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;} pre{white-space: pre-wrap; word-wrap: break-word;}</style></head><body>";
echo "<h1>Proses Reset Database Otomatis</h1>";
echo "<p>Skrip ini akan mereset tabel `users` dan data terkait secara paksa. Waktu: " . date('Y-m-d H:i:s') . "</p><hr>";

// --- Detail Koneksi ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_akun_cerdas';

// --- Buat Koneksi ---
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    echo "<div class='log error'>❌ GAGAL terhubung ke database. Error: " . mysqli_connect_error() . "</div>";
    die("</body></html>");
}
echo "<div class='log success'>✅ BERHASIL terhubung ke database.</div>";

// --- Kumpulan Perintah SQL yang akan dieksekusi secara berurutan ---
$queries = [
    "Menonaktifkan Foreign Key Checks" => "SET FOREIGN_KEY_CHECKS=0;",
    "Menghapus tabel 'users' jika ada" => "DROP TABLE IF EXISTS `users`;",
    "Membuat ulang tabel 'users'" => "
        CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `nama` varchar(100) NOT NULL,
          `email` varchar(100) NOT NULL,
          `password` varchar(255) NOT NULL,
          `role` enum('user','admin') NOT NULL DEFAULT 'user',
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    "Memasukkan akun admin dummy" => "
        INSERT INTO `users` (nama, email, password, role)
        VALUES (
            'admin',
            'admin@gmail.com',
            '\$2y\$10\$Wq3l.L/v5sF9k/H7.x/E4.Ue5.x/S6G/fO.hJ/dG/eC.i.jF/kL',
            'admin'
        );",
    "Membersihkan tabel anak #1" => "TRUNCATE TABLE `user_progress`;",
    "Membersihkan tabel anak #2" => "TRUNCATE TABLE `quiz_attempts`;",
    "Membersihkan tabel anak #3" => "TRUNCATE TABLE `forum_posts`;",
    "Membersihkan tabel anak #4" => "TRUNCATE TABLE `forum_threads`;",
    "Mengaktifkan kembali Foreign Key Checks" => "SET FOREIGN_KEY_CHECKS=1;"
];

echo "<h2>Memulai Eksekusi Perintah...</h2>";

foreach ($queries as $description => $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "<div class='log success'>✅ <b>BERHASIL:</b> " . htmlspecialchars($description) . "</div>";
    } else {
        echo "<div class='log error'>❌ <b>GAGAL:</b> " . htmlspecialchars($description) . "<br><b>Error:</b> " . mysqli_error($conn) . "</div>";
        mysqli_close($conn);
        die("</body></html>");
    }
}

echo "<hr><h2>🎉 PROSES RESET SELESAI! 🎉</h2>";
echo "<p>Database Anda telah berhasil direset. Tabel `users` telah dibuat ulang dan akun admin telah ditambahkan.</p>";
echo "<p>Silakan coba login kembali sekarang. Anda bisa menutup halaman ini.</p>";

mysqli_close($conn);
echo "</body></html>";
?>