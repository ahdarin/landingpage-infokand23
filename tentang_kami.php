<?php
// tentang_kami.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang Kami - Website Kelas</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f8f9fa; color: #333; }
        .navbar { display: flex; justify-content: space-between; padding: 20px 5%; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .navbar a { text-decoration: none; color: #333; margin-left: 20px; font-weight: 500; }
        .container { max-width: 800px; margin: 60px auto; padding: 0 20px; }
        .about-card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); text-align: center; }
        .class-photo { width: 100%; height: auto; max-height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 30px; background-color: #ddd; }
        .about-card h1 { font-size: 32px; margin-bottom: 20px; color: #111; }
        .about-card p { font-size: 16px; line-height: 1.8; color: #555; text-align: justify; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-top: 40px; }
        .stat-box { background: #fdfdfd; border: 1px solid #eee; padding: 20px; border-radius: 8px; }
        .stat-box h3 { font-size: 28px; color: #007bff; margin: 0 0 5px; }
        .stat-box p { font-size: 14px; color: #666; margin: 0; text-align: center; }
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
    <div class="about-card">
        <img src="https://via.placeholder.com/800x400?text=Foto+Bersama+Kelas" class="class-photo" alt="Foto Bersama Kelas">
        
        <h1>Tentang Kelas Kami</h1>
        <p>
            Website ini dirancang sebagai platform portofolio bersama untuk menampilkan berbagai hasil karya dan proyek website yang dikembangkan oleh mahasiswa kelas kami. Melalui wadah ini, setiap mahasiswa dapat membagikan hasil belajarnya sekaligus melatih kemampuan pengembangan web secara langsung.
        </p>

        <div class="stats-grid">
            <div class="stat-box">
                <h3>30+</h3>
                <p>Mahasiswa</p>
            </div>
            <div class="stat-box">
                <h3>1</h3>
                <p>Visi Bersama</p>
            </div>
            <div class="stat-box">
                <h3>Aktif</h3>
                <p>Kolaborasi</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>