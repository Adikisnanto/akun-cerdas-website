<?php
require '../config.php';
require 'admin_gatekeeper.php';
$page_title = "Manajemen Modul";

$result = mysqli_query($conn, "SELECT * FROM modules ORDER BY id");
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
            <div class="d-flex justify-content-between">
                <h1 class="fw-bold">Manajemen Modul</h1>
                <a href="edit_module.php" class="btn btn-primary my-auto">Tambah Modul Baru</a>
            </div>
            <table class="table table-striped mt-4">
                <thead>
                    <tr><th>ID</th><th>Thumbnail</th><th>Judul</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><img src="<?= htmlspecialchars($row['thumbnail_url']) ?>" alt="thumb" width="100"></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td>
                            <a href="manage_lessons.php?module_id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Lihat Materi</a>
                            <a href="edit_module.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="#" onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<script>
function confirmDelete(id) {
    if (confirm("Anda yakin ingin menghapus modul ini? Semua materi dan kuis di dalamnya akan ikut terhapus!")) {
        window.location.href = 'delete.php?type=module&id=' + id;
    }
}
</script>
</body>
</html>