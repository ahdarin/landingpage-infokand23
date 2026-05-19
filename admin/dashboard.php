<?php
// admin/dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../landingPageResources/config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Ambil data OTP dari session jika ada (untuk keperluan tampilan modal setelah redirect)
$generated_password = $_SESSION['mhs_otp_password'] ?? '';
$target_mhs_name = $_SESSION['mhs_otp_name'] ?? '';

// Handle Reset Password
if (isset($_POST['action_reset'])) {
    $id = $_POST['mhs_id'];
    
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $new_password = '';
    for ($i = 0; $i < 8; $i++) {
        $new_password .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = :pass, request_change_password = 0, is_password_changed = 0 WHERE id = :id");
    $stmt->execute([':pass' => $hashed_password, ':id' => $id]);
    
    $stmt_name = $pdo->prepare("SELECT full_name FROM users WHERE id = :id");
    $stmt_name->execute([':id' => $id]);
    $mhs_name = $stmt_name->fetchColumn();

    // 🔒 AMANKAN DI SESSION: Simpan data ke session temporer
    $_SESSION['mhs_otp_password'] = $new_password;
    $_SESSION['mhs_otp_name'] = $mhs_name;

    // 🔄 REDIRECT: Alihkan halaman ke dirinya sendiri untuk membunuh status POST browser
    header("Location: dashboard.php");
    exit();
}

// Handle Clear OTP Session (Dipicu saat klik tombol "I Have Copied It")
if (isset($_POST['action_clear_otp'])) {
    unset($_SESSION['mhs_otp_password']);
    unset($_SESSION['mhs_otp_name']);
    header("Location: dashboard.php");
    exit();
}

// Handle Hide / Unhide
if (isset($_POST['action_toggle_hide'])) {
    $id = $_POST['mhs_id'];
    $current_status = $_POST['current_hidden_status'];
    $new_status = ($current_status == 1) ? 0 : 1;
    
    $stmt = $pdo->prepare("UPDATE users SET is_hidden = :status WHERE id = :id");
    $stmt->execute([':status' => $new_status, ':id' => $id]);
    header("Location: dashboard.php");
    exit();
}

$students = $pdo->query("SELECT * FROM users ORDER BY nim ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Admin Dashboard - Informatika'23</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] antialiased min-h-screen flex flex-col pt-32 pb-12">

    <div id="nav-container" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div id="nav-overlay" class="absolute inset-0 z-[-1]" style="background: linear-gradient(to bottom, rgba(11, 23, 39, 0.6) 0%, rgba(11, 23, 39, 0.4) 0%, transparent 100%); height: 150%;"></div>
        <div class="max-w-7xl mx-auto px-4 md:px-8 w-full pt-4">
            <header class="bg-white/80 border-gray-100 mt-4 py-3 px-6 md:px-8 rounded-full flex justify-between items-center border backdrop-blur-sm shadow-sm">
                <div class="flex items-center gap-8">
                    <span class="text-xl font-bold text-[#0d8276] select-none">Informatika<span class="text-gray-900">'23</span></span>
                </div>
                <div class="flex items-center gap-4 text-sm font-medium">
                    <span class="hidden md:inline-block text-gray-400 text-xs font-bold tracking-wider uppercase border-r border-gray-200 pr-4">Admin: <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Root'); ?></span>
                    <a href="logout.php" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2 px-4 rounded-full text-xs tracking-wider uppercase transition flex items-center gap-1.5 border border-red-100">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sign Out
                    </a>
                </div>
            </header>
        </div>
    </div>

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 md:px-8 mt-8">
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-[#0d8276] rounded-full -mr-10 -mt-10 opacity-10 blur-3xl"></div>
            
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-6 relative z-10">Student Database Control Center</h1>
            
            <div class="overflow-x-auto rounded-2xl border border-gray-100 relative z-10">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-gray-50/70 text-gray-500 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                            <th class="p-4">Nama</th>
                            <th class="p-4">NIM</th>
                            <th class="p-4 text-center">Verified</th>
                            <th class="p-4 text-center">Views</th>
                            <th class="p-4">Last Update</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php foreach ($students as $row): ?>
                        <tr class="<?= $row['is_hidden'] ? 'bg-gray-50/70 opacity-60' : 'hover:bg-gray-50/30 transition' ?>">
                            <td class="p-4 font-semibold text-gray-900"><?= htmlspecialchars($row['full_name']) ?></td>
                            <td class="p-4 text-gray-600 font-mono text-xs"><?= htmlspecialchars($row['nim']) ?></td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold <?= $row['is_password_changed'] ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-amber-50 text-amber-700 border border-amber-100' ?>">
                                    <?= $row['is_password_changed'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td class="p-4 text-center font-semibold text-gray-700"><?= number_format($row['views_count']) ?></td>
                            <td class="p-4 text-gray-400 text-xs font-medium"><?= $row['updated_at'] ?></td>
                            <td class="p-4 text-center">
                                <?php if ($row['request_change_password'] == 1): ?>
                                    <span class="bg-red-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full animate-pulse tracking-wide">REQ RESET</span>
                                <?php else: ?>
                                    <span class="text-gray-300 font-mono">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="openDetailModal(<?= htmlspecialchars(json_encode($row)) ?>)" title="View Profile Detail" class="p-2 bg-gray-50 hover:bg-[#0d8276]/10 text-gray-600 hover:text-[#0d8276] rounded-xl transition border border-gray-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    
                                    <form method="POST" onsubmit="return confirm('Generate random password untuk <?= addslashes($row['full_name']) ?>?')">
                                        <input type="hidden" name="mhs_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="action_reset" title="Reset/Change Password" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-xl transition border border-amber-100">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        </button>
                                    </form>

                                    <form method="POST">
                                        <input type="hidden" name="mhs_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="current_hidden_status" value="<?= $row['is_hidden'] ?>">
                                        <button type="submit" name="action_toggle_hide" title="<?= $row['is_hidden'] ? 'Unhide Website' : 'Hide Website' ?>" class="p-2 rounded-xl transition border <?= $row['is_hidden'] ? 'bg-green-50 text-green-600 border-green-100 hover:bg-green-100' : 'bg-red-50 text-red-600 border-red-100 hover:bg-red-100' ?>">
                                            <?php if($row['is_hidden']): ?>
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 10v2m-6-8h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>
                                            <?php else: ?>
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php if (!empty($generated_password)): ?>
    <div id="otp-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-gray-100 relative overflow-hidden">
            
            <div class="w-12 h-12 bg-amber-50 border border-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900 mb-1">Temporary Password Generated</h2>
            <p class="text-xs text-gray-400 mb-4">For: <span class="font-bold text-gray-700"><?= htmlspecialchars($target_mhs_name) ?></span></p>
            
            <div class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-2xl py-3 px-4 mb-4 gap-2">
                <div id="password-text" class="font-mono text-2xl font-bold tracking-widest text-[#0d8276] select-all">
                    <?= $generated_password ?>
                </div>
                <button onclick="copyPasswordToClipboard()" id="btn-copy-otp" title="Copy Password" class="p-2 bg-white hover:bg-gray-100 border border-gray-200 text-gray-500 hover:text-[#0d8276] rounded-xl transition shadow-sm shrink-0">
                    <svg id="icon-copy" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                    <svg id="icon-check" class="h-4 w-4 text-green-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
            
            <p class="text-[10px] text-red-500 mb-6 leading-tight">⚠️ Copy password ini sekarang! Ketika modal ditutup atau di-refresh, plain-text password akan dihancurkan dari memori.</p>
            
            <form method="POST">
                <button type="submit" name="action_clear_otp" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3.5 rounded-xl text-sm transition shadow-md">
                    I Have Copied It
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div id="detail-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden justify-center p-4 overflow-y-auto">
    
        <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden relative max-w-2xl w-full my-8 md:my-auto">
            <div class="absolute top-0 right-0 w-40 h-40 bg-[#0d8276] rounded-full -mr-10 -mt-10 opacity-20 blur-3xl"></div>
            
            <div class="p-6 md:p-10 relative z-10">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Student Profile</h2>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex flex-col items-center mb-8">
                    <div class="w-24 h-24 rounded-full overflow-hidden ring-4 ring-gray-100 bg-gray-50">
                        <img id="mhs-modal-avatar" src="" class="w-full h-full object-cover" alt="Profile">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50/60 p-4 rounded-2xl border border-gray-100/70">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Full Name</label>
                        <p id="mhs-modal-name" class="text-gray-900 font-semibold"></p>
                    </div>
                    <div class="bg-gray-50/60 p-4 rounded-2xl border border-gray-100/70">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Student ID (NIM)</label>
                        <p id="mhs-modal-nim" class="text-gray-900 font-mono font-semibold"></p>
                    </div>
                </div>

                <div class="mb-6 bg-gray-50/60 p-4 rounded-2xl border border-gray-100/70">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Short Bio</label>
                    <p id="mhs-modal-bio" class="text-sm text-gray-600 leading-relaxed italic"></p>
                </div>

                <hr class="border-gray-100 mb-6">

                <div class="space-y-5">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Website Title</label>
                        <p id="mhs-modal-title" class="text-sm font-semibold text-gray-800 bg-gray-50/30 px-4 py-3 rounded-xl border border-gray-100"></p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Website URL</label>
                        <div class="flex items-center gap-2 bg-gray-50/30 px-4 py-3 rounded-xl border border-gray-100 text-sm text-[#0d8276] font-medium break-all">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.826a4 4 0 015.656 0l4 4a4 4 0 01-5.656 5.656l-1.1-1.1" /></svg>
                            <a id="mhs-modal-link" href="" target="_blank" class="hover:underline"></a>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Description Overview</label>
                        <div id="mhs-modal-desc" class="text-sm text-gray-600 bg-gray-50/30 px-4 py-4 rounded-2xl border border-gray-100 leading-relaxed whitespace-pre-line max-h-40 overflow-y-auto"></div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Project Thumbnail Preview</label>
                        <div class="border border-gray-100 rounded-2xl p-2 bg-gray-50/50">
                            <img id="mhs-modal-thumb" src="" class="w-full max-h-48 object-cover rounded-xl" alt="Thumbnail Project">
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button onclick="closeDetailModal()" class="w-full bg-[#eef2ff] hover:bg-indigo-100 text-gray-600 font-bold py-3.5 rounded-2xl transition text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('detail-modal');

        function openDetailModal(data) {
            // Render Avatar
            document.getElementById('mhs-modal-avatar').src = data.profile_photo && data.profile_photo.trim() !== '' 
                ? '../' + data.profile_photo 
                : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.full_name) + '&background=f1f5f9&color=0d8276';
            
            // Render Text Biasa
            document.getElementById('mhs-modal-name').innerText = data.full_name;
            document.getElementById('mhs-modal-nim').innerText = data.nim;
            document.getElementById('mhs-modal-bio').innerText = data.bio && data.bio.trim() !== '' ? '"' + data.bio + '"' : 'Belum mengonfigurasi bio personal.';
            document.getElementById('mhs-modal-title').innerText = data.website_title && data.website_title.trim() !== '' ? data.website_title : 'Untitled Project';
            
            // Render URL Link
            const linkTag = document.getElementById('mhs-modal-link');
            if(data.website_link && data.website_link.trim() !== '') {
                linkTag.href = data.website_link;
                linkTag.innerText = data.website_link;
            } else {
                linkTag.href = '#';
                linkTag.innerText = 'No External Link Available';
            }

            document.getElementById('mhs-modal-desc').innerText = data.description && data.description.trim() !== '' ? data.description : 'Mahasiswa belum mengonstruksi ringkasan deskripsi web proyek.';
            
            // Render Thumbnail
            const thumbTag = document.getElementById('mhs-modal-thumb');
            thumbTag.src = data.thumbnail && data.thumbnail.trim() !== '' 
                ? '../' + data.thumbnail 
                : 'https://placehold.co/600x300/f1f5f9/a1a1aa?text=No+Thumbnail+Asset';

            // Show Modal Container
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // Lock main scrollbar
        }

        function closeDetailModal() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore main scrollbar
        }

        // Close modal when wrapper layer outside container gets clicked
        window.onclick = function(event) {
            if (event.target == modal) {
                closeDetailModal();
            }
        }

        function copyPasswordToClipboard() {
            // Ambil teks password dari elemen HTML
            const passwordText = document.getElementById('password-text').innerText.trim();
            
            // Gunakan Clipboard API bawaan browser modern
            navigator.clipboard.writeText(passwordText).then(() => {
                const iconCopy = document.getElementById('icon-copy');
                const iconCheck = document.getElementById('icon-check');
                const btnCopy = document.getElementById('btn-copy-otp');

                // Ubah ikon menjadi tanda centang hijau (Success Feedback)
                iconCopy.classList.add('hidden');
                iconCheck.classList.remove('hidden');
                btnCopy.classList.add('bg-green-50', 'border-green-200');

                // Kembalikan ikon semula setelah 2 detik
                setTimeout(() => {
                    iconCopy.classList.remove('hidden');
                    iconCheck.classList.add('hidden');
                    btnCopy.classList.remove('bg-green-50', 'border-green-200');
                }, 2000);
            }).catch(err => {
                alert('Gagal menyalin password: ', err);
            });
        }
    </script>
</body>
</html>