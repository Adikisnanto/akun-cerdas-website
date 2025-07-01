<?php
require '../config.php';
require 'admin_gatekeeper.php';
$page_title = "Manajemen Blog";

$query = "SELECT a.id, a.title, a.status, a.created_at, u.nama as author_name FROM articles a JOIN users u ON a.author_id = u.id ORDER BY a.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include '../partials/head.php'; ?>
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'admin_sidebar.php'; ?>
        <div class="main-content flex-grow-1 p-4">
            <div class="d-flex justify-content-between">
                <h1 class="fw-bold">Manajemen Blog</h1>
                <a href="edit_article.php" class="btn btn-primary my-auto">Tulis Artikel Baru</a>
            </div>
            <table class="table table-striped mt-4">
                <thead class="table-dark">
                    <tr><th>Judul</th><th>Penulis</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while($article = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($article['title']) ?></td>
                        <td><?= htmlspecialchars($article['author_name']) ?></td>
                        <td><span class="badge <?= $article['status'] == 'published' ? 'bg-success' : 'bg-secondary' ?>"><?= $article['status'] ?></span></td>
                        <td><?= date('d M Y', strtotime($article['created_at'])) ?></td>
                        <td>
                            <a href="edit_article.php?id=<?= $article['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>