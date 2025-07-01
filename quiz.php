<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

// Ambil info kuis dan pertanyaannya
$quiz_stmt = mysqli_prepare($conn, "SELECT title FROM quizzes WHERE id = ?");
mysqli_stmt_bind_param($quiz_stmt, "i", $quiz_id);
mysqli_stmt_execute($quiz_stmt);
$quiz = mysqli_fetch_assoc(mysqli_stmt_get_result($quiz_stmt));

if (!$quiz) { die("Kuis tidak ditemukan."); }

// Ambil semua pertanyaan dan pilihan jawabannya
$questions_stmt = mysqli_prepare($conn, 
    "SELECT q.id as question_id, q.question_text, o.id as option_id, o.option_text 
     FROM questions q 
     JOIN options o ON q.id = o.question_id 
     WHERE q.quiz_id = ?");
mysqli_stmt_bind_param($questions_stmt, "i", $quiz_id);
mysqli_stmt_execute($questions_stmt);
$result = mysqli_stmt_get_result($questions_stmt);

$questions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $questions[$row['question_id']]['question_text'] = $row['question_text'];
    $questions[$row['question_id']]['options'][] = [
        'id' => $row['option_id'],
        'text' => $row['option_text']
    ];
}

// Definisikan judul halaman untuk digunakan di head.php
$page_title = htmlspecialchars($quiz['title']) . " - Akun Cerdas";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; // Sekarang file ini ada ?>
</head>
<body class="bg-light">
    <?php include 'partials/navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="fw-bold"><?= htmlspecialchars($quiz['title']) ?></h2>
        <p class="text-muted">Pilih jawaban yang menurut Anda paling benar.</p>
        <hr>
        <form action="submit_quiz.php" method="POST">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <?php $question_number = 1; foreach ($questions as $id => $q): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <p class="fw-bold">Pertanyaan <?= $question_number++ ?>:</p>
                        <p><?= htmlspecialchars($q['question_text']) ?></p>
                        
                        <?php foreach ($q['options'] as $option): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[<?= $id ?>]" id="option<?= $option['id'] ?>" value="<?= $option['id'] ?>" required>
                            <label class="form-check-label" for="option<?= $option['id'] ?>">
                                <?= htmlspecialchars($option['text']) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-success btn-lg">Selesai & Kirim Jawaban</button>
        </form>
    </div>
</body>
</html>