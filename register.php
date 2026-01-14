<?php
session_start();
include 'koneksi.php'; // Panggil jembatan database

// Variabel untuk menampung pesan error/sukses
$error_msg = "";

// Cek apakah tombol "Sign Up" ditekan
if (isset($_POST['sign_up'])) {
    // 1. Ambil data dari form dan bersihkan (biar aman dari hacker)
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password_raw = $_POST['password'];

    // 2. Cek apakah Email atau Username sudah ada di database?
    // Kita pakai OR karena keduanya harus unik
    $check_query = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
    $result_check = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result_check) > 0) {
        // Kalau ketemu datanya, berarti sudah terpakai
        $error_msg = "Email or Username already taken! Please choose another.";
    } else {
        // 3. Enkripsi Password (Wajib biar aman)
        $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

        // 4. Masukkan data ke Database
        $insert_query = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$password_hash')";
        
        if (mysqli_query($conn, $insert_query)) {
            // BERHASIL!
            // Ambil ID user yang barusan dibuat
            $new_user_id = mysqli_insert_id($conn);
            
            // Simpan ID ke Session (biar sistem tau siapa yang lagi login)
            $_SESSION['user_id'] = $new_user_id;
            
            // Lempar ke Step 2 (Isi Nama)
            header("Location: create-profile.php");
            exit();
        } else {
            $error_msg = "Error System: " . mysqli_error($conn);
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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
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
            
            <div class="auth-toggle">
                <a href="register.php" class="toggle-btn active">Sign up</a>
                <a href="login.php" class="toggle-btn inactive">Log in</a>
            </div>

            <h2 class="auth-title">Create Account</h2>

            <?php if(!empty($error_msg)): ?>
                <div style="background-color: #fee2e2; color: #ef4444; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="example@gmail.com" required>
                </div>

                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Choose a username" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="passwordInput" placeholder="Min. 8 characters" minlength="8" required>
                        
                        <span id="togglePassword" class="eye-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </span>
                    </div>
                </div>

                <button type="submit" name="sign_up" class="btn-submit">Sign Up</button>
            </form>
            
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Ganti Icon
                if (type === 'text') {
                    this.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
                } else {
                    this.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
                }
            });
        }
    </script>
</body>
</html>