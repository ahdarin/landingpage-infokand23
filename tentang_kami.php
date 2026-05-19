<?php
require_once 'landingPageResources/config/koneksi.php';

try {
    // Menghitung total views dari semua user
    $stmt_views = $pdo->query("SELECT SUM(views_count) as total_views FROM users");
    $row_views = $stmt_views->fetch();
    $total_views = $row_views['total_views'] ?? 0;

    // Format angka (misal 1200 jadi 1.2k)
    if ($total_views >= 1000) {
        $formatted_views = round($total_views / 1000, 1) . 'k';
    } else {
        $formatted_views = $total_views;
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="landingPageResources/assets/img/LOGO IF.png" type="image/png">
    <title>Informatika'23 | Tentang Kami</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }

        @keyframes scroll-smooth {
            0% {
                transform: translateX(0);
            }
            100% {
                /* Geser tepat separuh dari total lebar (karena kita punya 2 blok identik) */
                transform: translateX(-50%);
            }
        }

        .animate-marquee-smooth {
            display: flex;
            animation: scroll-smooth 30s linear infinite;
        }

        .min-w-max {
            width: max-content;
        }
    </style>
</head>
<body class="bg-[#f8fafc] antialiased min-h-screen flex flex-col">

    <?php include 'landingPageResources/components/navbar.php'; ?>

    <main class="flex-grow w-full pt-32 pb-20">
        <div class="max-w-5xl mx-auto px-4 md:px-8">
            
            <div class="bg-gradient-to-br from-white to-gray-100 rounded-[2.5rem] p-8 md:p-12 shadow-sm border border-white flex flex-col md:flex-row items-center md:items-start gap-8 md:gap-12 relative overflow-hidden">
                
                <img src="landingPageResources/assets/img/kicau.gif" 
                    alt="kicau" 
                    class="hidden md:block absolute bottom-4 right-8 w-12 h-12 object-contain opacity-80 pointer-events-none">

                <div class="absolute -right-20 -top-20 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="w-40 h-40 md:w-48 md:h-48 rounded-full border-[6px] border-white shadow-xl overflow-hidden flex-shrink-0 z-10 bg-white flex items-center justify-center p-6">
                    <img src="landingPageResources/assets/img/LOGO IF-02.png" alt="Logo Informatika" class="w-full h-full object-contain">
                </div>

                <div class="flex flex-col items-center md:items-start text-center md:text-left z-10 pt-2">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 tracking-tight">Informatika'23</h1>
                    <p class="text-gray-600 text-lg mb-8 max-w-xl leading-relaxed">
                        Wadah kreativitas dan inovasi mahasiswa Informatika Universitas Andalas angkatan 2023. Kami berdedikasi untuk terus bereksplorasi dalam dunia teknologi dan membangun masa depan digital yang lebih baik.
                    </p>

                    <div class="bg-white rounded-3xl px-8 py-4 shadow-sm border border-gray-100 inline-flex items-center divide-x divide-gray-200">
                        <div class="flex flex-col items-center px-6">
                            <span class="text-[#0d8276] font-bold text-2xl leading-none mb-1">42</span>
                            <span class="text-[9px] font-bold text-gray-400 tracking-widest uppercase">Members</span>
                        </div>
                        <div class="flex flex-col items-center px-6">
                            <span class="text-[#0d8276] font-bold text-2xl leading-none mb-1"><?= $formatted_views; ?></span>
                            <span class="text-[9px] font-bold text-gray-400 tracking-widest uppercase">Total Views</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-12 text-gray-600 leading-relaxed">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Beyond the Code</h2>
                    <p>We believe that Informatics is more than just writing syntax or building complex algorithms. It is the art of turning abstract ideas into real, interactive digital solutions that bring a meaningful impact to the world around us.</p>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">The Digital Atelier</h2>
                    <p>This is our creative sanctuary. A living archive that captures our journey, our learning process, and the final results of our best projects as we build the technology of tomorrow.</p>
                </div>
            </div>

        </div>
    </main>

    <div class="w-full bg-[#0d8276] py-3 overflow-hidden border-y border-white/10 mt-20 relative">
        <div class="flex min-w-max animate-marquee-smooth">
            
            <div class="flex items-center text-white font-bold text-xs md:text-sm tracking-[0.2em] uppercase py-1">
                <span>&nbsp;✦ KAELA ✦ LARA ✦ AHDA ✦ ALIFFIA ✦ DEVIA ✦ NIA ✦ NUGI ✦ WAHYU ✦ CHATGPT ✦ EZZA ✦ IMBANG ✦ DILA ✦ JIBOY ✦ KEVIN ✦ HAFIZ ✦ GALID ✦ DAWI ✦ GEMINI ✦ RIFKI ✦ GHAZI ✦ RAHUL ✦ REYNARD ✦ BUCEL ✦ INDAH ✦ SHERLY ✦ FARHAN ✦ PERPLEXITY ✦ FAWWAZ ✦ LUTHFI ✦ STANLEY ✦ CLAUDE ✦ PIA ✦ SURDIK ✦ FARREL ✦ FIKHRI ✦ REZA ✦ IRFAN ✦ IRGI ✦ COPILOT ✦ FATHUR ✦ AMIN ✦ FARIZ ✦ RAFKI ✦ GEPE ✦ GROK ✦ RIDHO ✦ FAYI&nbsp;</span>
            </div>

            <div class="flex items-center text-white font-bold text-xs md:text-sm tracking-[0.2em] uppercase py-1">
                <span>&nbsp;✦ KAELA ✦ LARA ✦ AHDA ✦ ALIFFIA ✦ DEVIA ✦ NIA ✦ NUGI ✦ WAHYU ✦ CHATGPT ✦ EZZA ✦ IMBANG ✦ DILA ✦ JIBOY ✦ KEVIN ✦ HAFIZ ✦ GALID ✦ DAWI ✦ GEMINI ✦ RIFKI ✦ GHAZI ✦ RAHUL ✦ REYNARD ✦ BUCEL ✦ INDAH ✦ SHERLY ✦ FARHAN ✦ PERPLEXITY ✦ FAWWAZ ✦ LUTHFI ✦ STANLEY ✦ CLAUDE ✦ PIA ✦ SURDIK ✦ FARREL ✦ FIKHRI ✦ REZA ✦ IRFAN ✦ IRGI ✦ COPILOT ✦ FATHUR ✦ AMIN ✦ FARIZ ✦ RAFKI ✦ GEPE ✦ GROK ✦ RIDHO ✦ FAYI&nbsp;</span>
            </div>

        </div>
    </div>
    <?php include 'landingPageResources/components/footer.php'; ?>

</body>
</html>