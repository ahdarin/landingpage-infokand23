<?php
// edit_profil.php
require_once 'koneksi.php';
require_once 'auth_check.php'; // Proteksi login

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// 1. Ambil data pengguna saat ini dari database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();

// 2. Jika tombol "Skip" ditekan, langsung alihkan
if (isset($_POST['skip'])) {
    header("Location: proyek.php");
    exit();
}

/**
 * Fungsi Kompresi & Konversi Gambar ke Format WebP
 */
function uploadAndCompressToWebP($file_source, $target_path, $quality = 75, $max_width = 1200) {
    // Ambil informasi dasar gambar
    $info = getimagesize($file_source);
    if (!$info) return false;

    // Buat resource gambar berdasarkan tipe file asli
    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($file_source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($file_source);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($file_source);
            break;
        default:
            return false; // Format file tidak didukung
    }

    $orig_width = imagesx($image);
    $orig_height = imagesy($image);

    // Hitung dimensi baru jika lebar gambar melebihi batas maksimal
    if ($orig_width > $max_width) {
        $new_width = $max_width;
        $new_height = floor($orig_height * ($max_width / $orig_width));

        // Buat kanvas kosong dengan ukuran baru
        $truecolor = imagecreatetruecolor($new_width, $new_height);

        // Pertahankan transparansi bila file asli adalah PNG
        imagealphablending($truecolor, false);
        imagesavealpha($truecolor, true);

        // Resize gambar asli ke kanvas baru
        imagecopyresampled($truecolor, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
        
        imagedestroy($image);
        $image = $truecolor;
    }

    // Simpan dalam format WebP
    $success = imagewebp($image, $target_path, $quality);
    imagedestroy($image);

    return $success;
}

// 3. Proses form submission saat tombol "Save" ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $bio = trim($_POST['bio']);
    $website_title = trim($_POST['website_title']);
    $website_link = trim($_POST['website_link']);
    $description = trim($_POST['description']);
    
    $profile_photo = $user['profile_photo'];
    $thumbnail = $user['thumbnail'];

    // Siapkan folder upload
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size = 2 * 1024 * 1024; // 2 MB

    // --- PROSES UPLOAD & KOMPRESI FOTO PROFIL ---
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_extensions) && $file['size'] <= $max_size) {
            $new_name = 'profile_' . $user_id . '_' . time() . '.webp';
            $target_path = $upload_dir . $new_name;

            // Maksimal lebar 400px untuk foto profil
            if (uploadAndCompressToWebP($file['tmp_name'], $target_path, 80, 400)) {
                // Hapus file foto lama jika ada
                if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])) {
                    unlink($user['profile_photo']);
                }
                $profile_photo = $target_path;
            } else {
                $error = "Gagal memproses foto profil.";
            }
        } else {
            $error = "Foto profil harus format JPG/PNG/WebP dan ukuran maks 2MB.";
        }
    }

    // --- PROSES UPLOAD & KOMPRESI THUMBNAIL ---
    if (empty($error) && isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['thumbnail'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_extensions) && $file['size'] <= $max_size) {
            $new_name = 'thumb_' . $user_id . '_' . time() . '.webp';
            $target_path = $upload_dir . $new_name;

            // Maksimal lebar 1200px untuk thumbnail project
            if (uploadAndCompressToWebP($file['tmp_name'], $target_path, 75, 1200)) {
                // Hapus file thumbnail lama jika ada
                if (!empty($user['thumbnail']) && file_exists($user['thumbnail'])) {
                    unlink($user['thumbnail']);
                }
                $thumbnail = $target_path;
            } else {
                $error = "Gagal memproses thumbnail.";
            }
        } else {
            $error = "Thumbnail harus format JPG/PNG/WebP dan ukuran maks 2MB.";
        }
    }

    // 4. Update data ke database jika tidak ada error
    if (empty($error)) {
        try {
            $update_stmt = $pdo->prepare("UPDATE users SET bio = :bio, website_title = :website_title, website_link = :website_link, description = :description, profile_photo = :profile_photo, thumbnail = :thumbnail WHERE id = :id");
            $update_stmt->execute([
                ':bio' => $bio,
                ':website_title' => $website_title,
                ':website_link' => $website_link,
                ':description' => $description,
                ':profile_photo' => $profile_photo,
                ':thumbnail' => $thumbnail,
                ':id' => $user_id
            ]);

            $success = "Data profil berhasil disimpan!";
            header("refresh:1; url=proyek.php");
        } catch (PDOException $e) {
            $error = "Gagal memperbarui database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil & Project</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="text"], input[type="url"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 80px; }
        .action-buttons { display: flex; justify-content: space-between; margin-top: 25px; }
        .btn { padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 16px; }
        .btn-save { background: #007bff; color: white; }
        .btn-skip { background: #6c757d; color: white; }
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Lengkapi Profil & Website Anda</h2>
    <?php if (!empty($error)): ?> <div class="error"><?= $error; ?></div> <?php endif; ?>
    <?php if (!empty($success)): ?> <div class="success"><?= $success; ?></div> <?php endif; ?>

    <form action="edit_profil.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="bio">Bio Singkat</label>
            <textarea id="bio" name="bio" placeholder="Tulis bio singkat Anda..."><?= htmlspecialchars($user['bio'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="profile_photo">Foto Profil (Maks 2MB, JPG/PNG)</label>
            <?php if (!empty($user['profile_photo'])): ?>
                <img src="<?= $user['profile_photo']; ?>" width="60" height="60" style="border-radius: 50%; margin-bottom: 5px; object-fit: cover;"><br>
            <?php endif; ?>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">

        <div class="form-group">
            <label for="website_title">Judul Website Proyek</label>
            <input type="text" id="website_title" name="website_title" value="<?= htmlspecialchars($user['website_title'] ?? ''); ?>" placeholder="Misal: Sistem Pakar Diagnosis Penyakit">
        </div>

        <div class="form-group">
            <label for="website_link">Link Website</label>
            <input type="url" id="website_link" name="website_link" value="<?= htmlspecialchars($user['website_link'] ?? ''); ?>" placeholder="https://domain-anda.com">
        </div>

        <div class="form-group">
            <label for="thumbnail">Thumbnail Website (Maks 2MB)</label>
            <?php if (!empty($user['thumbnail'])): ?>
                <img src="<?= $user['thumbnail']; ?>" width="100" style="margin-bottom: 5px;"><br>
            <?php endif; ?>
            <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
        </div>

        <div class="form-group">
            <label for="description">Deskripsi Lengkap Website</label>
            <textarea id="description" name="description" style="height: 120px;" placeholder="Tulis rincian atau fitur unggulan dari website Anda..."><?= htmlspecialchars($user['description'] ?? ''); ?></textarea>
        </div>

        <div class="action-buttons">
            <button type="submit" name="skip" class="btn btn-skip" formnovalidate>Skip</button>
            <button type="submit" name="save" class="btn btn-save">Save</button>
        </div>
    </form>
</div>

</body>
</html>