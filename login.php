<?php
session_start();
include 'koneksi.php';

// --- FITUR AUTO-LOGOUT ---
// Jika user iseng balik ke halaman login saat masih login,
// kita ANGGAP dia mau logout. Kita hancurkan session lamanya.
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    session_start(); // Mulai session baru yang bersih
}

$error_msg = "";

if (isset($_POST['login'])) {
    // Ambil inputan user (bisa email, bisa username)
    $login_input = mysqli_real_escape_string($conn, $_POST['login_input']);
    $password    = $_POST['password'];

    // Query Cerdas: Cari di kolom email ATAU username
    $query = "SELECT * FROM users WHERE email = '$login_input' OR username = '$login_input'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Cek Password (Hash)
        if (password_verify($password, $row['password'])) {
            // Login Sukses! 
            
            // TIPS TAMBAHAN: Kita perbarui ID session biar lebih aman (Security Best Practice)
            session_regenerate_id(true);

            // Simpan ID user yang BARU LOGIN ke session
            $_SESSION['user_id'] = $row['id'];
            
            // Lempar ke Welcome Page
            header("Location: welcome.php");
            exit();
        } else {
            // Password Salah -> Muncul Error (Sesuai requestmu)
            $error_msg = "Wrong Password!";
        }
    } else {
        // User Gak Ketemu -> Muncul Error
        $error_msg = "Username or Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
    
    <link rel="stylesheet" href="assets/css/onboarding.css?v=3">

    <style>
        .auth-card {
            max-width: 450px; 
            margin: 40px auto;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #64748b;
        }
        .register-link a {
            color: #06b6d4;
            font-weight: 600;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
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
        <div class="auth-card fade-in">
            
            <div class="onboarding-header">
                <h2 class="auth-title">Welcome Back! 👋</h2>
                <p class="step-indicator">Please login to continue</p>
            </div>

            <?php if($error_msg): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 10px; font-size: 0.9rem; text-align: center; margin-bottom: 20px;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                
                <div class="input-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #334155; font-weight: 500;">Email or Username</label>
                    <input type="text" name="login_input" placeholder="Enter email or username" required>
                </div>

                <div class="input-group" style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 8px; color: #334155; font-weight: 500;">Password</label>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>

                <div style="text-align: right; margin-bottom: 25px;">
                    <a href="#" style="font-size: 0.85rem; color: #64748b; text-decoration: none;">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="btn-next">Log In</button>

                <div class="register-link">
                    Don't have an account? <a href="register.php">Sign Up</a>
                </div>

            </form>
        </div>
    </div>

</body>
</html>