<?php
session_start();
include 'koneksi.php';

// --- FITUR AUTO-LOGOUT ---
// Jika user kembali ke halaman login saat masih login, kita logout-kan dulu
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    session_start();
}

$error_msg = "";

if (isset($_POST['login'])) {
    // AMBIL INPUT & BERSIHKAN
    $login_input = trim($_POST['login_input']);
    $password    = $_POST['password'];

    // PREPARED STATEMENT (Cek Email atau Username)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $login_input, $login_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // VERIFIKASI PASSWORD
        if (password_verify($password, $row['password'])) {
            // --- LOGIN SUKSES ---
            session_regenerate_id(true);

            // Simpan ID dan Role ke Session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role']; // <--- PENTING: Simpan Role!

            // --- TRAFFIC CONTROLLER (PENGATUR LALU LINTAS) ---
            if ($row['role'] === 'admin') {
                // Jika Admin, kirim ke Dashboard Admin
                header("Location: admin/dashboard.php");
            } else {
                // Jika User Biasa, kirim ke welcome page dulu baru ke user dashboard
                header("Location: welcome.php");
            }
            exit();
        } else {
            $error_msg = "Wrong Password!";
        }
    } else {
        $error_msg = "Username or Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HydraFit</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/onboarding.css?v=3">
</head>

<body class="auth-body">
    <nav class="auth-navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
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

            <?php if ($error_msg): ?>
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

                    <div class="password-wrapper">
                        <input type="password" name="password" id="passwordInput" placeholder="Enter password" required>

                        <button type="button" id="togglePasswordBtn" class="toggle-eye">
                            <svg id="eyeOpen" class="hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>

                            <svg id="eyeClosed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <div style="text-align: right; margin-bottom: 25px;">
                    <a href="forgot_password.php" style="font-size: 0.85rem; color: #64748b; text-decoration: none;">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="btn-next">Log In</button>

                <div class="register-link">
                    Don't have an account? <a href="register.php">Sign Up</a>
                </div>

            </form>
        </div>
    </div>
    <script src="assets/js/toggle_password.js"></script>
</body>

</html>