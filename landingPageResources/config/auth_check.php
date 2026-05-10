<?php
// auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// 2. Cek apakah user wajib ganti password (kecuali jika sedang di halaman ganti_password.php)
if ($_SESSION['is_password_changed'] == 0 && basename($_SERVER['PHP_SELF']) !== '../../ganti_password.php') {
    header("Location: ../../ganti_password.php");
    exit();
}
?>