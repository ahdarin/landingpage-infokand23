<?php
// seeder.php
require_once '../config/koneksi.php';

// Fungsi untuk membuat URL Slug otomatis
function createSlug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($string)));
    return trim($slug, '-');
}

// 1. Baca data dari file mahasiswa.json
$json_data = file_get_contents('mahasiswa.json');
$mahasiswa_baru = json_decode($json_data, true); // true agar menjadi array asosiatif PHP

if ($mahasiswa_baru === null) {
    die("Gagal membaca file JSON. Pastikan format JSON sudah benar.");
}

try {
    // Siapkan statement SQL
    $stmt = $pdo->prepare("INSERT INTO users (nim, password, full_name, slug, website_link) 
                           VALUES (:nim, :password, :full_name, :slug, :website_link)
                           ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)");

    $count = 0;
    foreach ($mahasiswa_baru as $mhs) {
        $hashed_password = password_hash($mhs['password'], PASSWORD_BCRYPT);
        $slug = createSlug($mhs['full_name']);

        $stmt->execute([
            ':nim'          => $mhs['nim'],
            ':password'     => $hashed_password,
            ':full_name'    => $mhs['full_name'],
            ':slug'         => $slug,
            ':website_link' => $mhs['website_link']
        ]);
        $count++;
    }

    echo "Berhasil! Sebanyak " . $count . " data mahasiswa berhasil dimasukkan ke database.";
} catch (PDOException $e) {
    echo "Gagal memasukkan data: " . $e->getMessage();
}
?>