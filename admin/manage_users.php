<?php
require '../config.php';
require 'admin_gatekeeper.php';
$page_title = "Manajemen Pengguna";

// Logika untuk mengubah role jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $user_id_to_update = (int)$_POST['user_id'];
    $new_role = $_POST['role'];

    // Keamanan: Admin tidak bisa mengubah role-nya sendiri
    if ($user_id_to_update != $_SESSION['user_id']) {
        if ($new_role === 'admin' || $new_role === 'user') {
            $stmt = mysqli_prepare($conn, "UPDATE users SET role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_role, $user_id_to_update);
            mysqli_stmt_execute($stmt);
        }
    }
}

// Query diperbarui untuk mengambil data langganan aktif pengguna
$query = "
    SELECT 
        u.id, u.nama, u.email, u.role, u.created_at, 
        sp.name as plan_name, 
        us.end_date 
    FROM 
        users u
    LEFT JOIN 
        user_subscriptions us ON u.id = us.user_id AND us.status = 'active' AND us.end_date >= CURDATE()
    LEFT JOIN 
        subscription_plans sp ON us.plan_id = sp.id
    ORDER BY 
        u.created_at DESC";
$result = mysqli_query($conn, $query);
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
            <h1 class="fw-bold">Manajemen Pengguna</h1>
            <p class="text-muted">Lihat, ubah peran, dan kelola langganan pengguna dari sistem.</p>
            <table class="table table-striped mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Peran (Role)</th>
                        <th>Langganan</th>
                        <th>Bergabung Sejak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['nama']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <form method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role" class="form-select form-select-sm" <?= ($user['id'] == $_SESSION['user_id']) ? 'disabled' : '' ?>>
                                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" name="change_role" class="btn btn-primary btn-sm ms-2" <?= ($user['id'] == $_SESSION['user_id']) ? 'disabled' : '' ?>>Simpan</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($user['plan_name']): ?>
                                <span class="badge bg-success"><?= htmlspecialchars($user['plan_name']) ?></span>
                                <small class="d-block text-muted">s/d <?= date('d M Y', strtotime($user['end_date'])) ?></small>
                            <?php else: ?>
                                <span class="badge bg-secondary">Basic / Free</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <a href="manage_subscription.php?user_id=<?= $user['id'] ?>" class="btn btn-info btn-sm" title="Kelola Langganan"><i class="fas fa-gem"></i></a>
                            
                            <a href="edit_mentor_profile.php?user_id=<?= $user['id'] ?>" class="btn btn-secondary btn-sm" title="Profil Mentor"><i class="fas fa-user-tie"></i></a>

                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="#" onclick="confirmDelete(<?= $user['id'] ?>)" class="btn btn-danger btn-sm" title="Hapus Pengguna"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<script>
function confirmDelete(id) {
    if (confirm("PERINGATAN! Anda yakin ingin menghapus pengguna ini? Semua progres belajar, hasil kuis, dan postingan forum milik pengguna ini akan HILANG PERMANEN.")) {
        window.location.href = 'delete.php?type=user&id=' + id;
    }
}
</script>
</body>
</html>