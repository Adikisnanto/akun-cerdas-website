<?php
require '../config.php'; // Path-nya ../ karena kita berada dalam folder admin
require 'admin_gatekeeper.php';

$page_title = "Dashboard Admin";

// Ambil statistik
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as count FROM users"))['count'];
$total_modules = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as count FROM modules"))['count'];
$total_lessons = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as count FROM lessons"))['count'];

// (BARU) Ambil data konfirmasi pembayaran yang pending
$pending_confirmations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as count FROM payment_confirmations WHERE status = 'pending'"))['count'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include '../partials/head.php'; // Path-nya ../ ?>
    <link rel="stylesheet" href="../css/admin_style.css"> </head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'admin_sidebar.php'; // Sidebar navigasi admin ?>
        <div class="main-content flex-grow-1 p-4">
            <h1 class="fw-bold">Dashboard Admin</h1>
            <p class="text-muted">Selamat datang di panel manajemen Akun Cerdas.</p>
            <div class="row mt-4">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Konfirmasi Pending</h5>
                                    <p class="card-text fs-2 fw-bold"><?= $pending_confirmations ?></p>
                                </div>
                                <i class="fas fa-file-invoice-dollar fa-3x opacity-50"></i>
                            </div>
                            <a href="manage_confirmations.php" class="text-white stretched-link">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                             <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Pengguna</h5>
                                    <p class="card-text fs-2 fw-bold"><?= $total_users ?></p>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Modul</h5>
                                    <p class="card-text fs-2 fw-bold"><?= $total_modules ?></p>
                                </div>
                                <i class="fas fa-book fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                     <div class="card text-white bg-info">
                        <div class="card-body">
                             <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Materi</h5>
                                    <p class="card-text fs-2 fw-bold"><?= $total_lessons ?></p>
                                </div>
                                <i class="fas fa-list-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>