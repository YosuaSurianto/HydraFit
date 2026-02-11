<?php
session_start();
include 'koneksi.php';

// Variabel untuk menampung script SweetAlert (Default kosong)
$swal_script = "";

// CEK KEAMANAN: Apakah user sudah login/register?
if (!isset($_SESSION['user_id'])) {
    header("Location: register.php"); 
    exit();
}

// PROSES UPDATE DATA SAAT TOMBOL DITEKAN
if (isset($_POST['save_profile'])) {
    $user_id = $_SESSION['user_id'];
    
    // Ambil data dari input
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);

    // Update database
    $query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name' WHERE id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        // BERHASIL: Langsung Lempar ke Step 3 (Silent Success)
        header("Location: complete-profile.php");
        exit();
    } else {
        // GAGAL: Siapkan SweetAlert Error
        $sys_error = mysqli_error($conn);
        $swal_script = "
            Swal.fire({
                icon: 'error',
                title: 'System Error',
                text: 'Failed to save profile: $sys_error'
            });
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile - HydraFit</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/onboarding.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            </form>
            
        </div>
    </div>

    <script>
        <?php echo $swal_script; ?>
    </script>

</body>
</html>