<?php
session_start();
include 'koneksi.php';

// --- FITUR AUTO-LOGOUT ---
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    session_start();
}

$error_msg = "";

if (isset($_POST['login'])) {
    // 1. AMBIL INPUT & BERSIHKAN (PENTING: Pakai trim, JANGAN pakai real_escape_string)
    $login_input = trim($_POST['login_input']); 
    $password    = $_POST['password'];

    // 2. PREPARED STATEMENT (Security Level: Bank)
    // Cek apakah email ATAU username cocok
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    
    // Binding (s = string)
    $stmt->bind_param("ss", $login_input, $login_input);
    
    // Eksekusi
    $stmt->execute();
    
    // Ambil Hasil
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // 3. VERIFIKASI PASSWORD
        if (password_verify($password, $row['password'])) {
            // Login Sukses
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            
            header("Location: welcome.php");
            exit();
        } else {
            $error_msg = "Wrong Password!";
        }
    } else {
        // User Gak Ketemu
        // DEBUGGING (Hapus baris echo ini kalau sudah fix)
        // echo "Input yang dicari: " . htmlspecialchars($login_input); 
        
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
        <a href="index.php" class="logo">
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