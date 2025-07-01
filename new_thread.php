<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$page_title = "Buat Topik Baru - Akun Cerdas";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $error = "Judul dan isi konten tidak boleh kosong.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO forum_threads (user_id, title, content) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $title, $content);
        if (mysqli_stmt_execute($stmt)) {
            $new_thread_id = mysqli_insert_id($conn);
            header("Location: thread.php?id=" . $new_thread_id);
            exit();
        } else {
            $error = "Gagal membuat topik. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
</head>
<body class="bg-light">
    <?php include 'partials/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="fw-bold mb-4">Buat Topik Diskusi Baru</h2>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Topik</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Isi Pertanyaan/Diskusi</label>
                        <textarea class="form-control" id="content" name="content" rows="8" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publikasikan Topik</button>
                    <a href="forum.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>