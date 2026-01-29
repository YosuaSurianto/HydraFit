<?php
session_start();
include 'koneksi.php';

// CEK KEAMANAN
if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

// PROSES UPDATE DATA
if (isset($_POST['finish_setup'])) {
    $user_id = $_SESSION['user_id'];
    
    // Ambil & Bersihkan Data
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $gender     = mysqli_real_escape_string($conn, $_POST['gender']);
    $blood_type = mysqli_real_escape_string($conn, $_POST['blood_type']);
    $height     = mysqli_real_escape_string($conn, $_POST['height']);
    $weight     = mysqli_real_escape_string($conn, $_POST['weight']);

    // Validasi Sederhana
    if ($height < 50 || $weight < 10) {
        $error_msg = "Please enter valid height and weight.";
    } else {
        // Update Data User
        $query_update = "UPDATE users SET 
                         birth_date = '$birth_date', 
                         gender = '$gender', 
                         blood_type = '$blood_type', 
                         height = '$height', 
                         current_weight = '$weight' 
                         WHERE id = '$user_id'";

        if (mysqli_query($conn, $query_update)) {
            // Catat History Awal
            $query_history = "INSERT INTO weight_tracking (user_id, weight, recorded_at) 
                              VALUES ('$user_id', '$weight', NOW())";
            mysqli_query($conn, $query_history); 

            // Sukses -> ke Welcome
            header("Location: welcome.php");
            exit();
        } else {
            $error_msg = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/onboarding.css">
</head>
<body class="auth-body">

    <nav class="auth-navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <span>HydraFit</span>
        </a>
    </nav>

    <div class="auth-container">
<div class="auth-card fade-in card-step-3">
            
            <a href="create-profile.php" class="back-icon" title="Go Back">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>

            <div class="onboarding-header">
                <h2 class="auth-title">Physical Data</h2>
                <p class="step-indicator">Step 3 of 3</p>
            </div>

            <?php if(isset($error_msg)): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 6px; font-size: 0.9rem; text-align: center; margin-bottom: 15px;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                
                <div class="input-group">
                    <label>Date of Birth</label>
                    <input type="date" name="birth_date" required>
                </div>

                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Blood Type</label>
                    <select name="blood_type" required>
                        <option value="" disabled selected>Select Blood Type</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Weight (kg)</label>
                    <input type="number" name="weight" placeholder="e.g. 65" step="0.1" min="20" max="300" required>
                </div>

                <div class="input-group">
                    <label>Height (cm)</label>
                    <input type="number" name="height" placeholder="e.g. 175" min="50" max="300" required>
                </div>

                <button type="submit" name="finish_setup" class="btn-finish-full">Complete Setup</button>

            </form>
        </div>
    </div>

</body>
</html>