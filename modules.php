<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil semua modul dari database
$result = mysqli_query($conn, "SELECT * FROM modules ORDER BY id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Modul - Akun Cerdas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'partials/navbar.php'; // Kita akan buat navbar terpisah nanti ?>

    <div class="container mt-5">
        <h1 class="mb-4 fw-bold">Pilih Modul Belajar</h1>
        <div class="row">
            <?php while ($module = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= htmlspecialchars($module['thumbnail_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($module['title']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($module['title']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($module['description']) ?></p>
                        <a href="learn.php?module_id=<?= $module['id'] ?>" class="btn btn-primary mt-auto">Mulai Belajar</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($result) == 0): ?>
                <p>Belum ada modul yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>