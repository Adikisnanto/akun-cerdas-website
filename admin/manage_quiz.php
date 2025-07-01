<?php
require '../config.php';
require 'admin_gatekeeper.php';

$lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;
if (!$lesson_id) { header("Location: manage_modules.php"); exit(); }

// Cek apakah kuis sudah ada
$quiz_stmt = mysqli_prepare($conn, "SELECT q.id, q.title, l.title as lesson_title, l.module_id FROM quizzes q JOIN lessons l ON q.lesson_id = l.id WHERE q.lesson_id = ?");
mysqli_stmt_bind_param($quiz_stmt, "i", $lesson_id);
mysqli_stmt_execute($quiz_stmt);
$quiz = mysqli_fetch_assoc(mysqli_stmt_get_result($quiz_stmt));
$module_id = $quiz ? $quiz['module_id'] : 0; // Untuk tombol kembali

// Jika kuis belum ada & form disubmit
if (!$quiz && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_title = $_POST['title'];
    $stmt = mysqli_prepare($conn, "INSERT INTO quizzes (lesson_id, title) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $lesson_id, $quiz_title);
    mysqli_stmt_execute($stmt);
    header("Location: manage_quiz.php?lesson_id=" . $lesson_id);
    exit();
}

// Ambil pertanyaan jika kuis sudah ada
$questions = [];
if ($quiz) {
    $q_stmt = mysqli_prepare($conn, "SELECT id, question_text FROM questions WHERE quiz_id = ?");
    mysqli_stmt_bind_param($q_stmt, "i", $quiz['id']);
    mysqli_stmt_execute($q_stmt);
    $result = mysqli_stmt_get_result($q_stmt);
    while($row = mysqli_fetch_assoc($result)){
        $questions[] = $row;
    }
}

$page_title = "Manajemen Kuis";
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
            <a href="manage_lessons.php?module_id=<?= $module_id ?>" class="btn btn-secondary btn-sm mb-3"><i class="fas fa-arrow-left"></i> Kembali ke Materi</a>
            <h1 class="fw-bold">Manajemen Kuis</h1>
            
            <?php if ($quiz): ?>
                <h4 class="text-muted">Untuk Materi: <?= htmlspecialchars($quiz['lesson_title']) ?></h4>
                <div class="d-flex justify-content-between mt-4">
                    <h5>Daftar Pertanyaan</h5>
                    <a href="edit_question.php?quiz_id=<?= $quiz['id'] ?>" class="btn btn-primary">Tambah Pertanyaan</a>
                </div>
                <table class="table table-striped mt-2">
                    <thead><tr><th>ID</th><th>Teks Pertanyaan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach($questions as $q): ?>
                        <tr>
                            <td><?= $q['id'] ?></td>
                            <td><?= htmlspecialchars(substr($q['question_text'], 0, 100)) ?>...</td>
                            <td>
                                <a href="edit_question.php?id=<?= $q['id'] ?>&quiz_id=<?= $quiz['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="#" onclick="confirmDelete(<?= $q['id'] ?>, <?= $lesson_id ?>)" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($questions)): ?>
                            <tr><td colspan="3" class="text-center">Belum ada pertanyaan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Buat Kuis Baru</h5>
                        <p>Materi ini belum memiliki kuis. Silakan buat satu.</p>
                        <form method="POST">
                            <div class="mb-3">
                                <label>Judul Kuis</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success">Buat Kuis</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<script>
function confirmDelete(id, lessonId) {
    if (confirm("Anda yakin ingin menghapus pertanyaan ini?")) {
        window.location.href = 'delete.php?type=question&id=' + id + '&lesson_id=' + lessonId;
    }
}
</script>
</body>
</html>