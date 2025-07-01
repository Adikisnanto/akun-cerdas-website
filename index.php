<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Cerdas - Platform Belajar Akuntansi Adaptif</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'partials/main_navbar.php'; ?>

    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start">
                    <h1 class="display-4 fw-bold">Platform Pembelajaran Akuntansi <span class="text-primary">Adaptif</span> untuk Masa Depanmu.</h1>
                    <p class="lead my-4">Belajar akuntansi jadi lebih mudah, personal, dan menyenangkan dengan kurikulum yang menyesuaikan kecepatan dan emosimu.</p>
                    <a href="register.php" class="btn btn-primary btn-lg me-2">Coba Gratis</a>
                    <a href="#fitur" class="btn btn-outline-secondary btn-lg">Lihat Fitur</a>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <video class="w-100 rounded shadow-lg" autoplay loop muted preload="metadata">
                        <source src="videos/Recording 2025-06-19 125012.mp4" type="video/mp4">
                        Browser Anda tidak mendukung tag video.
                    </video>
                </div>
            </div>
        </div>
    </header>

    <section id="fitur" class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Kenapa Memilih Akun Cerdas?</h2>
            <p class="text-muted mb-5">Kami bukan sekadar kursus online biasa.</p>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 p-4 border-0 shadow-sm">
                        <i class="fa-solid fa-user-graduate fa-3x text-primary mb-3"></i>
                        <h4 class="fw-semibold">Pembelajaran Personal</h4>
                        <p>Materi dan soal disesuaikan dengan kemampuan dan kecepatan belajarmu secara otomatis.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 p-4 border-0 shadow-sm">
                        <i class="fa-solid fa-face-smile fa-3x text-primary mb-3"></i>
                        <h4 class="fw-semibold">Emotion Feedback</h4>
                        <p>Sistem kami mendeteksi emosimu untuk memberikan tips dan jeda saat kamu merasa lelah atau bosan.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 p-4 border-0 shadow-sm">
                        <i class="fa-solid fa-briefcase fa-3x text-primary mb-3"></i>
                        <h4 class="fw-semibold">Studi Kasus Nyata</h4>
                        <p>Belajar langsung dari kasus-kasus industri nyata yang relevan dengan dunia kerja.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="testimoni" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Kata Mereka tentang Akun Cerdas</h2>
            <div class="row">
                 <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">"Awalnya saya benci akuntansi, tapi Akun Cerdas mengubah segalanya. Fitur adaptifnya keren banget!"</p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://i.pravatar.cc/50?img=1" class="rounded-circle me-3" alt="User Testimoni">
                                <div>
                                    <h6 class="mb-0 fw-bold">Budi Santoso</h6>
                                    <small class="text-muted">Mahasiswa Akuntansi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                     <div class="card">
                        <div class="card-body">
                            <p class="card-text">"Sebagai seorang profesional, saya butuh penyegaran materi. Custom learning path sangat membantu saya."</p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://i.pravatar.cc/50?img=5" class="rounded-circle me-3" alt="User Testimoni">
                                <div>
                                    <h6 class="mb-0 fw-bold">Citra Lestari</h6>
                                    <small class="text-muted">Junior Accountant</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="col-md-12 col-lg-4 mb-4">
                     <div class="card">
                        <div class="card-body">
                            <p class="card-text">"Portal lowongan magangnya the best! Saya dapat kesempatan magang virtual dari sini. Terima kasih Akun Cerdas!"</p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://i.pravatar.cc/50?img=3" class="rounded-circle me-3" alt="User Testimoni">
                                <div>
                                    <h6 class="mb-0 fw-bold">Rian Pratama</h6>
                                    <small class="text-muted">Alumni</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <footer class="py-4 bg-dark text-white">
        <div class="container text-center">
            <p>&copy; <?php echo date("Y"); ?> Akun Cerdas. Semua Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>