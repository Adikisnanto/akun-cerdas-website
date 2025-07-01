<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php?redirect=checkout.php?plan_id=" . $_GET['plan_id']); exit(); }

$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;
$plan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM subscription_plans WHERE id = $plan_id"));

if (!$plan) { die("Paket tidak valid."); }

// Logika untuk menangani upload bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $plan['price_monthly']; // Asumsi langganan bulanan
    
    // Proses upload file
    $target_dir = "uploads/proofs/";
    $filename = time() . '_' . basename($_FILES["proof"]["name"]);
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES["proof"]["tmp_name"], $target_file)) {
        // Simpan konfirmasi ke database
        $stmt = mysqli_prepare($conn, "INSERT INTO payment_confirmations (user_id, plan_id, amount, proof_of_payment_url, status) VALUES (?, ?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, "iiis", $user_id, $plan_id, $amount, $target_file);
        mysqli_stmt_execute($stmt);
        $success = "Konfirmasi pembayaran berhasil diunggah! Mohon tunggu persetujuan dari admin.";
    } else {
        $error = "Maaf, terjadi kesalahan saat mengunggah file Anda.";
    }
}

$page_title = "Checkout - " . $plan['name'];
?>
<!DOCTYPE html>
<html lang="id">
<head><?php include 'partials/head.php'; ?></head>
<body>
    <?php include 'partials/navbar.php'; ?>
    <div class="container mt-5 py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="fw-bold">Konfirmasi Pembayaran</h2>
                <p>Anda memilih paket: <strong class="text-primary"><?= htmlspecialchars($plan['name']) ?></strong></p>
                <div class="alert alert-info">
                    <h5 class="alert-heading">Instruksi Pembayaran</h5>
                    <p>Silakan lakukan transfer sejumlah **Rp <?= number_format($plan['price_monthly']) ?>** ke rekening berikut:</p>
                    <ul>
                        <li>Bank: BCA</li>
                        <li>No. Rekening: 123-456-7890</li>
                        <li>Atas Nama: PT Akun Cerdas Indonesia</li>
                    </ul>
                    <hr>
                    <p class="mb-0">Setelah melakukan transfer, mohon unggah bukti pembayaran Anda pada form di bawah ini.</p>
                </div>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php else: ?>
                    <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="proof" class="form-label">Unggah Bukti Pembayaran (JPG, PNG)</label>
                            <input class="form-control" type="file" id="proof" name="proof" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Saya Sudah Bayar, Konfirmasi Sekarang</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>