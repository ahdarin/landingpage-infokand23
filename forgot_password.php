<?php
// forgot_password.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'landingPageResources/config/koneksi.php';

$show_wa_button = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = trim($_POST['nim']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE nim = :nim");
    $stmt->execute([':nim' => $nim]);
    $user = $stmt->fetch();
    
    if ($user) {
        // 1. Log request ke database (ubah status jadi 1)
        $update = $pdo->prepare("UPDATE users SET request_change_password = 1 WHERE id = :id");
        $update->execute([':id' => $user['id']]);
        
        $show_wa_button = true;
    } else {
        $error = "Student ID (NIM) not found in our records.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link class="rounded-full" rel="icon" href="landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Informatika'23 | Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white antialiased">

    <a href="login.php" class="fixed top-8 left-8 z-50 flex items-center justify-center w-12 h-12 text-[#0d8276] md:text-white transition-all group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <div class="hidden md:flex md:w-1/2 relative overflow-hidden items-center px-12 lg:px-24 bg-[#0b1727]">
            <div id="silk-container" class="absolute inset-0 z-0"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <img src="landingPageResources/assets/img/LOGO IF.png" alt="Logo" class="w-10 h-10 bg-white rounded-full p-1">
                    <span class="text-white text-xl font-bold">Informatika'23</span>
                </div>
                <h1 class="text-white text-5xl lg:text-7xl font-bold mb-6 leading-tight">Reset <br> <span class="text-[#7DF5F4]">Account</span></h1>
                <p class="text-gray-400 text-lg max-w-md leading-relaxed">
                    Lost your credentials? Don't worry. Submit your request to establish secure synchronization with the administration node.
                </p>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 bg-gray-200 md:bg-gray-50">
            <div class="w-full max-w-md bg-white rounded-[2rem] p-8 md:p-10 shadow-2xl md:shadow-none border border-gray-100">
                
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Reset Request</h2>
                    <p class="text-gray-500">Recover access to your account</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-medium border border-red-100 text-center">
                        <?= $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$show_wa_button): ?>
                    <form action="" method="POST" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">NIM / Student ID</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                                    @
                                </span>
                                <input type="text" name="nim" required autocomplete="off"
                                    class="w-full bg-gray-50 border border-gray-100 text-gray-900 rounded-2xl py-4 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-[#0d8276]/20 focus:border-[#0d8276] transition-all"
                                    placeholder="Enter your Student ID (NIM)">
                            </div>
                        </div>

                        <button type="submit" 
                            class="w-full bg-[#0d8276] hover:bg-[#0a6b61] text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-teal-900/20">
                            Submit Request <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center space-y-6 py-4">
                        <div class="w-16 h-16 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto border border-green-100 shadow-md shadow-green-100/50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        
                        <div class="space-y-2">
                            <h3 class="text-xl font-bold text-gray-900">Request Registered</h3>
                            <p class="text-sm text-gray-500 leading-relaxed max-w-sm mx-auto">
                                Your data reset alert has been safely logged. Please open the terminal below to clear verification with the administrator.
                            </p>
                        </div>
                        
                        <a href="https://wa.me/6281275471093?text=da,%20ganti%20password%20infokand%20da" target="_blank" 
                           class="w-full inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20ba59] text-white font-bold py-4 rounded-2xl text-sm transition-all shadow-lg shadow-green-500/20">
                            Notify Admin Through WhatsApp
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="landingPageResources/assets/silk.js"></script>
</body>
</html>