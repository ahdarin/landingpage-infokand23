<?php
// admin/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Menggunakan ../ karena letak file login ini berada di dalam subfolder admin/
require_once '../landingPageResources/config/koneksi.php';

// Jika admin terdeteksi sudah login, langsung alihkan ke dashboard admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            // Memeriksa database admin khusus
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            // Verifikasi kecocokan password hash BCRYPT
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = $admin['id'];
                $_SESSION['admin_username']  = $admin['username'];
                $_SESSION['admin_name']      = $admin['full_name'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Username atau Password admin salah.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem database.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Informatika'23 | Admin Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white antialiased">

    <a href="../index.php" class="fixed top-8 left-8 z-50 flex items-center justify-center w-12 h-12 text-[#0d8276] md:text-white transition-all group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <div class="hidden md:flex md:w-1/2 relative overflow-hidden items-center px-12 lg:px-24">

            <div id="silk-container" class="absolute inset-0 z-0"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <img src="../landingPageResources/assets/img/LOGO IF.png" alt="Logo" class="w-10 h-10 bg-white rounded-full p-1">
                    <span class="text-white text-xl font-bold">Informatika'23</span>
                </div>
                <h1 class="text-white text-5xl lg:text-7xl font-bold mb-6 leading-tight">Admin <br> <span class="text-[#7DF5F4]">KanDua</span></h1>
                <p class="text-gray-400 text-lg max-w-md leading-relaxed">
                    Authorized access only. Log in to manage configurations, process credential requests, and curate content deployment.
                </p>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 bg-gray-200 md:bg-gray-50">
            <div class="w-full max-w-md bg-white rounded-[2rem] p-8 md:p-10 shadow-2xl md:shadow-none border border-gray-100">
                
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Login Atemin</h2>
                    <p class="text-gray-500">Access the core management platform</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-medium border border-red-100 text-center">
                        <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                                @
                            </span>
                            <input type="text" name="username" required autocomplete="off"
                                class="w-full bg-gray-50 border border-gray-100 text-gray-900 rounded-2xl py-4 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-[#0d8276]/20 focus:border-[#0d8276] transition-all"
                                placeholder="Enter Username">
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2 ml-1">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Password</label>
                            </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" name="password" required 
                                class="w-full bg-gray-50 border border-gray-100 text-gray-900 rounded-2xl py-4 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-[#0d8276]/20 focus:border-[#0d8276] transition-all"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#0d8276] hover:bg-[#0a6b61] text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-teal-900/20">
                        Sign In <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script src="../landingPageResources/assets/silk.js"></script>
</body>
</html>