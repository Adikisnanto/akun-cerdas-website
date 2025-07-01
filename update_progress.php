<?php
require 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lesson_id'])) {
    $user_id = $_SESSION['user_id'];
    $lesson_id = (int)$_POST['lesson_id'];

    // Cek apakah progres sudah ada
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM user_progress WHERE user_id = ? AND lesson_id = ?");
    mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $lesson_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        echo json_encode(['success' => false, 'message' => 'Already completed']);
    } else {
        // Masukkan progres baru
        $insert_stmt = mysqli_prepare($conn, "INSERT INTO user_progress (user_id, lesson_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $lesson_id);
        if (mysqli_stmt_execute($insert_stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>