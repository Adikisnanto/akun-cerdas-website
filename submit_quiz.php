<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $quiz_id = (int)$_POST['quiz_id'];
    $answers = $_POST['answers']; // Ini adalah array [question_id => option_id]

    $total_questions = count($answers);
    $correct_answers = 0;

    // Ambil semua jawaban yang benar untuk kuis ini dalam satu query
    $correct_options_stmt = mysqli_prepare($conn, 
        "SELECT o.question_id, o.id FROM options o JOIN questions q ON o.question_id = q.id WHERE q.quiz_id = ? AND o.is_correct = 1");
    mysqli_stmt_bind_param($correct_options_stmt, "i", $quiz_id);
    mysqli_stmt_execute($correct_options_stmt);
    $result = mysqli_stmt_get_result($correct_options_stmt);
    
    $correct_answers_map = [];
    while($row = mysqli_fetch_assoc($result)){
        $correct_answers_map[$row['question_id']] = $row['id'];
    }

    // Bandingkan jawaban user dengan jawaban yang benar
    foreach ($answers as $question_id => $user_option_id) {
        if (isset($correct_answers_map[$question_id]) && $correct_answers_map[$question_id] == $user_option_id) {
            $correct_answers++;
        }
    }

    $score = ($correct_answers / $total_questions) * 100;

    // Simpan hasil attempt ke database
    $insert_attempt_stmt = mysqli_prepare($conn, "INSERT INTO quiz_attempts (user_id, quiz_id, score) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insert_attempt_stmt, "iid", $user_id, $quiz_id, $score);
    mysqli_stmt_execute($insert_attempt_stmt);
    $attempt_id = mysqli_insert_id($conn);

    // Redirect ke halaman hasil
    header("Location: quiz_result.php?attempt_id=" . $attempt_id);
    exit();
}
?>