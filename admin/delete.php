<?php
require '../config.php';
require 'admin_gatekeeper.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($id > 0 && !empty($type)) {
    $table_name = '';
    $redirect_url = 'index.php'; // Default redirect

    if ($type === 'user' && $id == $_SESSION['user_id']) {
        die("Error: Anda tidak bisa menghapus akun Anda sendiri.");
    }

    switch ($type) {
        case 'module':
            $table_name = 'modules';
            $redirect_url = 'manage_modules.php';
            break;
        case 'lesson':
            $module_id = isset($_GET['module_id']) ? (int)$_GET['module_id'] : 0;
            $table_name = 'lessons';
            // Perbaikan di sini agar bisa kembali ke halaman materi yang benar
            $redirect_url = 'manage_lessons.php?module_id=' . $module_id; 
            break;
        case 'question':
            $lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;
            $table_name = 'questions';
            $redirect_url = 'manage_quiz.php?lesson_id=' . $lesson_id;
            break;
        case 'user':
            $table_name = 'users';
            $redirect_url = 'manage_users.php';
            break;
    }

    if (!empty($table_name)) {
        $stmt = mysqli_prepare($conn, "DELETE FROM $table_name WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: " . $redirect_url);
    exit();
}
?>