<?php
require '../config.php';
require 'admin_gatekeeper.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = ['id' => 0, 'title' => '', 'content' => '', 'thumbnail_url' => '', 'status' => 'draft'];
$page_title = "Tulis Artikel Baru";

if ($id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM articles WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $article = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $page_title = "Edit Artikel";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p_id = (int)$_POST['id'];
    $p_title = $_POST['title'];
    $p_content = $_POST['content'];
    $p_thumbnail_url = $_POST['thumbnail_url'];
    $p_status = $_POST['status'];
    $p_author_id = $_SESSION['user_id'];
    
    // Buat slug dari judul
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $p_title)));

    if ($p_id > 0) { // Update
        $stmt = mysqli_prepare($conn, "UPDATE articles SET title=?, slug=?, content=?, thumbnail_url=?, status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $p_title, $slug, $p_content, $p_thumbnail_url, $p_status, $p_id);
    } else { // Insert
        $stmt = mysqli_prepare($conn, "INSERT INTO articles (author_id, title, slug, content, thumbnail_url, status) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isssss", $p_author_id, $p_title, $slug, $p_content, $p_thumbnail_url, $p_status);
    }
    mysqli_stmt_execute($stmt);
    header("Location: manage_articles.php");
    exit();
}
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
            <h1 class="fw-bold"><?= $page_title ?></h1>
            <form method="POST" class="mt-4">
                <input type="hidden" name="id" value="<?= $article['id'] ?>">
                <div class="row">
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label>Judul Artikel</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($article['title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Konten</label>
                            <textarea name="content" class="form-control" rows="15" required><?= htmlspecialchars($article['content']) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="draft" <?= $article['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="published" <?= $article['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>URL Thumbnail</label>
                                    <input type="text" name="thumbnail_url" class="form-control" value="<?= htmlspecialchars($article['thumbnail_url']) ?>">
                                </div>
                                <button type="submit" class="btn btn-success w-100">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>