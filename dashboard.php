<?php
require 'config.php';

// "Gatekeeper" - Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['nama'];
$page_title = "Dashboard - Akun Cerdas";

// 1. Ambil Statistik Belajar
$progress_stmt = mysqli_prepare($conn, "SELECT COUNT(id) as completed_count FROM user_progress WHERE user_id = ?");
mysqli_stmt_bind_param($progress_stmt, "i", $user_id);
mysqli_stmt_execute($progress_stmt);
$completed_lessons = mysqli_fetch_assoc(mysqli_stmt_get_result($progress_stmt))['completed_count'];

$total_lessons_result = mysqli_query($conn, "SELECT COUNT(id) as total_count FROM lessons");
$total_lessons = mysqli_fetch_assoc($total_lessons_result)['total_count'];

$progress_percentage = ($total_lessons > 0) ? ($completed_lessons / $total_lessons) * 100 : 0;

// 2. Ambil Modul Terakhir yang Dipelajari
$last_module_stmt = mysqli_prepare($conn, 
    "SELECT m.id, m.title, m.description FROM modules m 
     JOIN lessons l ON m.id = l.module_id 
     JOIN user_progress up ON l.id = up.lesson_id 
     WHERE up.user_id = ? 
     ORDER BY up.completed_at DESC 
     LIMIT 1");
mysqli_stmt_bind_param($last_module_stmt, "i", $user_id);
mysqli_stmt_execute($last_module_stmt);
$last_module = mysqli_fetch_assoc(mysqli_stmt_get_result($last_module_stmt));

// 3. QUERY UNTUK BOOKING MENTOR DIHAPUS DARI SINI
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
</head>
<body class="bg-light">
    
    <?php include 'partials/navbar.php'; ?>
    
    <div class="container mt-5">
        <h2 class="fw-bold mb-1">Selamat Datang Kembali, <?= htmlspecialchars(explode(' ', $nama_user)[0]) ?>!</h2>
        <p class="text-muted">Siap untuk menaklukkan dunia akuntansi hari ini?</p>
        
        <div class="row mt-4 g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <?php if ($last_module): ?>
                            <h5 class="card-title fw-bold">Lanjutkan Belajar</h5>
                            <p class="text-muted">Kamu terakhir kali mempelajari modul:</p>
                            <div class="p-3 bg-light rounded">
                                <h6 class="fw-bold text-primary"><?= htmlspecialchars($last_module['title']) ?></h6>
                                <p class="mb-0 small"><?= htmlspecialchars($last_module['description']) ?></p>
                            </div>
                            <a href="learn.php?module_id=<?= $last_module['id'] ?>" class="btn btn-primary mt-3">Lanjutkan <i class="fas fa-arrow-right ms-1"></i></a>
                        <?php else: ?>
                            <h5 class="card-title fw-bold">Mulai Petualangan Belajarmu</h5>
                            <p class="text-muted">Sepertinya kamu baru di sini. Yuk, mulai dengan memilih modul pertamamu!</p>
                             <a href="modules.php" class="btn btn-primary mt-2">Lihat Semua Modul</a>
                        <?php endif; ?>
                    </div>
                </div>

                </div>

            <div class="col-lg-4">
                 <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold">Statistik Belajar Pribadi</h5>
                        <p class="mb-2">Materi Selesai: <strong><?= $completed_lessons ?> dari <?= $total_lessons ?></strong></p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progress_percentage ?>%;" aria-valuenow="<?= $progress_percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= round($progress_percentage) ?>%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                         <h6 class="fw-bold"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Tips Mingguan</h6>
                         <p class="small text-muted">Konsistensi adalah kunci. Coba luangkan waktu 15-30 menit setiap hari untuk hasil yang maksimal!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>