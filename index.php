<?php
// index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

try {
    // Ambil 6 proyek teratas berdasarkan views_count (Most Viewed)
    $stmt = $pdo->query("SELECT * FROM users ORDER BY views_count DESC LIMIT 6");
    $featured_projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured_projects = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Website Kelas Kami</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; color: #333; }
        .navbar { display: flex; justify-content: space-between; padding: 20px 5%; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .navbar a { text-decoration: none; color: #333; margin-left: 20px; font-weight: 500; }
        .hero { text-align: center; padding: 80px 20px; background: #ffffff; border-bottom: 1px solid #eaeaea; }
        .hero h1 { font-size: 38px; margin-bottom: 15px; color: #111; }
        .hero p { font-size: 18px; color: #666; max-width: 600px; margin: 0 auto 30px; }
        .btn-main { background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .container { max-width: 1100px; margin: 60px auto; padding: 0 20px; }
        .section-title { text-align: center; margin-bottom: 40px; font-size: 28px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
        .card { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .card-img { width: 100%; height: 180px; object-fit: cover; background-color: #ddd; }
        .card-content { padding: 20px; }
        .profile-meta { display: flex; align-items: center; margin-bottom: 15px; }
        .profile-img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px; background: #ccc; }
        .author-name { font-size: 14px; font-weight: 600; color: #555; }
        .card-title { font-size: 18px; margin: 10px 0; font-weight: bold; }
        .card-desc { font-size: 14px; color: #666; height: 60px; overflow: hidden; line-height: 1.4; margin-bottom: 15px; }
        .btn-detail { display: block; text-align: center; background: #f1f3f5; color: #333; padding: 10px; text-decoration: none; border-radius: 4px; font-weight: 500; }
        .btn-detail:hover { background: #e2e6ea; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo"><strong>Website Kelas</strong></div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="proyek.php">Proyek</a>
        <a href="tentang_kami.php">Tentang Kami</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="edit_profil.php">Dashboard</a>
            <a href="logout.php" style="color: red;">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="hero">
    <h1>Selamat Datang di Website Kelas Kami</h1>
    <p>Ruang pameran digital tempat mahasiswa membagikan hasil karya, portofolio, dan proyek website yang telah dikembangkan.</p>
    <a href="proyek.php" class="btn-main">Jelajahi Semua Website</a>
</div>

<div class="container">
    <h2 class="section-title">Karya Unggulan Mahasiswa (Most Viewed)</h2>
    <div class="grid">
        <?php if (!empty($featured_projects)): ?>
            <?php foreach ($featured_projects as $mhs): ?>
                <div class="card">
                    <img src="<?= !empty($mhs['thumbnail']) ? $mhs['thumbnail'] : 'https://placehold.co/400x225/e9ecef/495057?text=No+Thumbnail'; ?>" class="card-img" alt="Thumbnail">
                    
                    <div class="card-content">
                        <div class="profile-meta">
                            <img src="<?= !empty($mhs['profile_photo']) ? $mhs['profile_photo'] : 'https://placehold.co/150x150/e9ecef/495057?text=User'; ?>" class="profile-img" alt="Foto Profil">
                            <span class="author-name"><?= htmlspecialchars($mhs['full_name']); ?></span>
                        </div>
                        
                        <h3 class="card-title"><?= htmlspecialchars($mhs['website_title'] ?? 'Belum ada judul'); ?></h3>
                        <p class="card-desc"><?= htmlspecialchars($mhs['bio'] ?? 'Tidak ada deskripsi singkat.'); ?></p>
                        
                        <a href="detail.php?slug=<?= $mhs['slug']; ?>" class="btn-detail">Lihat Detail</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1; color: #666;">Belum ada proyek unggulan yang ditampilkan.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>