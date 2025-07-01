<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$thread_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];
$error = '';

// Logika untuk mengirim balasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_content'])) {
    $content = trim($_POST['reply_content']);
    if (!empty($content)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO forum_posts (thread_id, user_id, content) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iis", $thread_id, $user_id, $content);
        mysqli_stmt_execute($stmt);
        header("Location: thread.php?id=" . $thread_id); // Refresh halaman untuk lihat balasan baru
        exit();
    }
}

// Ambil data thread utama
$thread_stmt = mysqli_prepare($conn, "SELECT ft.title, ft.content, ft.created_at, u.nama as author_name FROM forum_threads ft JOIN users u ON ft.user_id = u.id WHERE ft.id = ?");
mysqli_stmt_bind_param($thread_stmt, "i", $thread_id);
mysqli_stmt_execute($thread_stmt);
$thread = mysqli_fetch_assoc(mysqli_stmt_get_result($thread_stmt));

if (!$thread) { die("Topik tidak ditemukan."); }

$page_title = htmlspecialchars($thread['title']) . " - Forum";

// Ambil semua balasan untuk thread ini
$posts_stmt = mysqli_prepare($conn, "SELECT fp.content, fp.created_at, u.nama as author_name FROM forum_posts fp JOIN users u ON fp.user_id = u.id WHERE fp.thread_id = ? ORDER BY fp.created_at ASC");
mysqli_stmt_bind_param($posts_stmt, "i", $thread_id);
mysqli_stmt_execute($posts_stmt);
$posts_result = mysqli_stmt_get_result($posts_stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
</head>
<body class="bg-light">
    <?php include 'partials/navbar.php'; ?>
    <div class="container mt-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h2 class="mb-0"><?= htmlspecialchars($thread['title']) ?></h2>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($thread['content'])) ?></p>
            </div>
            <div class="card-footer text-muted">
                Ditulis oleh <?= htmlspecialchars($thread['author_name']) ?> pada <?= date('d M Y, H:i', strtotime($thread['created_at'])) ?>
            </div>
        </div>

        <h4 class="mb-3">Balasan</h4>
        <?php while($post = mysqli_fetch_assoc($posts_result)): ?>
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
            </div>
            <div class="card-footer text-muted small">
                Oleh <?= htmlspecialchars($post['author_name']) ?> pada <?= date('d M Y, H:i', strtotime($post['created_at'])) ?>
            </div>
        </div>
        <?php endwhile; ?>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h5 class="card-title">Tulis Balasan Anda</h5>
                <form method="POST">
                    <div class="mb-3">
                        <textarea class="form-control" name="reply_content" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Balasan</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>