<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN: Apakah user sudah login/register?
// Kalau user_id kosong, berarti dia nyelonong masuk tanpa lewat register.php
if (!isset($_SESSION['user_id'])) {
    header("Location: register.php"); 
    exit();
}

// 2. PROSES UPDATE DATA SAAT TOMBOL DITEKAN
if (isset($_POST['save_profile'])) {
    $user_id = $_SESSION['user_id'];
    
    // Ambil data dari input (pakai atribut 'name', bukan 'id')
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);

    // Update database: isi kolom first_name dan last_name
    $query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name' WHERE id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        // BERHASIL: Lempar ke Step 3 (Data Fisik)
        header("Location: complete-profile.php");
        exit();
    } else {
        $error_msg = "Gagal menyimpan data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile - HydraFit</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/onboarding.css">
</head>
<body class="auth-body">

    <nav class="auth-navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
            <span>HydraFit</span>
        </a>
    </nav>

    <div class="auth-container">
        <div class="auth-card fade-in">
            
            <div class="onboarding-header">
                <h2 class="auth-title">Create a New Profile</h2>
                <p class="step-indicator">Step 2 of 3</p>
            </div>

            <?php if(isset($error_msg)): ?>
                <p style="color: red; text-align: center; margin-bottom: 10px;"><?php echo $error_msg; ?></p>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                
                <div class="input-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" id="firstName" placeholder="Enter first name" required>
                </div>

                <div class="input-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" id="lastName" placeholder="Enter last name" required>
                </div>

                <button type="submit" name="save_profile" class="btn-next">Next</button>

                <div class="onboarding-footer">
                    <a href="terms.php" target="_blank">Terms & Conditions</a>
                    <span>•</span>
                    <a href="privacy.php" target="_blank">Privacy Policy</a>
                </div>

            </form>
            
        </div>
    </div>

    </body>
</html>