<?php
require 'config.php';
$page_title = "Blog - Akun Cerdas";

$query = "SELECT a.*, u.nama as author_name FROM articles a JOIN users u ON a.author_id = u.id WHERE a.status = 'published' ORDER BY a.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
</head>
<body>
    <?php include 'partials/main_navbar.php'; // Navbar untuk publik ?>
    <div class="container mt-5">
        <h1 class="text-center mb-5 fw-bold">Blog Akun Cerdas</h1>
        <div class="row">
            <?php while($article = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= htmlspecialchars($article['thumbnail_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                        <p class="card-text text-muted">
                            <small>Oleh <?= htmlspecialchars($article['author_name']) ?> - <?= date('d M Y', strtotime($article['created_at'])) ?></small>
                        </p>
                        <a href="article.php?slug=<?= $article['slug'] ?>" class="btn btn-primary">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>