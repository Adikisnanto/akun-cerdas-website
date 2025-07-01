<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$attempt_id = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;

// Ambil hasil attempt dari database
$stmt = mysqli_prepare($conn, 
    "SELECT qa.score, q.id as quiz_id, q.title, l.id as lesson_id, m.id as module_id 
     FROM quiz_attempts qa 
     JOIN quizzes q ON qa.quiz_id = q.id
     JOIN lessons l ON q.lesson_id = l.id
     JOIN modules m ON l.module_id = m.id
     WHERE qa.id = ? AND qa.user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $attempt_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$attempt = mysqli_fetch_assoc($result);

if (!$attempt) { die("Hasil kuis tidak ditemukan atau Anda tidak memiliki akses."); }

// Definisikan judul halaman
$page_title = "Hasil Kuis - Akun Cerdas";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; // Sekarang file ini ada ?>
</head>
<body class="bg-light">
    <?php include 'partials/navbar.php'; ?>
    <div class="container mt-5 text-center">
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body p-5">
                <h2 class="fw-bold">Hasil Kuis: <?= htmlspecialchars($attempt['title']) ?></h2>
                <hr>
                <p class="fs-4">Skor Anda:</p>
                <h1 class="display-1 fw-bolder <?= $attempt['score'] >= 75 ? 'text-success' : 'text-danger' ?>">
                    <?= round($attempt['score']) ?>
                </h1>
                
                <?php if ($attempt['score'] >= 75): ?>
                    <p class="fs-5 text-success">Luar biasa! Kamu berhasil menguasai materi ini.</p>
                <?php else: ?>
                    <p class="fs-5 text-danger">Jangan menyerah! Coba pelajari lagi materinya dan ikuti kuis kembali.</p>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="quiz.php?quiz_id=<?= $attempt['quiz_id'] ?>" class="btn btn-secondary">Ulangi Kuis</a>
                    <a href="learn.php?module_id=<?= $attempt['module_id'] ?>&lesson_id=<?= $attempt['lesson_id'] ?>" class="btn btn-primary">Kembali ke Materi</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>