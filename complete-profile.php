<?php
session_start();
include 'koneksi.php';

// 1. CEK KEAMANAN
if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

// 2. PROSES SAAT TOMBOL FINISH DITEKAN
if (isset($_POST['finish_setup'])) {
    $user_id = $_SESSION['user_id'];
    
    // Ambil data dari form
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $gender     = mysqli_real_escape_string($conn, $_POST['gender']);
    $blood_type = mysqli_real_escape_string($conn, $_POST['blood_type']);
    $height     = mysqli_real_escape_string($conn, $_POST['height']);
    $weight     = mysqli_real_escape_string($conn, $_POST['weight']);

    // MISI 1: Update Data Profil User
    $query_update = "UPDATE users SET 
                     birth_date = '$birth_date', 
                     gender = '$gender', 
                     blood_type = '$blood_type', 
                     height = '$height', 
                     current_weight = '$weight' 
                     WHERE id = '$user_id'";

    if (mysqli_query($conn, $query_update)) {
        
        // MISI 2: Catat History Berat Badan Awal (Untuk Grafik)
        $date_now = date('Y-m-d'); // Ambil tanggal hari ini
        $query_history = "INSERT INTO weight_tracking (user_id, weight, recorded_at) 
                          VALUES ('$user_id', '$weight', '$date_now')";
        
        mysqli_query($conn, $query_history); // Eksekusi simpan history

        // SUKSES SEMUA: Lempar ke Welcome Page
        header("Location: welcome.php");
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
    <title>Complete Profile - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/onboarding.css">
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
        <div class="auth-card fade-in">
            
            <div class="onboarding-header">
                <h2 class="auth-title">Physical Data</h2>
                <p class="step-indicator">Step 3 of 3</p>
            </div>

            <?php if(isset($error_msg)): ?>
                <p style="color: red; text-align: center; margin-bottom: 10px;"><?php echo $error_msg; ?></p>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                
                <div class="input-group">
                    <label>Date of Birth</label>
                    <input type="date" name="birth_date" required>
                </div>

                <div class="form-row" style="display: flex; gap: 15px;">
                    <div class="input-group" style="flex: 1;">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="" disabled selected>Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label>Blood Type</label>
                        <select name="blood_type" required>
                            <option value="" disabled selected>Select</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" style="display: flex; gap: 15px;">
                    <div class="input-group" style="flex: 1;">
                        <label>Weight (kg)</label>
                        <input type="number" name="weight" placeholder="0" step="0.1" required>
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label>Height (cm)</label>
                        <input type="number" name="height" placeholder="0" required>
                    </div>
                </div>

                <button type="submit" name="finish_setup" class="btn-primary-full" style="width: 100%; margin-top: 10px;">Complete Setup</button>

            </form>
        </div>
    </div>
    
    </body>
</html>