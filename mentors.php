<?php
require 'config.php';
$page_title = "Temukan Mentor Anda";
// Ambil data bio juga dari database
$mentors = mysqli_query($conn, "SELECT id, nama, mentor_headline, mentor_bio FROM users WHERE is_mentor = 1");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include 'partials/head.php'; ?>
    <style>
        .mentor-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .mentor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
        }
        .social-icons a {
            color: #6c757d;
            font-size: 1.2rem;
            margin: 0 8px;
        }
        .social-icons a:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <?php include 'partials/main_navbar.php'; ?>
    <div class="container mt-5 py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Temukan Mentor Profesional Anda</h1>
            <p class="text-muted fs-5">Belajar langsung dari para ahli di bidangnya untuk mempercepat karir Anda.</p>
        </div>
        <div class="row">
            <?php while($mentor = mysqli_fetch_assoc($mentors)): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card text-center shadow-sm h-100 mentor-card">
                    <div class="card-body d-flex flex-column">
                        <img src="https://i.pravatar.cc/150?u=<?= $mentor['id'] ?>" class="rounded-circle mb-3 mx-auto" alt="Foto Mentor" style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($mentor['nama']) ?></h5>
                        <p class="text-primary mb-2"><?= htmlspecialchars($mentor['mentor_headline']) ?></p>
                        <p class="card-text text-muted small flex-grow-1">
                            <?= htmlspecialchars(substr($mentor['mentor_bio'], 0, 100)) ?>...
                        </p>
                        <div class="social-icons my-3">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fas fa-globe"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                        <a href="mentor_profile.php?id=<?= $mentor['id'] ?>" class="btn btn-outline-primary mt-auto">Lihat Profil & Jadwal</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>