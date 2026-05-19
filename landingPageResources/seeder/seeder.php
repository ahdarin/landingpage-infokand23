<?php
// seeder.php
require_once '../config/koneksi.php'; // Sesuaikan path koneksi.php kamu

// Fungsi untuk membuat URL Slug otomatis
function createSlug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($string)));
    return trim($slug, '-');
}

try {
    echo "<pre>=== Memulai Proses Database Setup ===\n\n";

    // ==========================================
    // 1. MEMBUAT TABEL USERS (MEMBER)
    // ==========================================
    $sql_users = "CREATE TABLE IF NOT EXISTS `users` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `nim` VARCHAR(20) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `is_password_changed` TINYINT(1) DEFAULT 0,
        `request_change_password` TINYINT(1) DEFAULT 0,
        `is_hidden` TINYINT(1) DEFAULT 0,
        `full_name` VARCHAR(100) NOT NULL,
        `slug` VARCHAR(120) NOT NULL UNIQUE,
        `bio` TEXT NULL,
        `profile_photo` VARCHAR(255) NULL,
        `website_title` VARCHAR(150) NULL,
        `website_link` VARCHAR(255) NULL,
        `thumbnail` VARCHAR(255) NULL,
        `description` TEXT NULL,
        `views_count` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;";
    
    $pdo->exec($sql_users);
    echo "[✔] Tabel 'users' berhasil disiapkan (atau sudah ada).\n";


    // ==========================================
    // 2. MEMBUAT TABEL ADMIN
    // ==========================================
    $sql_admin = "CREATE TABLE IF NOT EXISTS `admin_users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `full_name` VARCHAR(100) NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;";

    $pdo->exec($sql_admin);
    echo "[✔] Tabel 'admin_users' berhasil disiapkan (atau sudah ada).\n";


    // ==========================================
    // 3. SEEDER DATA MAHASISWA (USERS) FROM JSON
    // ==========================================
    $json_file = 'mahasiswa.json'; // Pastikan file mahasiswa.json berada di folder yang sama
    
    if (!file_exists($json_file)) {
        throw new Exception("File '$json_file' tidak ditemukan! Proses seeder mahasiswa dibatalkan.");
    }

    $json_data = file_get_contents($json_file);
    $mahasiswa_baru = json_decode($json_data, true);

    if ($mahasiswa_baru === null) {
        throw new Exception("Gagal membaca file JSON. Pastikan format sintaks di '$json_file' sudah benar.");
    }

    $stmt_user = $pdo->prepare("INSERT INTO users (nim, password, full_name, slug, website_link) 
                               VALUES (:nim, :password, :full_name, :slug, :website_link)
                               ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)");

    $user_count = 0;
    foreach ($mahasiswa_baru as $mhs) {
        $hashed_user_password = password_hash($mhs['password'], PASSWORD_BCRYPT);
        $slug = createSlug($mhs['full_name']);

        $stmt_user->execute([
            ':nim'          => $mhs['nim'],
            ':password'     => $hashed_user_password,
            ':full_name'    => $mhs['full_name'],
            ':slug'         => $slug,
            ':website_link' => $mhs['website_link']
        ]);
        $user_count++;
    }
    echo "[✔] Seeder Mahasiswa Berhasil: $user_count data masuk/diperbarui.\n";


    // ==========================================
    // 4. SEEDER DATA ADMIN
    // ==========================================
    // Sesuai request-mu, ubah nilai di bawah ini dengan password admin asli yang kamu inginkan!
    $admin_username_real = 'admin';
    $admin_password_real = 'admin123'; 
    $admin_name_real     = 'Admin Utama';

    $hashed_admin_password = password_hash($admin_password_real, PASSWORD_BCRYPT);

    $stmt_admin = $pdo->prepare("INSERT INTO admin_users (username, password, full_name) 
                                 VALUES (:user, :pass, :name)
                                 ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)");
    
    $stmt_admin->execute([
        ':user' => $admin_username_real,
        ':pass' => $hashed_admin_password,
        ':name' => $admin_name_real
    ]);
    echo "[✔] Seeder Admin Berhasil: Akun admin terenkripsi siap digunakan.\n\n";

    echo "=== Seluruh Proses Setup Berhasil Dieksekusi! ===</pre>";

} catch (Exception $e) {
    die("\n[❌] PROSES GAGAL: " . $e->getMessage() . "</pre>");
}
?>