<?php
require 'config.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

$stmt = mysqli_prepare($conn, "SELECT a.*, u.nama as author_name FROM articles a JOIN users u ON a.author_id = u.id WHERE a.slug = ? AND a.status = 'published'");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$article = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$article) { die("Artikel tidak ditemukan."); }

$page_title = htmlspecialchars($article['title']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
</head>
<body>
    <?php include 'partials/main_navbar.php'; ?>
    <div class="container mt-5" style="max-width: 800px;">
        <h1 class="mb-3 fw-bold"><?= htmlspecialchars($article['title']) ?></h1>
        <p class="text-muted">
            Oleh <?= htmlspecialchars($article['author_name']) ?> | Dipublikasikan pada <?= date('d F Y', strtotime($article['created_at'])) ?>
        </p>
        <img src="<?= htmlspecialchars($article['thumbnail_url']) ?>" class="img-fluid rounded my-4" alt="<?= htmlspecialchars($article['title']) ?>">
        
        <div class="article-content">
            <?= nl2br(htmlspecialchars($article['content'])) // nl2br untuk mengubah baris baru menjadi tag <br> ?>
        </div>
    </div>
</body>
</html>