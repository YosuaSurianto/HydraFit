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

    <div class="auth-container">
        <div class="welcome-card fade-in">
            
            <a href="complete-profile.php" class="back-icon" title="Edit Profile">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            
            <h1 class="welcome-title">Welcome, <span style="color: #06b6d4;"><?php echo htmlspecialchars($display_name); ?>!</span> 🎉</h1>
            <p class="welcome-subtitle">Your profile is ready. Here's what you can do:</p>

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
            </div>

            <a href="dashboard.php" class="btn-get-started">
                Get Started 🚀
            </a>

            <div class="welcome-footer">
                <a href="#">Need Help?</a>
                <a href="#">Support</a>
            </div>

        </div>
    </div>

</body>
</html>