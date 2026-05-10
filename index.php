<?php
// Logika Backend
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'landingpageResources/config/koneksi.php';

$your_website = null;
if (isset($_SESSION['user_id'])) {
    $stmt_mine = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt_mine->execute([':id' => $_SESSION['user_id']]);
    $your_website = $stmt_mine->fetch();
}

try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY views_count DESC LIMIT 6");
    $featured_projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured_projects = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Informatika'23</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        html {
            scroll-behavior: smooth;
        }
        #tentang, #proyek {
            scroll-margin-top: 80px; 
        }
        #grainient-container {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        #grainient-container canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }
    </style>
</head>
<body class="bg-[#f8fafc] antialiased flex flex-col min-h-screen relative">

    <div class="relative">
        <?php 
        include 'landingpageResources/components/navbar.php'; 
        ?>

        <!-- Hero Section -->
        <section class="text-white pt-40 pb-24 px-8 overflow-hidden relative bg-[#0b1727]" id="beranda" id="beranda">

            <div id="grainient-container"></div>

            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="z-10">
                    <h1 class="text-5xl md:text-6xl font-bold leading-tight mb-6">
                        Welcome to <br> <span class="text-teal-400">Informatika'23</span>
                    </h1>
                    <p class="text-gray-300 text-lg mb-8 max-w-md">
                        Discover and showcase personal websites, digital portfolio, and creative projects from Informatika'23 students.
                    </p>
                    <div class="flex gap-4">
                        <a href="proyek.php" class="bg-[#0d8276] hover:bg-[#0a6b61] px-6 py-3 rounded-full font-medium flex items-center gap-2 transition shadow-lg shadow-teal-500/20">
                            Explore Projects <span>→</span>
                        </a>
                        <a href="https://instagram.com/if.ua23"  target="_blank" class="inline-block border border-gray-400 hover:border-white px-6 py-3 rounded-full font-medium transition hover:bg-white/5 text-center">
                            Visit our Instagram <span>↗</span>
                        </a>
                    </div>
                </div>

                <!-- Bagian Gambar Melayang -->
                <div class="relative hidden md:block h-80">
                    <!-- Card 1 -->
                    <div class="absolute top-0 right-20 bg-gray-800 p-2 rounded-xl shadow-2xl border border-gray-700 w-64 transform rotate-[-5deg] hover:rotate-0 transition duration-300">
                        <!-- Container dengan Rasio 400x250 (1.6:1) -->
                        <div class="w-full aspect-[400/250] bg-gray-900 rounded-lg overflow-hidden flex items-center justify-center p-4">
                            <img src="landingpageResources/assets/img/Logo Unand PTNBH.png" 
                                class="max-w-full max-h-full object-contain" 
                                alt="Project 1">
                        </div>
                        <p class="text-xs mt-2 font-medium text-gray-300">Andalas University</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="absolute bottom-0 right-0 bg-gray-800 p-2 rounded-xl shadow-2xl border border-gray-700 w-64 transform rotate-[5deg] hover:rotate-0 transition duration-300">
                        <!-- Container dengan Rasio 400x250 (1.6:1) -->
                        <div class="w-full aspect-[400/250] bg-gray-900 rounded-lg overflow-hidden flex items-center justify-center p-4">
                            <img src="landingpageResources/assets/img/LOGO IF.png" 
                                class="max-w-full max-h-full object-contain" 
                                alt="Project 2">
                        </div>
                        <p class="text-xs mt-2 font-medium text-gray-300">Informatika'23</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission Section (Foto Angkatan & Stats) -->
        <section class="bg-white py-24 px-8" id="tentang">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <img src="landingpageResources/assets/img/class-photo.webp" alt="Class Photo" class="rounded-2xl shadow-lg w-full aspect-video md:aspect-auto md:h-80 object-cover">
                    <div class="absolute -bottom-6 right-6 bg-[#0d8276] text-white p-6 rounded-2xl shadow-xl">
                        <h3 class="text-3xl font-bold">200+</h3>
                        <p class="text-sm font-medium">Dosa Besar Capstone</p>
                    </div>
                </div>
                <div>
                    <span class="text-xs font-bold text-[#0d8276] bg-teal-50 px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block">Our Mission</span>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6 leading-tight">Curating the Future of Digital Innovation</h2>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        The Informatika'23 Platform was born from a need to bridge academic excellence with creative expression. We provide a space where every student's technical journey becomes a visual narrative, fostering collaboration and visibility across the digital atelier.
                    </p>
                    <div class="flex gap-4 items-center">
                        <a href="proyek.php" class="border-2 border-[#0d8276] text-[#0d8276] hover:bg-teal-50 px-6 py-2 rounded-full font-medium transition">Our Project</a>
                        <a href="tentang_kami.php" class="text-gray-900 font-medium hover:text-[#0d8276] flex items-center gap-1 transition">Learn More <span>›</span></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <!-- CONDITIONAL AREA: YOUR WEBSITE (Sederhana) -->
            <?php if (isset($_SESSION['user_id']) && $your_website): ?>

                <div class="flex-grow max-w-7xl mx-auto w-full px-4 md:px-8 py-12">
                    <div class="bg-gradient-to-r from-[#ffffff] to-[#86c0ba] rounded-3xl p-8 flex flex-col md:flex-row items-center gap-8 border border-gray-100">
                        
                        <!-- Konten Teks (Kiri) -->
                        <div class="flex-1">
                            <div class="inline-flex items-center gap-1.5 bg-teal-50 text-[#0d8276] px-3 py-1 rounded-full text-xs font-semibold mb-4 border border-teal-100">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Website Anda
                            </div>
                            
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">
                                <?= htmlspecialchars($your_website['website_title'] ?? 'Belum ada judul'); ?>
                            </h3>
                            
                            <p class="text-gray-500 text-sm mb-8 leading-relaxed max-w-md">
                                <?= htmlspecialchars($your_website['bio'] ?? 'Tidak ada deskripsi singkat.'); ?>
                            </p>
                            
                            <div class="flex items-center gap-3">
                                <!-- Tombol Detail -->
                                <a href="detail.php?slug=<?= $your_website['slug']; ?>" class="bg-[#e2e8f0] hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-full font-medium text-sm transition text-center">
                                    Detail
                                </a>
                                
                                <!-- Tombol Visit (Link ke Website) -->
                                <a href="<?= $your_website['website_link']; ?>" class="bg-[#0d8276] hover:bg-[#0a6b61] text-white px-6 py-2.5 rounded-full font-medium text-sm flex items-center gap-2 transition">
                                    Visit Website
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Kontainer Gambar (Kanan) -->
                        <div class="w-full md:w-1/2 rounded-2xl overflow-hidden shadow-lg border border-gray-200 aspect-video bg-gray-50">
                            <img src="<?= !empty($your_website['thumbnail']) ? $your_website['thumbnail'] : 'https://placehold.co/800x500/f8fafc/64748b?text=No+Thumbnail'; ?>" 
                                alt="My Website" 
                                class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Projects Grid Section -->
        <section id="proyek" class="bg-gray-50 pt-10 pb-20 px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Featured Websites</h2>
                        <p class="text-gray-500">Discover the latest additions to the directory</p>
                    </div>
                    <a href="proyek.php" class="text-[#0d8276] font-bold hover:text-[#0a6b61] flex flex-row items-center gap-1 whitespace-nowrap shrink-0">
                        VIEW ALL <span>→</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php if (!empty($featured_projects)): ?>
                        <?php foreach ($featured_projects as $mhs): ?>
                            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300 group">
                                <div class="overflow-hidden rounded-xl mb-4 h-48 bg-gray-900">
                                    <img src="<?= !empty($mhs['thumbnail']) ? $mhs['thumbnail'] : 'https://placehold.co/600x400/1e293b/white?text=No+Thumbnail'; ?>" alt="Project" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                </div>
                                <div class="flex items-center gap-2 mb-3">
                                    <img src="<?= !empty($mhs['profile_photo']) ? $mhs['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($mhs['full_name']).'&background=random&color=fff'; ?>" alt="Avatar" class="object-cover w-6 h-6 rounded-full">
                                    <span class="text-xs font-medium text-gray-600"><?= htmlspecialchars($mhs['full_name']); ?></span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars($mhs['website_title'] ?? 'Belum ada judul'); ?></h3>
                                <p class="text-sm text-gray-500 mb-6 line-clamp-3"><?= htmlspecialchars($mhs['bio'] ?? 'Tidak ada deskripsi singkat.'); ?></p>
                                <a href="detail.php?slug=<?= $mhs['slug']; ?>" class="block w-full text-center bg-gray-50 hover:bg-[#0d8276] hover:text-white text-gray-800 font-medium py-3 rounded-xl text-sm transition">View Website ↗</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="col-span-full text-center text-gray-500 py-12 italic">Belum ada proyek yang dapat ditampilkan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php include 'landingpageResources/components/footer.php'; ?>
    </div>

    <script src="landingPageResources/assets/grainient.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = ['beranda', 'tentang', 'proyek'];
            const navLinks = document.querySelectorAll('.nav-link');
            const underline = document.getElementById('nav-underline');

            function updateNav() {
                let currentSection = 'beranda';
                const scrollPosition = window.scrollY;

                sections.forEach(id => {
                    const section = document.getElementById(id);
                    if (section) {
                        const offset = section.offsetTop - 150;
                        if (scrollPosition >= offset) {
                            currentSection = id;
                        }
                    }
                });

                navLinks.forEach(link => {
                    const target = link.getAttribute('data-target');
                    const underline = document.getElementById('nav-underline');

                    if (target === currentSection && !link.classList.contains('active-manual')) {
                        link.classList.add('text-[#0d8276]');
                        link.classList.remove('text-gray-600', 'text-gray-300');
                        
                        if(underline) {
                            underline.style.opacity = "1";
                            underline.style.width = `${link.offsetWidth}px`;
                            underline.style.left = `${link.offsetLeft}px`;
                        }
                    } else if (!link.classList.contains('active-manual')) {
                        link.classList.remove('text-[#0d8276]');
                        link.classList.add('text-gray-600');
                    }
                });
            }

            window.addEventListener('scroll', updateNav);
            window.addEventListener('resize', updateNav);
            updateNav(); // Jalankan sekali saat load
        });
    </script>
    

</body>
</html>