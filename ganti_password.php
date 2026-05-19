<?php
// ganti_password.php
session_start();
require_once 'landingPageResources/config/koneksi.php';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Informatika'23 | Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white antialiased">

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <div class="hidden md:flex md:w-1/2 relative overflow-hidden items-center px-12 lg:px-24">

            <div id="silk-container" class="absolute inset-0 z-0"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <img src="landingPageResources/assets/img/LOGO IF.png" alt="Logo" class="w-10 h-10 bg-white rounded-full p-1">
                    <span class="text-white text-xl font-bold">Informatika'23</span>
                </div>
                <h1 class="text-white text-5xl lg:text-7xl font-bold mb-6 leading-tight">Change <br> <span class="text-[#7DF5F4]">Password</span></h1>
                <p class="text-gray-400 text-lg max-w-md leading-relaxed">
                    Keep your account safe and maintain secure access to the Informatika'23 ecosystem.
                </p>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 bg-gray-200 md:bg-gray-50">
            <div class="w-full max-w-md bg-white rounded-[2rem] p-8 md:p-10 shadow-2xl md:shadow-none border border-gray-100">
                
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-teal-50 text-[#0d8276] rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Secure Your Account</h2>
                    <p class="text-sm text-gray-500">Because this is your first login, you are required to change your password for security reasons.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-medium border border-red-100 flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?= $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="bg-teal-50 text-[#0d8276] p-4 rounded-xl mb-6 text-sm font-medium border border-teal-100 flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?= $success; ?>
                    </div>
                <?php endif; ?>

                <form action="ganti_password.php" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">New Password</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-4 flex items-center text-gray-400 group-focus-within:text-[#0d8276] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" id="password_baru" name="password_baru" required 
                                class="w-full bg-gray-50 border border-gray-100 text-gray-900 rounded-2xl py-4 pl-10 pr-4 focus:outline-none focus:ring-4 focus:ring-[#0d8276]/10 focus:border-[#0d8276] transition-all"
                                placeholder="Enter your new password">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Confirm New Password</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-4 flex items-center text-gray-400 group-focus-within:text-[#0d8276] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" required 
                                class="w-full bg-gray-50 border border-gray-100 text-gray-900 rounded-2xl py-4 pl-10 pr-4 focus:outline-none focus:ring-4 focus:ring-[#0d8276]/10 focus:border-[#0d8276] transition-all"
                                placeholder="Re-enter your new password">
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#0d8276] hover:bg-[#0a6b61] text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-xl shadow-[#0d8276]/20 active:scale-[0.98]">
                        Simpan Password <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script src="landingPageResources/assets/silk.js"></script>

</body>
</html>