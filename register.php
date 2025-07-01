<?php
require 'config.php';

$error = '';
$success = '';
$page_title = "Daftar - Akun Cerdas";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    if (empty($nama) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $password_confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } 
    // Validasi Kekuatan Password
    elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password lemah! Password harus minimal 8 karakter, mengandung setidaknya satu huruf besar, satu huruf kecil, dan satu angka.";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        // Cek apakah email sudah terdaftar
        $sql_check = "SELECT id FROM users WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Hash password sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Simpan ke database
            $sql_insert = "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "sss", $nama, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt_insert)) {
                $success = "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
    <style>
        .auth-container { max-width: 450px; }
    </style>
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="auth-container card p-4 shadow-sm">
            <h3 class="text-center fw-bold text-primary mb-4">Daftar Akun Cerdas</h3>
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php else: ?>
            <form action="register.php" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required aria-describedby="passwordHelp" autocomplete="new-password">
                    <div id="passwordHelp" class="form-text">
                        Min. 8 karakter, mengandung huruf besar, kecil, dan angka.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Daftar</button>
            </form>
            <?php endif; ?>
            <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        </div>
    </div>
</body>
</html>