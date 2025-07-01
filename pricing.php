<?php
require 'config.php';
$page_title = "Harga - Akun Cerdas";
$plans = mysqli_query($conn, "SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY price_monthly ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
</head>
<body>
    <?php include 'partials/main_navbar.php'; ?>
    <div class="container mt-5 py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Pilih Paket yang Tepat Untuk Anda</h1>
            <p class="text-muted">Mulai perjalananmu menguasai akuntansi hari ini.</p>
        </div>
        <div class="row">
            <?php while($plan = mysqli_fetch_assoc($plans)): ?>
            <div class="col-lg-4">
                <div class="card mb-4 rounded-3 shadow-sm">
                    <div class="card-header py-3">
                        <h4 class="my-0 fw-normal"><?= htmlspecialchars($plan['name']) ?></h4>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title pricing-card-title">Rp <?= number_format($plan['price_monthly']) ?><small class="text-muted fw-light">/bln</small></h1>
                        <ul class="list-unstyled mt-3 mb-4">
                            <?php 
                                $features = explode(',', $plan['features']);
                                foreach($features as $feature) {
                                    echo "<li>" . htmlspecialchars($feature) . "</li>";
                                }
                            ?>
                        </ul>
                        <a href="checkout.php?plan_id=<?= $plan['id'] ?>" class="w-100 btn btn-lg btn-primary">Pilih Paket</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>