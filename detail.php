<?php
// detail.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'landingPageResources/config/koneksi.php';

// 1. Ambil parameter slug dari URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    $_SESSION['toast_error'] = "User data not found or the user has not verified their account yet.";
    header("Location: index.php");
    exit();
}

try {
    // 2. Ambil data mahasiswa berdasarkan slug DAN pastikan sudah ganti password (is_password_changed = 1)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE slug = :slug AND is_password_changed = 1 AND is_hidden = 0");
    $stmt->execute([':slug' => $slug]);
    $mhs = $stmt->fetch();

    // Jika data tidak ditemukan atau is_password_changed masih 0
    if (!$mhs) {
        $_SESSION['toast_error'] = "User data not found or the user has not verified their account yet.";
        header("Location: index.php");
        exit();
    }

    // 3. Logika Views Count (Cegah Spam dengan Session)
    $session_key = 'viewed_' . $slug;
    if (!isset($_SESSION[$session_key])) {
        $update_views = $pdo->prepare("UPDATE users SET views_count = views_count + 1 WHERE id = :id");
        $update_views->execute([':id' => $mhs['id']]);
        $_SESSION[$session_key] = true;
        $mhs['views_count'] += 1;
    }

    // 4. Ambil Proyek Lain (Archive) secara acak yang SUDAH ganti password juga
    $stmt_others = $pdo->prepare("SELECT * FROM users WHERE id != :id AND is_password_changed = 1 AND is_hidden = 0 ORDER BY RAND() LIMIT 3");
    $stmt_others->execute([':id' => $mhs['id']]);
    $other_projects = $stmt_others->fetchAll();

} catch (PDOException $e) {
    die("Terjadi kesalahan sistem: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Informatika'23 | <?= htmlspecialchars($mhs['website_title'] ?? 'Detail Website'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white antialiased min-h-screen flex flex-col">

    <?php 
    include 'landingPageResources/components/navbar.php'; 
    ?>

    <main class="flex-grow w-full pt-28 pb-0">
        
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            
            <div class="relative w-full aspect-video md:aspect-auto md:h-[500px] rounded-[32px] overflow-hidden shadow-lg group">
                
                <img src="<?= !empty($mhs['thumbnail']) ? $mhs['thumbnail'] : 'https://placehold.co/1200x600/e2e8f0/64748b?text=No+Thumbnail'; ?>" 
                    alt="Project Banner" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent"></div>
                
                <div class="absolute bottom-0 left-0 w-full p-6 md:p-12 flex flex-row justify-between items-end gap-4 md:gap-6">
                    
                    <div class="text-left flex-1 min-w-0">
                        <h1 class="text-xl md:text-5xl font-bold text-white mb-1 md:mb-2 leading-tight truncate md:whitespace-normal">
                            <?= htmlspecialchars($mhs['website_title'] ?? 'Project Title'); ?>
                        </h1>
                        <p class="text-gray-300 text-xs md:text-lg font-medium truncate md:whitespace-normal">
                            by <?= htmlspecialchars($mhs['full_name']); ?>
                        </p>
                    </div>
                    
                    <?php if (!empty($mhs['website_link'])): ?>
                    <a href="<?= htmlspecialchars($mhs['website_link']); ?>" target="_blank" 
                    class="bg-[#0d8276] hover:bg-[#0a6b61] text-white px-5 md:px-8 py-2.5 md:py-3.5 rounded-full text-xs md:text-base font-bold flex items-center gap-2 transition shadow-lg shadow-teal-500/30 whitespace-nowrap shrink-0">
                        Visit Website
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-16 mb-24">
                
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Project Overview</h2>
                    <div class="text-gray-600 leading-relaxed whitespace-pre-line">
                        <?= !empty($mhs['description']) ? htmlspecialchars($mhs['description']) : 'Belum ada deskripsi lengkap untuk website ini.'; ?>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white sticky top-24 rounded-3xl p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full overflow-hidden mb-4 ring-4 ring-gray-50">
                            <img src="<?= !empty($mhs['profile_photo']) ? $mhs['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($mhs['full_name']).'&background=0b1727&color=fff&size=200'; ?>" 
                                 alt="Profile Photo" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($mhs['full_name']); ?></h3>
                        <p class="text-xs text-gray-400 font-medium tracking-widest mb-6 uppercase"><?= htmlspecialchars($mhs['nim']); ?></p>
                        
                        <p class="text-sm text-gray-500 mb-8 leading-relaxed italic">
                            "<?= htmlspecialchars($mhs['bio'] ?? 'Mahasiswa Informatika Universitas Andalas'); ?>"
                        </p>
                        
                        <div class="w-full flex flex-col gap-3">
                            <div class="w-full bg-gray-50 text-gray-500 font-bold py-3 rounded-xl flex items-center justify-center gap-3 border border-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span class="text-xs uppercase tracking-wider">Views</span>
                                <span class="text-gray-900"><?= number_format($mhs['views_count']); ?></span>
                            </div>

                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $mhs['id']): ?>
                                <a href="edit_profil.php" class="w-full bg-[#0d8276] hover:bg-[#0a6b61] text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-teal-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Profil
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <?php if (!empty($other_projects)): ?>
        <section class="bg-[#f8fafc] py-20 px-8 border-t border-gray-100">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-12">
                    <h2 class="text-2xl font-bold text-gray-900">More from the Archive</h2>
                    <a href="proyek.php" class="text-[#0d8276] font-bold hover:text-[#0a6b61] flex items-center gap-1 text-sm tracking-wider uppercase">VIEW ALL <span>→</span></a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($other_projects as $other): ?>
                    <div class="bg-white rounded-[24px] p-4 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300 group flex flex-col">
                        <div class="overflow-hidden rounded-xl mb-4 h-48 bg-gray-900">
                            <img src="<?= !empty($other['thumbnail']) ? $other['thumbnail'] : 'https://placehold.co/600x400/1e293b/white?text=No+Thumbnail'; ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="flex items-center gap-2 mb-3 px-2">
                            <img src="<?= !empty($other['profile_photo']) ? $other['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($other['full_name']).'&background=random&color=fff'; ?>" class="w-6 h-6 rounded-full object-cover">
                            <span class="text-xs font-semibold text-gray-600"><?= htmlspecialchars($other['full_name']); ?></span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 px-2"><?= htmlspecialchars($other['website_title'] ?? 'Untitled Project'); ?></h3>
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2 px-2 flex-grow">
                            <?= htmlspecialchars($other['bio'] ?? 'Tidak ada deskripsi singkat.'); ?>
                        </p>
                        <a href="detail.php?slug=<?= $other['slug']; ?>" class="flex items-center justify-center gap-2 w-full bg-[#f1f5f9] hover:bg-gray-200 text-gray-800 font-semibold py-3.5 rounded-xl text-sm transition mt-auto">
                            View Website
                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

    </main>

    
    <?php include 'landingPageResources/components/footer.php'; ?>

</body>
</html>