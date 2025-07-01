<?php
require '../config.php';
require 'admin_gatekeeper.php';

$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$question = ['id' => 0, 'question_text' => ''];
$options = [['id'=>0, 'option_text'=>''], ['id'=>0, 'option_text'=>''], ['id'=>0, 'option_text'=>''], ['id'=>0, 'option_text'=>'']];
$correct_option_id = 0;
$page_title = "Tambah Pertanyaan Baru";

// Jika ini mode EDIT, ambil data yang ada
if ($question_id) {
    // Ambil pertanyaan
    $stmt_q = mysqli_prepare($conn, "SELECT * FROM questions WHERE id = ?");
    mysqli_stmt_bind_param($stmt_q, "i", $question_id);
    mysqli_stmt_execute($stmt_q);
    $question = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_q));
    
    // Ambil pilihan jawaban
    $stmt_o = mysqli_prepare($conn, "SELECT * FROM options WHERE question_id = ?");
    mysqli_stmt_bind_param($stmt_o, "i", $question_id);
    mysqli_stmt_execute($stmt_o);
    $result_o = mysqli_stmt_get_result($stmt_o);
    $options = [];
    while($row = mysqli_fetch_assoc($result_o)){
        $options[] = $row;
        if($row['is_correct']) $correct_option_id = $row['id'];
    }
    $page_title = "Edit Pertanyaan";
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = $_POST['question_text'];
    $post_options = $_POST['options'];
    $is_correct_index = $_POST['is_correct'];
    $q_id = (int)$_POST['id'];
    $qz_id = (int)$_POST['quiz_id'];
    
    // Update atau Insert pertanyaan
    if ($q_id > 0) { // Update
        $stmt = mysqli_prepare($conn, "UPDATE questions SET question_text = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $question_text, $q_id);
        mysqli_stmt_execute($stmt);
        // Hapus opsi lama untuk diganti dengan yang baru
        $del_stmt = mysqli_prepare($conn, "DELETE FROM options WHERE question_id = ?");
        mysqli_stmt_bind_param($del_stmt, "i", $q_id);
        mysqli_stmt_execute($del_stmt);
    } else { // Insert
        $stmt = mysqli_prepare($conn, "INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "is", $qz_id, $question_text);
        mysqli_stmt_execute($stmt);
        $q_id = mysqli_insert_id($conn); // Dapatkan ID pertanyaan yang baru dibuat
    }

    // Insert pilihan jawaban yang baru
    $opt_stmt = mysqli_prepare($conn, "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
    foreach ($post_options as $index => $option_text) {
        if (!empty($option_text)) {
            $is_correct = ($index == $is_correct_index) ? 1 : 0;
            mysqli_stmt_bind_param($opt_stmt, "isi", $q_id, $option_text, $is_correct);
            mysqli_stmt_execute($opt_stmt);
        }
    }
    
    $quiz_info_stmt = mysqli_prepare($conn, "SELECT lesson_id FROM quizzes WHERE id = ?");
    mysqli_stmt_bind_param($quiz_info_stmt, "i", $qz_id);
    mysqli_stmt_execute($quiz_info_stmt);
    $lesson_id = mysqli_fetch_assoc(mysqli_stmt_get_result($quiz_info_stmt))['lesson_id'];

    header("Location: manage_quiz.php?lesson_id=" . $lesson_id);
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
            <form method="POST">
                <input type="hidden" name="id" value="<?= $question['id'] ?>">
                <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
                <div class="card mt-4">
                    <div class="card-header">Teks Pertanyaan</div>
                    <div class="card-body">
                        <textarea name="question_text" class="form-control" rows="4" required><?= htmlspecialchars($question['question_text']) ?></textarea>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header">Pilihan Jawaban (Isi yang diperlukan saja)</div>
                    <div class="card-body">
                        <p>Pilih salah satu sebagai jawaban yang benar:</p>
                        <?php for($i = 0; $i < 4; $i++): ?>
                        <div class="input-group mb-3">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="radio" name="is_correct" value="<?= $i ?>" <?= isset($options[$i]['id']) && $options[$i]['id'] == $correct_option_id ? 'checked' : '' ?> required>
                            </div>
                            <input type="text" name="options[]" class="form-control" value="<?= isset($options[$i]) ? htmlspecialchars($options[$i]['option_text']) : '' ?>">
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-success mt-4">Simpan Pertanyaan</button>
            </form>
        </div>
    </div>
</body>
</html>