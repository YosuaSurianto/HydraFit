<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HydraFit - From Fat To Fit</title>
    
    <!-- 1. GOOGLE FONTS (POPPINS) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- 2. CSS LOKAL -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Canvas untuk Background Animasi -->
    <canvas id="particles-canvas"></canvas>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <!-- Icon Jantung (SVG Manual - Tanpa CDN) -->
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
            <span>HydraFit</span>
        </div>

        <div class="nav-buttons">
            <!-- Tombol Navigasi -->
            <a href="login.php" class="btn-outline">Login</a>
            <a href="register.php" class="btn-outline">Sign Up</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="hero-container">
        
        <!-- Konten Kiri: Teks -->
        <div class="hero-text fade-in">
            <h1>
                Hi There, <br>
                Welcome to <span class="text-gradient">HydraFit</span>
            </h1>
            
            <h2 class="subtitle">
                We Help You Go <span class="text-red">From Fat</span> To <span class="text-green">Fit</span>
            </h2>

            <p class="description">
                Discover a personalized weight-management plan designed to fit your lifestyle. Track your progress, stay motivated, and build healthier habits step by step — all in one platform.
            </p>

            <div class="cta-group">
                <a href="register.php" class="btn-primary btn-large" id="btnStart"> Workout Now 
                    <!-- Icon Panah Kanan -->
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px;">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                
                <!-- Social Media Icons -->
                <div class="socials">
                    <a href="#" class="icon-box">
                        <!-- Instagram Icon -->
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </a>
                    <a href="#" class="icon-box">
                        <!-- Twitter Icon -->
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Konten Kanan: Gambar -->
        <div class="hero-image fade-in delay">
            <div class="circle-container">
                <!-- Gambar di lingkaran -->
                <img src="assets/image/Fit.jpg" alt="Fit">
            </div>
            
            <!-- Hiasan Blur (Glow Effect) -->
            <div class="blur-blob blue"></div>
            <div class="blur-blob purple"></div>
        </div>

    </main>

    <!-- JS LOKAL -->
    <script src="assets/js/script.js"></script>
</body>
</html>