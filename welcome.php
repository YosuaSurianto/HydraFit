<?php
session_start();
include 'koneksi.php';

// 1. CEK SESSION: Wajib Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. AMBIL DATA USER DARI DATABASE
$user_id = $_SESSION['user_id'];
$query = "SELECT username, first_name FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

// Tentukan nama panggilan (Kalau First Name ada, pakai itu. Kalau kosong, pakai Username)
$display_name = !empty($user_data['first_name']) ? $user_data['first_name'] : $user_data['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/welcome.css">
</head>
<body style="background-color: #f8fafc; display: flex; flex-direction: column; align-items: center; min-height: 100vh;">

    <nav style="width: 100%; background: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <a href="#" class="logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #1e293b; font-weight: 800; font-size: 1.2rem;">
            <div class="logo-icon" style="background-color: #2563eb; color: white; padding: 5px; border-radius: 6px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <span>HydraFit</span>
        </a>

        <div class="user-display">
            <span>Hello, <?php echo htmlspecialchars($display_name); ?></span>
            <div class="user-avatar-small">
                <?php echo strtoupper(substr($display_name, 0, 1)); // Ambil huruf pertama ?>
            </div>
        </div>
    </nav>

    <div class="welcome-card fade-in">
        
        <h1 class="welcome-title">Welcome to HydraFit, <span style="color: #2563eb;"><?php echo htmlspecialchars($display_name); ?>!</span> 🎉</h1>
        
        <p class="welcome-subtitle">
            Your account has been successfully created. You are now ready to start your journey towards a healthier life.
        </p>

        <div class="trust-box">
            
            <div class="trust-item">
                <div class="trust-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div class="trust-text">
                    <strong>Track Progress</strong>
                    <p>Monitor your weight changes daily with interactive charts.</p>
                </div>
            </div>

            <div class="trust-item">
                <div class="trust-icon green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="trust-text">
                    <strong>Secure Data</strong>
                    <p>Your physical data is stored securely in our system.</p>
                </div>
            </div>

            <div class="trust-item">
                <div class="trust-icon purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="trust-text">
                    <strong>Real-time Analysis</strong>
                    <p>Get instant BMI calculation based on your profile.</p>
                </div>
            </div>

        </div>

        <a href="dashboard.php" class="btn-get-started" id="btnGetStarted" style="display: block; text-decoration: none; text-align: center;">
            GET STARTED 🚀
        </a>

        <div class="welcome-footer">
            <a href="#">Need Help?</a>
            <a href="#">Support</a>
        </div>

    </div>

    </body>
</html>