<?php
// detail.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

// 1. Ambil parameter slug dari URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header("Location: proyek.php");
    exit();
}

try {
    // 2. Ambil data mahasiswa berdasarkan slug
    $stmt = $pdo->prepare("SELECT * FROM users WHERE slug = :slug");
    $stmt->execute([':slug' => $slug]);
    $mhs = $stmt->fetch();

    // Jika data tidak ditemukan, arahkan ke halaman 404 sederhana
    if (!$mhs) {
        echo "<h2>Halaman tidak ditemukan.</h2><a href='proyek.php'>Kembali ke Halaman Proyek</a>";
        exit();
    }

    // 3. Pencegahan Spamming Views (Kendala 1)
    // Cek apakah user/pengunjung sudah melihat halaman dengan slug ini di sesi saat ini
    $session_key = 'viewed_' . $slug;

    if (!isset($_SESSION[$session_key])) {
        // Jika belum pernah melihat dalam sesi ini, tambahkan views_count +1 di database
        $update_views = $pdo->prepare("UPDATE users SET views_count = views_count + 1 WHERE id = :id");
        $update_views->execute([':id' => $mhs['id']]);
        
        // Simpan status bahwa slug ini sudah dikunjungi pada sesi ini
        $_SESSION[$session_key] = true;

        // Perbarui nilai lokal variabel agar angka view langsung ter-update di layar
        $mhs['views_count'] += 1;
    }

} catch (PDOException $e) {
    die("Terjadi kesalahan sistem: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Website - <?= htmlspecialchars($mhs['full_name']); ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa; color: #333; }
        .navbar { display: flex; justify-content: space-between; padding: 20px 5%; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .navbar a { text-decoration: none; color: #333; margin-left: 20px; font-weight: 500; }
        .container { max-width: 900px; margin: 50px auto; padding: 0 20px; }
        .back-link { display: inline-block; margin-bottom: 25px; text-decoration: none; color: #007bff; font-weight: 500; }
        .detail-card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
        .profile-section { display: flex; align-items: center; gap: 20px; margin-bottom: 35px; }
        .profile-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; background: #ddd; }
        .profile-info h1 { margin: 0 0 5px; font-size: 26px; }
        .profile-info p { margin: 0; color: #666; font-size: 15px; }
        .views-badge { background: #e9ecef; color: #495057; font-size: 13px; padding: 5px 12px; border-radius: 15px; display: inline-block; margin-top: 5px; }
        .thumb-img { width: 100%; height: auto; max-height: 450px; object-fit: cover; border-radius: 8px; margin-bottom: 30px; background: #eee; }
        .project-content h2 { font-size: 24px; margin-bottom: 15px; }
        .project-desc { font-size: 16px; line-height: 1.6; color: #444; margin-bottom: 35px; white-space: pre-line; }
        .action-area { display: flex; gap: 15px; }
        .btn { display: inline-block; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold; text-align: center; }
        .btn-visit { background: #007bff; color: white; }
        .btn-visit:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo"><strong>Website Kelas</strong></div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="proyek.php">Proyek</a>
        <a href="tentang_kami.php">Tentang Kami</a>
    </div>
</div>

<div class="container">
    <a href="proyek.php" class="back-link">&larr; Kembali ke Proyek</a>

    <div class="detail-card">
        <div class="profile-section">
            <img src="<?= !empty($mhs['profile_photo']) ? $mhs['profile_photo'] : 'https://via.placeholder.com/150?text=User'; ?>" class="profile-img" alt="Foto Profil">
            <div class="profile-info">
                <h1><?= htmlspecialchars($mhs['full_name']); ?></h1>
                <p>NIM: <?= htmlspecialchars($mhs['nim']); ?></p>
                <p style="font-style: italic; margin-top: 4px; color: #555;"><?= htmlspecialchars($mhs['bio'] ?? 'Mahasiswa Informatika'); ?></p>
                <span class="views-badge">Dilihat <?= $mhs['views_count']; ?> kali</span>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 35px;">

        <div class="project-content">
            <img src="<?= !empty($mhs['thumbnail']) ? $mhs['thumbnail'] : 'https://via.placeholder.com/800x450?text=No+Thumbnail'; ?>" class="thumb-img" alt="Thumbnail Website">
            
            <h2><?= htmlspecialchars($mhs['website_title'] ?? 'Belum ada judul website'); ?></h2>
            <p class="project-desc">
                <?= htmlspecialchars($mhs['description'] ?? 'Belum ada deskripsi lengkap untuk website ini.'); ?>
            </p>

            <div class="action-area">
                <?php if (!empty($mhs['website_link'])): ?>
                    <a href="<?= htmlspecialchars($mhs['website_link']); ?>" target="_blank" class="btn btn-visit">Kunjungi Website</a>
                <?php else: ?>
                    <button class="btn" style="background: #ccc; color: #666; cursor: not-allowed;" disabled>Link Belum Tersedia</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>