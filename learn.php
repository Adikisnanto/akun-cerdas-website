<?php
require 'config.php';

// Gatekeeper - Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data dari URL dan Session
$user_id = $_SESSION['user_id'];
$module_id = isset($_GET['module_id']) ? (int)$_GET['module_id'] : 0;
$current_lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

// Ambil info modul
$module_stmt = mysqli_prepare($conn, "SELECT title FROM modules WHERE id = ?");
mysqli_stmt_bind_param($module_stmt, "i", $module_id);
mysqli_stmt_execute($module_stmt);
$module = mysqli_fetch_assoc(mysqli_stmt_get_result($module_stmt));

if (!$module) {
    die("Modul tidak ditemukan.");
}

// Ambil semua materi untuk sidebar dan status progresnya
$lessons_stmt = mysqli_prepare($conn, "SELECT l.id, l.title, up.id as progress_id FROM lessons l LEFT JOIN user_progress up ON l.id = up.lesson_id AND up.user_id = ? WHERE l.module_id = ? ORDER BY l.lesson_order");
mysqli_stmt_bind_param($lessons_stmt, "ii", $user_id, $module_id);
mysqli_stmt_execute($lessons_stmt);
$lessons_result = mysqli_stmt_get_result($lessons_stmt);
$lessons = [];
while ($row = mysqli_fetch_assoc($lessons_result)) {
    $lessons[] = $row;
}

// Tentukan materi yang akan ditampilkan jika tidak ada di URL
if ($current_lesson_id == 0) {
    if (!empty($lessons)) {
        $current_lesson_id = $lessons[0]['id'];
    } else {
        die("Modul ini belum memiliki materi.");
    }
}

// Ambil konten materi yang sedang aktif
$content_stmt = mysqli_prepare($conn, "SELECT * FROM lessons WHERE id = ?");
mysqli_stmt_bind_param($content_stmt, "i", $current_lesson_id);
mysqli_stmt_execute($content_stmt);
$current_lesson = mysqli_fetch_assoc(mysqli_stmt_get_result($content_stmt));

if (!$current_lesson) {
    die("Materi tidak ditemukan.");
}

// Cek apakah materi ini sudah diselesaikan oleh user
$progress_check_stmt = mysqli_prepare($conn, "SELECT id FROM user_progress WHERE user_id = ? AND lesson_id = ?");
mysqli_stmt_bind_param($progress_check_stmt, "ii", $user_id, $current_lesson_id);
mysqli_stmt_execute($progress_check_stmt);
$is_completed = mysqli_fetch_assoc(mysqli_stmt_get_result($progress_check_stmt));

// Cek apakah materi ini punya kuis
$quiz_check_stmt = mysqli_prepare($conn, "SELECT id FROM quizzes WHERE lesson_id = ?");
mysqli_stmt_bind_param($quiz_check_stmt, "i", $current_lesson_id);
mysqli_stmt_execute($quiz_check_stmt);
$quiz = mysqli_fetch_assoc(mysqli_stmt_get_result($quiz_check_stmt));

$page_title = htmlspecialchars($current_lesson['title']) . " - Akun Cerdas";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
    <style>
        .sidebar { height: calc(100vh - 56px); position: fixed; top: 56px; left: 0; overflow-y: auto; z-index: 1000; }
        .content-area { margin-left: 280px; padding: 2rem; }
        .lesson-list-item.active { background-color: #e9ecef; font-weight: bold;}
        .lesson-video, .lesson-image { max-width: 100%; height: auto; border-radius: 0.5rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1); }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; background-color: #000; border-radius: 0.5rem; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
    </style>
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    <div class="d-flex">
        <div class="sidebar bg-light p-3" style="width: 280px;">
             <h5 class="mb-3 fw-bold"><?= htmlspecialchars($module['title']) ?></h5>
            <div class="list-group list-group-flush">
                <?php foreach ($lessons as $lesson): ?>
                    <a href="learn.php?module_id=<?= $module_id ?>&lesson_id=<?= $lesson['id'] ?>" class="list-group-item list-group-item-action lesson-list-item <?= $lesson['id'] == $current_lesson_id ? 'active' : '' ?>">
                        <?= htmlspecialchars($lesson['title']) ?>
                        <?php if($lesson['progress_id']): ?><i class="fas fa-check-circle text-success float-end mt-1"></i><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="content-area container-fluid">
            <h2 class="mb-4"><?= htmlspecialchars($current_lesson['title']) ?></h2>

            <?php if ($current_lesson['content_type'] == 'youtube_link'): ?>
                <div class="video-container shadow-sm">
                    <iframe src="<?= htmlspecialchars($current_lesson['content_data']) ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
            <?php elseif ($current_lesson['content_type'] == 'local_video'): ?>
                <video controls class="lesson-video">
                    <source src="<?= htmlspecialchars($current_lesson['content_data']) ?>" type="video/mp4">
                    Browser Anda tidak mendukung tag video.
                </video>
            <?php elseif ($current_lesson['content_type'] == 'local_image'): ?>
                <img src="<?= htmlspecialchars($current_lesson['content_data']) ?>" class="lesson-image" alt="Gambar Materi">
            <?php elseif ($current_lesson['content_type'] == 'text'): ?>
                <div class="p-4 bg-white rounded shadow-sm">
                    <?= nl2br(htmlspecialchars($current_lesson['content_data'])) ?>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <?php if ($is_completed): ?>
                    <button class="btn btn-success" disabled><i class="fas fa-check"></i> Sudah Selesai</button>
                <?php else: ?>
                    <button class="btn btn-primary" id="complete-btn" data-lesson-id="<?= $current_lesson_id ?>">
                        <i class="fas fa-check"></i> Tandai Selesai
                    </button>
                <?php endif; ?>
            </div>

            <?php if ($quiz): ?>
            <div class="mt-4 p-4 bg-warning bg-opacity-10 border border-warning rounded shadow-sm">
                <h5 class="fw-bold">Uji Pemahamanmu!</h5>
                <p>Materi ini memiliki kuis untuk mengukur sejauh mana pemahamanmu. Yuk, coba kerjakan!</p>
                <a href="quiz.php?quiz_id=<?= $quiz['id'] ?>" class="btn btn-warning fw-bold">Ikuti Kuis <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    const completeBtn = document.getElementById('complete-btn');
    // Pastikan tombolnya ada sebelum menambahkan event listener
    if (completeBtn) {
        completeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const lessonId = this.getAttribute('data-lesson-id');
            
            // Kirim data ke backend
            fetch('update_progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'lesson_id=' + lessonId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update tampilan tanpa perlu reload halaman
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-check-circle text-success float-end mt-1';
                    document.querySelector(`a[href*="lesson_id=${lessonId}"]`).appendChild(icon);
                    
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-check"></i> Sudah Selesai';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');
                    
                } else {
                    // Beri tahu user jika sudah selesai atau ada error lain
                    alert(data.message || 'Gagal menyimpan progres.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan.');
            });
        });
    }
    </script>
</body>
</html>