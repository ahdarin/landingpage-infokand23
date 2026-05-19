<?php
// admin/logout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Bersihkan semua data session spesifik milik admin
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);

// 2. Hancurkan cookie session di browser agar token hangus total
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Tendang kembali ke halaman login admin
header("Location: login.php");
exit();