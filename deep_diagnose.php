<?php
echo "<h1>Diagnostik Mendalam - Data Tabel Pengguna</h1><hr>";

require 'config.php';

echo "Mencoba mengambil semua data dari tabel `users`...<br><br>";

$result = mysqli_query($conn, "SELECT id, nama, email, role FROM users");

if (!$result) {
    die("<b style='color:red;'>GAGAL: Query untuk mengambil data pengguna gagal.</b>");
}

if (mysqli_num_rows($result) == 0) {
    echo "<p><b>Hasil:</b> Tabel `users` Anda benar-benar kosong. Tidak ada data pengguna sama sekali.</p>";
    echo "<p><b>Solusi:</b> Anda perlu menjalankan kembali skrip untuk membuat ulang tabel `users` dan mengisi akun admin.</p>";
} else {
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<tr style="background-color:#eee;">
            <th>ID</th>
            <th>Nama</th>
            <th>Email yang Terlihat</th>
            <th>Panjang String (Harusnya 16 untuk dianai@gmail.com)</th>
            <th>Tes Perbandingan Tepat (diana@gmail.com)</th>
            <th>Tes Perbandingan Tepat (dianai@gmail.com)</th>
          </tr>';

    while ($user = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $user['id'] . '</td>';
        echo '<td>' . htmlspecialchars($user['nama']) . '</td>';
        echo '<td>' . htmlspecialchars($user['email']) . '</td>';
        
        // Menampilkan panjang string email, ini akan mengungkap karakter tersembunyi
        echo '<td>' . strlen($user['email']) . '</td>';

        // Melakukan perbandingan langsung di sini
        if (trim($user['email']) === 'diana@gmail.com') {
            echo '<td style="background-color:yellow;">COCOK DENGAN "diana"</td>';
        } else {
            echo '<td>Tidak Cocok</td>';
        }

        if (trim($user['email']) === 'dianai@gmail.com') {
            echo '<td style="background-color:lightgreen;">COCOK DENGAN "dianai"</td>';
        } else {
            echo '<td>Tidak Cocok</td>';
        }

        echo '</tr>';
    }
    echo '</table>';
}

mysqli_close($conn);
?>