<?php
require 'config.php';
$mentor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $booking_datetime = $_POST['booking_datetime'];
    $topic = $_POST['topic'];
    $stmt = mysqli_prepare($conn, "INSERT INTO mentor_bookings (user_id, mentor_id, booking_datetime, topic) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiss", $user_id, $mentor_id, $booking_datetime, $topic);
    mysqli_stmt_execute($stmt);
    $success_message = "Permintaan booking Anda telah terkirim!";
}

$mentor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $mentor_id AND is_mentor = 1"));
if (!$mentor) { die("Mentor tidak ditemukan."); }

$page_title = "Profil Mentor: " . $mentor['nama'];

// Cek status langganan user yang sedang login
$is_premium = false;
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $premium_plan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM subscription_plans WHERE name = 'Premium'"));
    $premium_plan_id = $premium_plan['id'];

    $query = "SELECT id FROM user_subscriptions WHERE user_id = $user_id AND plan_id = $premium_plan_id AND status = 'active' AND end_date >= CURDATE()";
    $sub_check = mysqli_query($conn, $query);
    if(mysqli_num_rows($sub_check) > 0 || $_SESSION['role'] === 'admin') {
        $is_premium = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><?php include 'partials/head.php'; ?></head>
<body>
    <?php include 'partials/main_navbar.php'; ?>
    <div class="container mt-5 py-4">
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="https://i.pravatar.cc/200?u=<?= $mentor['id'] ?>" class="img-fluid rounded-circle mb-3" alt="Foto Mentor">
                <h2 class="fw-bold"><?= htmlspecialchars($mentor['nama']) ?></h2>
                <p class="text-primary fs-5"><?= htmlspecialchars($mentor['mentor_headline']) ?></p>
            </div>
            <div class="col-md-8">
                <h3>Tentang Mentor</h3>
                <p><?= nl2br(htmlspecialchars($mentor['mentor_bio'])) ?></p>
                <hr>
                <h3>Ajukan Jadwal Mentoring</h3>
                <?php if ($is_premium): ?>
                    <?php if(isset($success_message)): ?>
                        <div class="alert alert-success"><?= $success_message ?></div>
                    <?php else: ?>
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Pilih Tanggal & Waktu</label>
                                        <input type="datetime-local" name="booking_datetime" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Topik yang Ingin Dibahas</label>
                                        <textarea name="topic" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Kirim Permintaan</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Fitur booking 1-on-1 hanya tersedia untuk pelanggan paket <a href="pricing.php">Premium</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>