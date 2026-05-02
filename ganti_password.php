<?php
// ganti_password.php
session_start();
require_once 'koneksi.php';

// Pastikan sudah login, tapi jangan gunakan auth_check.php agar tidak terjadi redirect loop
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Jika sudah pernah ganti password, arahkan langsung ke edit profil
if ($_SESSION['is_password_changed'] == 1) {
    header("Location: edit_profil.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);

    if (strlen($password_baru) >= 6) {
        if ($password_baru === $konfirmasi_password) {
            try {
                $hashed_password = password_hash($password_baru, PASSWORD_BCRYPT);
                
                $stmt = $pdo->prepare("UPDATE users SET password = :password, is_password_changed = 1 WHERE id = :id");
                $stmt->execute([
                    ':password' => $hashed_password,
                    ':id' => $_SESSION['user_id']
                ]);

                // Update session
                $_SESSION['is_password_changed'] = 1;
                $success = "Password berhasil diperbarui! Mengalihkan ke edit profil...";
                header("refresh:2; url=edit_profil.php");
            } catch (PDOException $e) {
                $error = "Gagal memperbarui password.";
            }
        } else {
            $error = "Konfirmasi password tidak cocok.";
        }
    } else {
        $error = "Password minimal harus memiliki 6 karakter.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password Wajib</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f4f6f9; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 380px; }
        .form-group { margin-bottom: 15px; }
        input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; font-size: 14px; }
        .success { color: green; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Ganti Password</h2>
    <p style="font-size: 13px; color: #666;">Demi keamanan akun, Anda diwajibkan untuk mengganti password bawaan terlebih dahulu.</p>
    
    <?php if (!empty($error)): ?> <div class="error"><?= $error; ?></div> <?php endif; ?>
    <?php if (!empty($success)): ?> <div class="success"><?= $success; ?></div> <?php endif; ?>

    <form action="ganti_password.php" method="POST">
        <div class="form-group">
            <label for="password_baru">Password Baru</label>
            <input type="password" id="password_baru" name="password_baru" required>
        </div>
        <div class="form-group">
            <label for="konfirmasi_password">Ulangi Password Baru</label>
            <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
        </div>
        <button type="submit">Simpan Password</button>
    </form>
</div>

</body>
</html>