    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php">Akun Cerdas</a>
        <div class="d-flex align-items-center">
            <a class="nav-link me-3" href="modules.php">Semua Modul</a>
            <a class="nav-link me-3" href="forum.php">Forum Diskusi</a> <span class="navbar-text me-3">
                Halo, <?= htmlspecialchars($_SESSION['nama']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>