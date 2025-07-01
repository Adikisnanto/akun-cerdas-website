<?php
// --- Baris Ajaib untuk Menampilkan Error ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -----------------------------------------

require '../config.php';
require 'admin_gatekeeper.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$module = ['id' => 0, 'title' => '', 'description' => '', 'thumbnail_url' => ''];
$page_title = "Tambah Modul Baru";

if ($id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM modules WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($result) {
        $module = mysqli_fetch_assoc($result);
        if($module) {
            $page_title = "Edit Modul: " . htmlspecialchars($module['title']);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $thumbnail_url = $_POST['thumbnail_url'];
    $module_id = (int)$_POST['id'];

    if ($module_id > 0) { // Update
        $stmt = mysqli_prepare($conn, "UPDATE modules SET title=?, description=?, thumbnail_url=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $thumbnail_url, $module_id);
    } else { // Insert
        $stmt = mysqli_prepare($conn, "INSERT INTO modules (title, description, thumbnail_url) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $title, $description, $thumbnail_url);
    }
    mysqli_stmt_execute($stmt);
    header("Location: manage_modules.php");
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
                <input type="hidden" name="id" value="<?= $module['id'] ?>">
                <div class="mb-3">
                    <label>Judul Modul</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($module['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($module['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label>URL Thumbnail</label>
                    <input type="text" name="thumbnail_url" class="form-control" value="<?= htmlspecialchars($module['thumbnail_url']) ?>" required>
                </div>
                <button type="submit" class="btn btn-success">Simpan Modul</button>
                <a href="manage_modules.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>