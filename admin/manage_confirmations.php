<?php
require '../config.php';
require 'admin_gatekeeper.php';
$page_title = "Konfirmasi Pembayaran";

// Logika untuk approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmation_id'])) {
    $confirmation_id = (int)$_POST['confirmation_id'];
    $new_status = $_POST['status'];
    $user_id = (int)$_POST['user_id'];
    $plan_id = (int)$_POST['plan_id'];
    
    if ($new_status === 'approved' || $new_status === 'rejected') {
        // Update status konfirmasi
        $stmt = mysqli_prepare($conn, "UPDATE payment_confirmations SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $confirmation_id);
        mysqli_stmt_execute($stmt);

        // Jika disetujui, aktifkan langganan user
        if ($new_status === 'approved') {
            $start_date = date("Y-m-d");
            $end_date = date('Y-m-d', strtotime("+1 month")); // Durasi 1 bulan
            
            // Hapus langganan aktif lama, lalu insert yang baru
            mysqli_query($conn, "DELETE FROM user_subscriptions WHERE user_id = $user_id");
            $sub_stmt = mysqli_prepare($conn, "INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'active')");
            mysqli_stmt_bind_param($sub_stmt, "iiss", $user_id, $plan_id, $start_date, $end_date);
            mysqli_stmt_execute($sub_stmt);
        }
        header("Location: manage_confirmations.php");
        exit();
    }
}

$query = "SELECT pc.*, u.nama as user_name, sp.name as plan_name FROM payment_confirmations pc JOIN users u ON pc.user_id = u.id JOIN subscription_plans sp ON pc.plan_id = sp.id ORDER BY pc.created_at DESC";
$confirmations = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head><?php include '../partials/head.php'; ?></head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'admin_sidebar.php'; ?>
        <div class="main-content flex-grow-1 p-4">
            <h1 class="fw-bold">Konfirmasi Pembayaran</h1>
            <table class="table table-striped mt-4">
                <thead class="table-dark">
                    <tr><th>User</th><th>Paket</th><th>Jumlah</th><th>Bukti</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($confirmations)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= htmlspecialchars($row['plan_name']) ?></td>
                        <td>Rp <?= number_format($row['amount']) ?></td>
                        <td><a href="../<?= $row['proof_of_payment_url'] ?>" target="_blank">Lihat Bukti</a></td>
                        <td><span class="badge bg-info"><?= $row['status'] ?></span></td>
                        <td>
                            <?php if($row['status'] == 'pending'): ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="confirmation_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                <input type="hidden" name="plan_id" value="<?= $row['plan_id'] ?>">
                                <button type="submit" name="status" value="approved" class="btn btn-success btn-sm">Approve</button>
                                <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>