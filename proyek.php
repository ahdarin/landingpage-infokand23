<?php
// proyek.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

// 1. Tangkap parameter Search & Sort
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// 2. Query data "Your Website" jika sudah login
$your_website = null;
if (isset($_SESSION['user_id'])) {
    $stmt_mine = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt_mine->execute([':id' => $_SESSION['user_id']]);
    $your_website = $stmt_mine->fetch();
}

// 3. Bangun query dinamis untuk "All Websites"
$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

// Tambahkan filter pencarian
if (!empty($search)) {
    $sql .= " AND (full_name LIKE :search OR website_title LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

// Tambahkan pengurutan
if ($sort === 'name') {
    $sql .= " ORDER BY full_name ASC";
} elseif ($sort === 'most_viewed') {
    $sql .= " ORDER BY views_count DESC";
} else {
    // Newest
    $sql .= " ORDER BY created_at DESC";
}

try {
    $stmt_all = $pdo->prepare($sql);
    $stmt_all->execute($params);
    $all_projects = $stmt_all->fetchAll();
} catch (PDOException $e) {
    $all_projects = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semua Proyek Mahasiswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f8f9fa; color: #333; }
        .navbar { display: flex; justify-content: space-between; padding: 20px 5%; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .navbar a { text-decoration: none; color: #333; margin-left: 20px; }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 40px; justify-content: space-between; flex-wrap: wrap; }
        .filter-bar form { display: flex; gap: 10px; width: 100%; max-width: 600px; }
        .search-input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .sort-select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; background: white; }
        .btn-filter { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .section-header { margin: 30px 0 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
        .card { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .card-img { width: 100%; height: 180px; object-fit: cover; background-color: #ddd; }
        .card-content { padding: 20px; }
        .profile-meta { display: flex; align-items: center; margin-bottom: 15px; }
        .profile-img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px; background: #ccc; }
        .author-name { font-size: 14px; font-weight: 600; color: #555; }
        .card-title { font-size: 18px; margin: 10px 0; font-weight: bold; }
        .card-desc { font-size: 14px; color: #666; height: 60px; overflow: hidden; margin-bottom: 15px; }
        .btn-detail { display: block; text-align: center; background: #f1f3f5; color: #333; padding: 10px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .btn-detail:hover { background: #e2e6ea; }
        /* Style Card Khusus Milik Anda */
        .my-card { border: 2px solid #28a745; background-color: #fcfdfc; }
        .badge-mine { background: #28a745; color: white; padding: 4px 8px; font-size: 11px; border-radius: 4px; float: right; }
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

<div class="container">
    
    <div class="filter-bar">
        <form action="proyek.php" method="GET" id="filterForm">
            <input type="text" name="search" class="search-input" value="<?= htmlspecialchars($search ?? ''); ?>" placeholder="Cari nama mahasiswa atau judul website...">
            
            <select name="sort" class="sort-select" onchange="document.getElementById('filterForm').submit()">
                <option value="newest" <?= (isset($sort) && $sort === 'newest') ? 'selected' : ''; ?>>Paling Baru</option>
                <option value="name" <?= (isset($sort) && $sort === 'name') ? 'selected' : ''; ?>>Berdasarkan Nama (A-Z)</option>
                <option value="most_viewed" <?= (isset($sort) && $sort === 'most_viewed') ? 'selected' : ''; ?>>Paling Populer</option>
            </select>
            
            <button type="submit" class="btn-filter">Cari</button>
        </form>
    </div>

    <?php if ($your_website && !empty($your_website['website_title'])): ?>
        <h2 class="section-header">Your Website</h2>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 400px)); margin-bottom: 50px;">
            <div class="card my-card">
                <img src="<?= !empty($your_website['thumbnail']) ? $your_website['thumbnail'] : 'https://placehold.co/400x225/e9ecef/495057?text=No+Thumbnail'; ?>" class="card-img" alt="Thumbnail">
                <div class="card-content">
                    <div class="profile-meta">
                        <img src="<?= !empty($your_website['profile_photo']) ? $your_website['profile_photo'] : 'https://placehold.co/150x150/e9ecef/495057?text=User'; ?>" class="profile-img" alt="Foto Profil">
                        <span class="author-name"><?= htmlspecialchars($your_website['full_name']); ?></span>
                        <span class="badge-mine">Website Anda</span>
                    </div>
                    <h3 class="card-title"><?= htmlspecialchars($your_website['website_title']); ?></h3>
                    <p class="card-desc"><?= htmlspecialchars($your_website['bio'] ?? 'Tidak ada deskripsi singkat.'); ?></p>
                    <a href="detail.php?slug=<?= $your_website['slug']; ?>" class="btn-detail">Lihat Detail</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h2 class="section-header">All Websites</h2>
    <div class="grid">
        <?php if (!empty($all_projects)): ?>
            <?php foreach ($all_projects as $mhs): ?>
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
            <p style="grid-column: 1/-1; text-align: center; color: #666; margin: 40px 0;">Tidak ada website yang ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>