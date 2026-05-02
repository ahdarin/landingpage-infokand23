<?php
// login.php
session_start();
require_once 'koneksi.php';

// Jika sudah login, langsung arahkan ke dashboard/halaman proyek
if (isset($_SESSION['user_id'])) {
    header("Location: proyek.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = trim($_POST['nim']);
    $password = trim($_POST['password']);

    if (!empty($nim) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE nim = :nim");
            $stmt->execute([':nim' => $nim]);
            $user = $stmt->fetch();

            // Verifikasi password yang di-hash
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nim'] = $user['nim'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['is_password_changed'] = $user['is_password_changed'];

                // Jika password belum diganti, arahkan ke ganti password
                if ($user['is_password_changed'] == 0) {
                    header("Location: ganti_password.php");
                } else {
                    header("Location: proyek.php");
                }
                exit();
            } else {
                $error = "NIM atau Password salah.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem.";
        }
    } else {
        $error = "Harap isi semua field.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Website Kelas</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f4f6f9; }
        .login-card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 15px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Login Mahasiswa</h2>
    <?php if (!empty($error)): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim" required placeholder="Masukkan NIM">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Masukkan Password">
        </div>
        <button type="submit">Masuk</button>
    </form>
</div>

</body>
</html>