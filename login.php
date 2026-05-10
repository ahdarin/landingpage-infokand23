<?php
// login.php
session_start();
require_once 'landingpageResources/config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
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

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nim'] = $user['nim'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['is_password_changed'] = $user['is_password_changed'];

                if ($user['is_password_changed'] == 0) {
                    header("Location: ganti_password.php");
                } else {
                    header("Location: index.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Informatika'23 | Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white antialiased">

    <a href="index.php" class="fixed top-8 left-8 z-50 flex items-center justify-center w-12 h-12 text-[#0d8276] md:text-white transition-all group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <div class="hidden md:flex md:w-1/2 relative overflow-hidden items-center px-12 lg:px-24">

            <div id="silk-container" class="absolute inset-0 z-0"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <img src="landingPageResources/assets/img/LOGO IF.png" alt="Logo" class="w-10 h-10 bg-white rounded-full p-1">
                    <span class="text-white text-xl font-bold">Informatika'23</span>
                </div>
                <h1 class="text-white text-5xl lg:text-7xl font-bold mb-6 leading-tight">Welcome <br> <span class="text-[#7DF5F4]">Back</span></h1>
                <p class="text-gray-400 text-lg max-w-md leading-relaxed">
                    Continue your journey within the Informatika'23 creative ecosystem. Your innovation starts here.
                </p>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 bg-gray-200 md:bg-gray-50">
            <div class="w-full max-w-md bg-white rounded-[2rem] p-8 md:p-10 shadow-2xl md:shadow-none border border-gray-100">
                
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Sign In</h2>
                    <p class="text-gray-500">Access your curated dashboard</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-medium border border-red-100">
                        <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">NIM / Student ID</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                                @
                            </span>
                            <input type="text" name="nim" required 
                                class="w-full bg-gray-50 border border-gray-100 text-gray-900 rounded-2xl py-4 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-[#0d8276]/20 focus:border-[#0d8276] transition-all"
                                placeholder="Masukkan NIM Anda">
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2 ml-1">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Password</label>
                            <a href="https://open.spotify.com/track/1XabvPK1VQEH4YqzDovs46?si=228b24bd30024512" class="text-[10px] font-bold text-[#0d8276] uppercase tracking-wider">Forgot Password</a>
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
                        Login <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>

                <!-- <div class="mt-8">
                    <div class="relative flex items-center justify-center mb-8">
                        <div class="flex-grow border-t border-gray-100"></div>
                        <span class="flex-shrink mx-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Social Entry</span>
                        <div class="flex-grow border-t border-gray-100"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button class="flex items-center justify-center gap-3 bg-gray-50 hover:bg-gray-100 border border-gray-100 py-3 rounded-xl transition-all">
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">
                            <span class="text-xs font-bold text-gray-700">Google</span>
                        </button>
                        <button class="flex items-center justify-center gap-3 bg-gray-50 hover:bg-gray-100 border border-gray-100 py-3 rounded-xl transition-all">
                            <img src="https://www.svgrepo.com/show/512317/github-142.svg" class="w-5 h-5">
                            <span class="text-xs font-bold text-gray-700">GitHub</span>
                        </button>
                    </div>
                </div>

                <p class="mt-10 text-center text-sm text-gray-500">
                    Don't have an account? <a href="#" class="text-[#0d8276] font-bold hover:underline">Register</a>
                </p> -->

            </div>
        </div>
    </div>

    <script src="landingPageResources/assets/silk.js"></script>
</body>
</html>