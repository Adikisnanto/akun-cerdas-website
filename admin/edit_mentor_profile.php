<?php
require '../config.php';
require 'admin_gatekeeper.php';
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if (!$user_id) { header("Location: manage_users.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_mentor = isset($_POST['is_mentor']) ? 1 : 0;
    $headline = $_POST['mentor_headline'];
    $bio = $_POST['mentor_bio'];
    $stmt = mysqli_prepare($conn, "UPDATE users SET is_mentor = ?, mentor_headline = ?, mentor_bio = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "issi", $is_mentor, $headline, $bio, $user_id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_users.php");
    exit();
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama, email, is_mentor, mentor_headline, mentor_bio FROM users WHERE id = $user_id"));
$page_title = "Edit Profil Mentor: " . $user['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head><?php include '../partials/head.php'; ?></head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'admin_sidebar.php'; ?>
        <div class="main-content flex-grow-1 p-4">
            <h1 class="fw-bold"><?= $page_title ?></h1>
            <form method="POST">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_mentor" id="is_mentor" value="1" <?= $user['is_mentor'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_mentor">Jadikan sebagai Mentor</label>
                </div>
                <div class="mb-3">
                    <label>Headline Mentor (contoh: Senior Accountant at PwC)</label>
                    <input type="text" name="mentor_headline" class="form-control" value="<?= htmlspecialchars($user['mentor_headline']) ?>">
                </div>
                <div class="mb-3">
                    <label>Bio Singkat Mentor</label>
                    <textarea name="mentor_bio" class="form-control" rows="5"><?= htmlspecialchars($user['mentor_bio']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Simpan Profil Mentor</button>
            </form>
        </div>
    </div>
</body>
</html>