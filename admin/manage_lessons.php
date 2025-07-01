<?php
require '../config.php';
require 'admin_gatekeeper.php';

$module_id = isset($_GET['module_id']) ? (int)$_GET['module_id'] : 0;
if (!$module_id) {
    header("Location: manage_modules.php");
    exit();
}

// Ambil judul modul untuk ditampilkan
$module_stmt = mysqli_prepare($conn, "SELECT title FROM modules WHERE id = ?");
mysqli_stmt_bind_param($module_stmt, "i", $module_id);
mysqli_stmt_execute($module_stmt);
$module = mysqli_fetch_assoc(mysqli_stmt_get_result($module_stmt));

if (!$module) {
    die("Modul tidak ditemukan.");
}

$page_title = "Manajemen Materi untuk: " . htmlspecialchars($module['title']);

// Ambil semua materi DAN cek apakah ada kuis yang terhubung
$lessons_query = "
    SELECT l.*, q.id as quiz_id 
    FROM lessons l 
    LEFT JOIN quizzes q ON l.id = q.lesson_id 
    WHERE l.module_id = ? 
    ORDER BY l.lesson_order";
$stmt = mysqli_prepare($conn, $lessons_query);
mysqli_stmt_bind_param($stmt, "i", $module_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
            <a href="manage_modules.php" class="btn btn-secondary btn-sm mb-3"><i class="fas fa-arrow-left"></i> Kembali ke Modul</a>
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="fw-bold">Manajemen Materi</h1>
                <a href="edit_lesson.php?module_id=<?= $module_id ?>" class="btn btn-primary">Tambah Materi Baru</a>
            </div>
            <h4 class="text-muted">Modul: <?= htmlspecialchars($module['title']) ?></h4>

            <table class="table table-striped mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>Urutan</th>
                        <th>Judul Materi</th>
                        <th>Tipe Konten</th>
                        <th>Kuis</th> <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['lesson_order'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><span class="badge bg-info"><?= $row['content_type'] ?></span></td>
                        
                        <td>
                            <?php if($row['quiz_id']): ?>
                                <a href="manage_quiz.php?lesson_id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Kelola Kuis</a>
                            <?php else: ?>
                                <a href="manage_quiz.php?lesson_id=<?= $row['id'] ?>" class="btn btn-outline-success btn-sm">Buat Kuis</a>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <a href="edit_lesson.php?id=<?= $row['id'] ?>&module_id=<?= $module_id ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="#" onclick="confirmDelete(<?= $row['id'] ?>, <?= $module_id ?>)" class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<script>
function confirmDelete(id, moduleId) {
    if (confirm("Anda yakin ingin menghapus materi ini?")) {
        window.location.href = 'delete.php?type=lesson&id=' + id + '&module_id=' + moduleId;
    }
}
</script>
</body>
</html>