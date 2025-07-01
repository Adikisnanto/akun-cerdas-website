<?php
require '../config.php';
require 'admin_gatekeeper.php';

$module_id = isset($_GET['module_id']) ? (int)$_GET['module_id'] : 0;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$lesson = ['id' => 0, 'module_id' => $module_id, 'title' => '', 'content_type' => 'text', 'content_data' => '', 'lesson_order' => ''];
$page_title = "Tambah Materi Baru";

if ($id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM lessons WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $lesson = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $page_title = "Edit Materi";
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p_id = (int)$_POST['id'];
    $p_module_id = (int)$_POST['module_id'];
    $p_title = $_POST['title'];
    $p_content_type = $_POST['content_type'];
    $p_lesson_order = (int)$_POST['lesson_order'];
    $p_content_data = $_POST['content_data_current']; // Ambil data lama sebagai default

    // Logika baru untuk memilih sumber data
    if ($p_content_type === 'text') {
        $p_content_data = $_POST['content_data_text'];
    } elseif ($p_content_type === 'youtube_link') {
        $p_content_data = $_POST['content_data_link'];
    } elseif (isset($_FILES['content_file']) && $_FILES['content_file']['error'] == 0) {
        // Hanya proses upload jika ada file baru yang diunggah
        $target_dir = "../uploads/lessons/";
        $filename = time() . '_' . basename($_FILES["content_file"]["name"]);
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["content_file"]["tmp_name"], $target_file)) {
            $p_content_data = "uploads/lessons/" . $filename;
        } else {
            die("Error saat mengupload file.");
        }
    }

    if ($p_id > 0) { // Update
        $stmt = mysqli_prepare($conn, "UPDATE lessons SET title=?, content_type=?, content_data=?, lesson_order=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssii", $p_title, $p_content_type, $p_content_data, $p_lesson_order, $p_id);
    } else { // Insert
        $stmt = mysqli_prepare($conn, "INSERT INTO lessons (module_id, title, content_type, content_data, lesson_order) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isssi", $p_module_id, $p_title, $p_content_type, $p_content_data, $p_lesson_order);
    }
    mysqli_stmt_execute($stmt);
    header("Location: manage_lessons.php?module_id=" . $p_module_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head><?php include '../partials/head.php'; ?></head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'admin_sidebar.php'; ?>
        <div class="main-content flex-grow-1 p-4">
            <a href="manage_lessons.php?module_id=<?= $module_id ?>" class="btn btn-secondary btn-sm mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
            <h1 class="fw-bold"><?= $page_title ?></h1>
            
            <form method="POST" class="mt-4" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $lesson['id'] ?>">
                <input type="hidden" name="module_id" value="<?= $lesson['module_id'] ?>">
                <input type="hidden" name="content_data_current" value="<?= htmlspecialchars($lesson['content_data']) ?>">
                
                <div class="mb-3">
                    <label>Judul Materi</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($lesson['title']) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Urutan Materi</label>
                        <input type="number" name="lesson_order" class="form-control" value="<?= htmlspecialchars($lesson['lesson_order']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Tipe Konten</label>
                        <select name="content_type" id="content_type_selector" class="form-select">
                            <option value="text" <?= $lesson['content_type'] == 'text' ? 'selected' : '' ?>>Teks</option>
                            <option value="local_image" <?= $lesson['content_type'] == 'local_image' ? 'selected' : '' ?>>Gambar (Upload)</option>
                            <option value="local_video" <?= $lesson['content_type'] == 'local_video' ? 'selected' : '' ?>>Video (Upload)</option>
                            <option value="youtube_link" <?= $lesson['content_type'] == 'youtube_link' ? 'selected' : '' ?>>Video (Link YouTube)</option>
                        </select>
                    </div>
                </div>

                <div id="content-input-text" class="content-input-wrapper mb-3">
                    <label>Konten Teks</label>
                    <textarea name="content_data_text" class="form-control" rows="10"><?= ($lesson['content_type'] == 'text') ? htmlspecialchars($lesson['content_data']) : '' ?></textarea>
                </div>

                <div id="content-input-file" class="content-input-wrapper mb-3">
                    <label>Upload File (untuk Video/Gambar)</label>
                    <input type="file" name="content_file" class="form-control">
                    <div class="form-text">Pilih file jika tipe konten adalah 'Video (Upload)' atau 'Gambar (Upload)'. Max: 50MB.</div>
                </div>

                <div id="content-input-link" class="content-input-wrapper mb-3">
                    <label>Link Embed YouTube</label>
                    <input type="text" name="content_data_link" class="form-control" value="<?= ($lesson['content_type'] == 'youtube_link') ? htmlspecialchars($lesson['content_data']) : '' ?>">
                    <div class="form-text">Contoh: `https://www.youtube.com/embed/VIDEO_ID8`</div>
                </div>
                <?php if (!empty($lesson['content_data']) && in_array($lesson['content_type'], ['local_video', 'local_image', 'youtube_link'])): ?>
                    <p class="mt-2">Konten saat ini: <code><?= htmlspecialchars($lesson['content_data']) ?></code></p>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-success mt-4">Simpan Materi</button>
            </form>
        </div>
    </div>

<script>
// JavaScript untuk menampilkan/menyembunyikan input yang relevan
document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('content_type_selector');
    const wrapperText = document.getElementById('content-input-text');
    const wrapperFile = document.getElementById('content-input-file');
    const wrapperLink = document.getElementById('content-input-link');

    function toggleInputs() {
        const selectedType = selector.value;
        
        // Sembunyikan semua wrapper dulu
        wrapperText.style.display = 'none';
        wrapperFile.style.display = 'none';
        wrapperLink.style.display = 'none';

        // Tampilkan yang relevan
        if (selectedType === 'text') {
            wrapperText.style.display = 'block';
        } else if (selectedType === 'local_video' || selectedType === 'local_image') {
            wrapperFile.style.display = 'block';
        } else if (selectedType === 'youtube_link') {
            wrapperLink.style.display = 'block';
        }
    }

    // Panggil saat halaman dimuat
    toggleInputs();

    // Panggil saat pilihan berubah
    selector.addEventListener('change', toggleInputs);
});
</script>
</body>
</html>