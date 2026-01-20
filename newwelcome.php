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

// Tentukan nama panggilan
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
    <link rel="stylesheet" href="assets/css/onboarding.css?v=3">
    
    <style>
        .trust-box {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 20px 0;
            text-align: left;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 10px;
        }
        .trust-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .trust-icon.blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
        .trust-icon.green { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .trust-icon.purple { background: rgba(147, 51, 234, 0.1); color: #9333ea; }
        
        .trust-text strong { display: block; font-size: 0.9rem; color: #1e293b; }
        .trust-text p { font-size: 0.8rem; color: #64748b; margin: 0; }

        /* Judul Welcome */
        .welcome-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
        .welcome-subtitle { color: #64748b; font-size: 0.9rem; margin-bottom: 20px; }
    </style>
</head>
<body class="auth-body">

    <nav class="auth-navbar">
        <a href="#" class="logo">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <span>HydraFit</span>
        </a>
    </nav>

    <div class="auth-container">
        <div class="auth-card fade-in" style="position: relative; padding-top: 50px;">
            
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
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <div class="trust-text">
                        <strong>Track Progress</strong>
                        <p>Monitor weight changes daily.</p>
                    </div>
                </div>

                <div class="trust-item">
                    <div class="trust-icon purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="trust-text">
                        <strong>Real-time BMI</strong>
                        <p>Instant health analysis.</p>
                    </div>
                </div>
            </div>

            <a href="dashboard.php" class="btn-next" style="display: block; text-decoration: none; text-align: center;">
                Get Started 🚀
            </a>

        </div>
    </div>

</body>
</html>