<?php
session_start();
include 'koneksi.php';

$error_msg = "";
$success_msg = "";

if (isset($_POST['register'])) {
    // AMBIL INPUT & BERSIHKAN
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // CEK APAKAH EMAIL SUDAH ADA? (Mencegah Duplikat)
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error_msg = "Email or Username already registered!";
    } else {
        // ENKRIPSI PASSWORD memakai algoritma BCRYPT bawaan PHP.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // PREPARED STATEMENT INSERT (Brankas Aman) 
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        
        // Binding: sss = string, string, string
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            // SUKSES & AUTO-LOGIN (PENTING!)
            // Ambil ID user yang barusan dibuat
            $new_user_id = $conn->insert_id;

            // Simpan ke Session biar langsung dianggap LOGIN
            $_SESSION['user_id'] = $new_user_id;

            // Redirect langsung ke pengisian profil
            header("Location: create-profile.php");
            exit();
        } else {
            $error_msg = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - HydraFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/onboarding.css?v=3">
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
                <h2 class="auth-title">Create Account 🚀</h2>
                <p class="step-indicator">Join us and start your journey</p>
            </div>

            <?php if($error_msg): ?>
                <div class="alert-error">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Choose a username" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="passwordInput" placeholder="Create a password" required>
                    </div>
                </div>

                <button type="submit" name="register" class="btn-next">Sign Up</button>

                <div class="login-link">
                    Already have an account? <a href="login.php">Log In</a>
                </div>

            </form>
        </div>
    </div>

</body>
</html>