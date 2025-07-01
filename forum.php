<?php
require 'config.php';

// --- GATEKEEPER UNTUK LANGGANAN ---
if (!isset($_SESSION['user_id'])) {
    // Jika belum login sama sekali, arahkan ke halaman login
    header("Location: login.php");
    exit();
} else {
    // Jika sudah login, cek rolenya atau status langganannya
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['role'];
    $today = date("Y-m-d");
    
    // Admin selalu punya akses
    if ($user_role !== 'admin') {
        // Jika bukan admin, cek langganan aktif
        $query = "SELECT id FROM user_subscriptions WHERE user_id = ? AND status = 'active' AND end_date >= ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $today);
        mysqli_stmt_execute($stmt);
        $subscription_check = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($subscription_check) == 0) {
            // Jika tidak punya langganan aktif & bukan admin, tendang ke halaman harga
            // ?error=... adalah parameter agar kita bisa menampilkan pesan di halaman pricing nanti
            header("Location: pricing.php?error=subscription_required");
            exit();
        }
    }
}
// --- AKHIR GATEKEEPER ---

$page_title = "Forum Diskusi - Akun Cerdas";

// Query untuk mengambil semua thread, nama pembuat, dan jumlah balasan
$threads_query = "
    SELECT 
        ft.id, 
        ft.title, 
        ft.created_at, 
        u.nama as author_name,
        (SELECT COUNT(id) FROM forum_posts WHERE thread_id = ft.id) as reply_count
    FROM 
        forum_threads ft
    JOIN 
        users u ON ft.user_id = u.id
    ORDER BY 
        ft.created_at DESC
";
$threads_result = mysqli_query($conn, $threads_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
</head>
<body class="bg-light">
    <?php include 'partials/navbar.php'; // Navbar untuk pengguna yang sudah login ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">Forum Diskusi</h1>
            <a href="new_thread.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Buat Topik Baru</a>
        </div>

        <div class="card shadow-sm">
            <ul class="list-group list-group-flush">
                <?php if(mysqli_num_rows($threads_result) > 0): ?>
                    <?php while($thread = mysqli_fetch_assoc($threads_result)): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h5 class="mb-1"><a href="thread.php?id=<?= $thread['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($thread['title']) ?></a></h5>
                                <small class="text-muted">
                                    Dimulai oleh <?= htmlspecialchars($thread['author_name']) ?> pada <?= date('d M Y, H:i', strtotime($thread['created_at'])) ?>
                                </small>
                            </div>
                            <span class="badge bg-secondary rounded-pill"><?= $thread['reply_count'] ?> balasan</span>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="list-group-item p-3 text-center">Belum ada topik diskusi. Jadilah yang pertama!</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>