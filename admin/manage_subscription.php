<?php
require '../config.php';
require 'admin_gatekeeper.php';

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if (!$user_id) { header("Location: manage_users.php"); exit(); }

// Logika untuk assign langganan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_id = (int)$_POST['plan_id'];
    $duration_months = (int)$_POST['duration_months'];
    
    $start_date = date("Y-m-d");
    $end_date = date('Y-m-d', strtotime("+$duration_months months", strtotime($start_date)));

    // Hapus langganan aktif lama jika ada, lalu insert yang baru
    mysqli_query($conn, "DELETE FROM user_subscriptions WHERE user_id = $user_id AND status = 'active'");
    
    $stmt = mysqli_prepare($conn, "INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'active')");
    mysqli_stmt_bind_param($stmt, "iiss", $user_id, $plan_id, $start_date, $end_date);
    mysqli_stmt_execute($stmt);

    header("Location: manage_users.php");
    exit();
}

// Ambil data user & plans
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama, email FROM users WHERE id = $user_id"));
$plans = mysqli_query($conn, "SELECT * FROM subscription_plans WHERE is_active = 1");
$page_title = "Kelola Langganan: " . $user['nama'];
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
            <p><?= $user['email'] ?></p>
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Pilih Paket</label>
                            <select name="plan_id" class="form-select" required>
                                <?php while($plan = mysqli_fetch_assoc($plans)): ?>
                                <option value="<?= $plan['id'] ?>"><?= $plan['name'] ?> (Rp <?= number_format($plan['price_monthly']) ?>/bln)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durasi (Bulan)</label>
                            <input type="number" name="duration_months" class="form-control" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-success">Aktifkan Langganan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>