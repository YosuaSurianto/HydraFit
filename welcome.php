<?php
session_start();
include 'koneksi.php';

// CEK SESSION: Wajib Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// AMBIL DATA USER DARI DATABASE
$user_id = $_SESSION['user_id'];
$query = "SELECT username, first_name FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

// Tentukan nama panggilan
$display_name = !empty($user_data['first_name']) ? $user_data['first_name'] : $user_data['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <link rel="stylesheet" href="assets/css/welcome.css">
</head>
<body class="auth-body">

    <nav class="auth-navbar">
        <a href="#" class="logo">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <span>HydraFit</span>
        </a>
        
        <div class="user-display">
            <span>Hello, <?php echo htmlspecialchars($display_name); ?></span>
            <div class="user-avatar-small">
                <?php echo strtoupper(substr($display_name, 0, 1)); ?>
            </div>
        </div>
    </nav>
<!-- Kata kata -->
    <div class="auth-container">
        <div class="welcome-card fade-in">      
            <h1 class="welcome-title">Welcome, <span style="color: #06b6d4;"><?php echo htmlspecialchars($display_name); ?>!</span> 🎉</h1>
            <p class="welcome-subtitle">You just took a big step towards managing and improving your health!</p>

            <!-- Kata kata di kotak abu abu -->
            <div class="trust-box">

                <div class="trust-item">
                    <div class="trust-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <div class="trust-text">
                        <strong>Track Progress</strong>
                        <p>Monitor weight changes daily.</p>
                    </div>
                </div>

                <div class="trust-item">
                    <div class="trust-icon purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="trust-text">
                        <strong>Real-time BMI</strong>
                        <p>Instant health analysis.</p>
                    </div>
                </div>

                <div class="trust-item">
                    <div class="trust-icon green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <div class="trust-text">
                        <strong>Data Privacy</strong>
                        <p>You control who you share with. Delete your data at any time.</p>
                    </div>
                </div>

                <div class="trust-item">
                    <div class="trust-icon purple">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </div>
                    <div class="trust-text">
                        <strong>No Ad Selling</strong>
                        <p>We do NOT sell your data. Your info stays private. No ads!</p>
                    </div>
                </div>

                <div class="trust-item">
                    <div class="trust-icon orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                    </div>
                    <div class="trust-text">
                        <strong>Secure Sync</strong>
                        <p>Sync securely across devices. Use on Web or Mobile.</p>
                    </div>
                </div>
                </div>
            <!-- Kata kata di kotak abu abu END-->


            <a href="dashboard.php"  class="btn-get-started">
                Get Started 🚀
            </a>

        </div>
    </div>

</body>
</html>