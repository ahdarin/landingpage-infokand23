<?php
// edit_profil.php
require_once 'landingPageResources/config/koneksi.php';
require_once 'landingPageResources/config/auth_check.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

$your_website = null;
if (isset($_SESSION['user_id'])) {
    $stmt_mine = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt_mine->execute([':id' => $_SESSION['user_id']]);
    $your_website = $stmt_mine->fetch();
}

// 1. Ambil data pengguna
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();

// 2. Logic Skip
if (isset($_POST['skip'])) {
    header("Location: proyek.php");
    exit();
}

function uploadAndCompressToWebP($file_source, $target_path, $quality = 75, $max_width = 1200) {
    $info = getimagesize($file_source);
    if (!$info) return false;
    switch ($info['mime']) {
        case 'image/jpeg': $image = imagecreatefromjpeg($file_source); break;
        case 'image/png': $image = imagecreatefrompng($file_source); break;
        case 'image/webp': $image = imagecreatefromwebp($file_source); break;
        default: return false;
    }
    $orig_width = imagesx($image);
    $orig_height = imagesy($image);
    if ($orig_width > $max_width) {
        $new_width = $max_width;
        $new_height = floor($orig_height * ($max_width / $orig_width));
        $truecolor = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($truecolor, false);
        imagesavealpha($truecolor, true);
        imagecopyresampled($truecolor, $image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
        imagedestroy($image);
        $image = $truecolor;
    }
    $res = imagewebp($image, $target_path, $quality);
    imagedestroy($image);
    return $res;
}

// 3. Proses Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $bio = trim($_POST['bio']);
    $website_title = trim($_POST['website_title']);
    $website_link = trim($_POST['website_link']);
    $description = trim($_POST['description']);
    $profile_photo = $user['profile_photo'];
    $thumbnail = $user['thumbnail'];

    $upload_dir = 'landingPageResources/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    // Upload Profile Photo
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $new_name = 'profile_' . $user_id . '_' . time() . '.webp';
        $target_path = $upload_dir . $new_name;
        if (uploadAndCompressToWebP($_FILES['profile_photo']['tmp_name'], $target_path, 80, 400)) {
            if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])) unlink($user['profile_photo']);
            $profile_photo = $target_path;
        }
    }

    // Upload Thumbnail
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $new_name = 'thumb_' . $user_id . '_' . time() . '.webp';
        $target_path = $upload_dir . $new_name;
        if (uploadAndCompressToWebP($_FILES['thumbnail']['tmp_name'], $target_path, 75, 1200)) {
            if (!empty($user['thumbnail']) && file_exists($user['thumbnail'])) unlink($user['thumbnail']);
            $thumbnail = $target_path;
        }
    }

    try {
        $update_stmt = $pdo->prepare("UPDATE users SET bio = :bio, website_title = :website_title, website_link = :website_link, description = :description, profile_photo = :profile_photo, thumbnail = :thumbnail WHERE id = :id");
        $update_stmt->execute([
            ':bio' => $bio, ':website_title' => $website_title, ':website_link' => $website_link,
            ':description' => $description, ':profile_photo' => $profile_photo, ':thumbnail' => $thumbnail, ':id' => $user_id
        ]);
        $success = "Profil berhasil diperbarui!";
        header("refresh:1; url=proyek.php");
    } catch (PDOException $e) { $error = "Gagal memperbarui data."; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Edit Profile - Informatika'23</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 antialiased min-h-screen flex flex-col">

    <a href="detail.php?slug=<?= $your_website['slug']; ?>" class="fixed top-8 left-8 z-50 flex items-center justify-center w-12 h-12 text-[#0d8276] transition-all group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>

    <main class="flex-grow py-12 px-4">
        <div class="max-w-2xl mx-auto">
            
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
                <p class="text-gray-500 mt-1">Update your personal information and website details</p>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-40 h-40 bg-[#0d8276] rounded-full -mr-10 -mt-10 opacity-45 blur-3xl"></div>
                
                <form action="edit_profil.php" method="POST" enctype="multipart/form-data" class="p-8 md:p-12 relative z-10">
                    
                    <div class="flex flex-col items-center mb-10">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-full overflow-hidden ring-4 ring-gray-50">
                                <img id="preview_profile" src="<?= !empty($user['profile_photo']) ? $user['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($user['full_name']).'&background=f1f5f9&color=0d8276'; ?>" 
                                     class="w-full h-full object-cover" alt="Profile">
                            </div>
                            <label for="profile_photo" class="absolute bottom-0 right-0 bg-[#0d8276] text-white p-2 rounded-full cursor-pointer shadow-lg hover:bg-[#0a6b61] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </label>
                            <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*" onchange="previewImg(this, 'preview_profile')">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Full Name</label>
                            <p class="text-gray-900 font-semibold"><?= htmlspecialchars($user['full_name']); ?></p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Student ID (NIM)</label>
                            <p class="text-gray-900 font-semibold"><?= htmlspecialchars($user['nim']); ?></p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block ml-1">Short Bio</label>
                        <textarea name="bio" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d8276]/20 focus:border-[#0d8276] transition" 
                                  placeholder="Tell us a bit about yourself..."><?= htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>

                    <hr class="border-gray-100 mb-8">

                    <div class="space-y-6">
                        <div>
                            <label class="text-sm font-bold text-gray-800 mb-2 block">Website URL</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.826a4 4 0 015.656 0l4 4a4 4 0 01-5.656 5.656l-1.1-1.1" /></svg>
                                </span>
                                <input type="url" name="website_link" value="<?= htmlspecialchars($user['website_link'] ?? ''); ?>"
                                       class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 pl-12 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d8276]/20 focus:border-[#0d8276] transition"
                                       placeholder="https://namamu.infokand23.my.id/">
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-bold text-gray-800 mb-2 block">Website Title</label>
                            <input type="text" name="website_title" value="<?= htmlspecialchars($user['website_title'] ?? ''); ?>"
                                   class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 px-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d8276]/20 focus:border-[#0d8276] transition"
                                   placeholder="My Portfolio">
                        </div>

                        <div>
                            <label class="text-sm font-bold text-gray-800 mb-2 block">Description</label>
                            <textarea name="description" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm h-32 focus:outline-none focus:ring-2 focus:ring-[#0d8276]/20 focus:border-[#0d8276] transition"
                                      placeholder="A brief description of your site..."><?= htmlspecialchars($user['description'] ?? ''); ?></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-bold text-gray-800 mb-2 block">Thumbnail Upload</label>
                            <div class="relative border-2 border-dashed border-gray-200 rounded-[2rem] p-8 text-center hover:bg-gray-50 transition group">
                                <input type="file" name="thumbnail" id="thumbnail" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" onchange="previewImg(this, 'preview_thumb')">
                                <div id="upload_placeholder">
                                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-3 group-hover:text-[#0d8276] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                    <p class="text-sm text-gray-600"><span class="text-[#0d8276] font-bold">Upload a file</span> or drag and drop</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                                </div>
                                <img id="preview_thumb" src="<?= $user['thumbnail'] ?>" class="<?= empty($user['thumbnail']) ? 'hidden' : '' ?> mt-2 max-h-40 mx-auto rounded-lg">
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 space-y-3">
                        <button type="submit" name="save" class="w-full bg-[#0d8276] hover:bg-[#0a6b61] text-white font-bold py-4 rounded-2xl shadow-lg shadow-teal-900/20 transition transform active:scale-[0.98]">
                            Save Changes
                        </button>
                        <button type="submit" name="skip" class="w-full bg-[#eef2ff] hover:bg-indigo-100 text-gray-600 font-bold py-4 rounded-2xl transition" formnovalidate>
                            Cancel
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <?php include 'landingPageResources/components/footer.php'; ?>

    <script>
        function previewImg(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).classList.remove('hidden');
                    if(previewId === 'preview_thumb') {
                        document.getElementById('upload_placeholder').classList.add('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>