<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
$loggedIn = isset($_SESSION['user_id']);

$your_website = null;
if ($loggedIn) {
    $stmt_mine = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt_mine->execute([':id' => $_SESSION['user_id']]);
    $your_website = $stmt_mine->fetch();
}

// Logika URL dinamis
function getDynamicLink($target, $current_page) {
    if ($current_page === 'index.php') {
        return "#" . $target;
    } else {
        if ($target === 'beranda') return "index.php";
        if ($target === 'tentang') return "tentang_kami.php";
        if ($target === 'proyek') return "proyek.php";
        return "index.php#" . $target;
    }
}
?>

<div id="nav-container" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    
    <div id="nav-overlay" class="absolute inset-0 z-[-1] transition-opacity duration-150 pointer-events-none" 
         style="background: linear-gradient(to bottom, rgba(11, 23, 39, 0.6) 0%, rgba(11, 23, 39, 0.4) 0%, transparent 100%); height: 150%;">
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-8 w-full pt-4" id="nav-content">
        <header class="bg-white/80 border-gray-100 mt-4 py-3 px-6 md:px-8 rounded-full flex justify-between items-center border backdrop-blur-sm transition-all duration-500">
            <div class="flex items-center gap-8">
                <a href="index.php" class="text-xl font-bold text-[#0d8276]">Informatika<span class="text-gray-900">'23</span></a>
                
                <nav class="hidden md:flex gap-6 text-sm font-medium relative">
                    <a href="<?= getDynamicLink('beranda', $current_page) ?>" 
                       data-target="beranda"
                       class="nav-link transition-colors duration-500 text-gray-600">
                       Beranda
                    </a>

                    <a href="<?= getDynamicLink('tentang', $current_page) ?>" 
                       data-target="tentang"
                       class="nav-link transition-colors duration-500 text-gray-600 hover:text-[#0d8276]">
                       Tentang Kami
                    </a>

                    <a href="<?= getDynamicLink('proyek', $current_page) ?>" 
                       data-target="proyek"
                       id="nav-proyek-link"
                       class="nav-link transition-colors duration-500 <?= ($current_page === 'proyek.php') ? 'text-[#0d8276]' : 'text-gray-600 hover:text-[#0d8276]' ?>">
                       Proyek
                    </a>

                    <?php if($current_page === 'index.php' || $current_page === 'proyek.php'): ?>
                        <div id="nav-underline" 
                            class="absolute bottom-[-6px] h-[2px] bg-[#0d8276] transition-all duration-500 ease-in-out 
                            <?= ($current_page === 'proyek.php') ? 'opacity-100' : 'opacity-0' ?>"
                            <?php 
                                if($current_page === 'proyek.php') {
                                    echo 'style="width: 52px; left: 192px;"'; 
                                }
                            ?>>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="flex items-center gap-4 md:gap-6 text-sm font-medium">
                <?php if($loggedIn): ?>
                    <div class="relative group ml-4 pl-4 border-l border-gray-200">
                        <button class="flex items-center gap-3 cursor-pointer focus:outline-none" id="userMenuButton">
                            <span class="hidden md:block text-gray-700 text-[11px] font-bold tracking-wider uppercase group-hover:text-[#0d8276] transition">
                                <?= htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                            </span>
                            <?php 
                            $userName = $_SESSION['full_name'] ?? 'User';
                            $profileImg = !empty($_SESSION['profile_photo']) ? $_SESSION['profile_photo'] : "https://ui-avatars.com/api/?name=".urlencode($userName)."&background=0b1727&color=fff";
                            ?>
                            <img src="<?= $profileImg; ?>" alt="Profile" class="w-8 h-8 rounded-full border-2 <?= ($current_page === 'edit_profil.php') ? 'border-[#0d8276]' : 'border-transparent group-hover:border-[#0d8276]'; ?> transition object-cover">
                        </button>

                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-[100] opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300">
                            <a href="detail.php?slug=<?= $your_website['slug'] ?? ''; ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-teal-50 hover:text-[#0d8276] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                <span>Your Website</span>
                            </a>
                            <div class="border-t border-gray-50 my-1"></div>
                            <a href="landingPageResources/config/logout.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-[#0d8276] transition uppercase text-xs tracking-wider">Sign In</a>
                <?php endif; ?>
            </div>
        </header>
    </div>
</div>