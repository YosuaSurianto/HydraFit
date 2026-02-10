<?php
session_start();
include 'koneksi.php'; 

// Keamanan: Cek sesi email
if (!isset($_SESSION['email_reset'])) {
    header("Location: login.php");
    exit();
}

$swal_icon = "";
$swal_title = "";
$swal_text = "";
$email = $_SESSION['email_reset'];

if (isset($_POST['reset_password'])) {
    // Ambil langsung dari POST untuk password (jangan di-escape dulu)
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if ($pass1 === $pass2) {

        // Hash password asli
        $final_pass = password_hash($pass1, PASSWORD_DEFAULT);
        
        // Update password & hapus OTP
        $update = mysqli_query($conn, "UPDATE users SET password = '$final_pass', otp_code = NULL, otp_expiry = NULL WHERE email = '$email'");

        if ($update) {
            $swal_icon = "success";
            $swal_title = "Password Changed!";
            $swal_text = "You can now login with your new password.";
            $redirect_url = "login.php";
            
            // Hapus sesi email reset karena sudah selesai
            unset($_SESSION['email_reset']); 
            // session_destroy(); // Jangan destroy seluruh session, biar gak ganggu session lain
        } else {
            $swal_icon = "error";
            $swal_title = "Error";
            $swal_text = "Database update failed.";
        }
    } else {
        $swal_icon = "error";
        $swal_title = "Mismatch";
        $swal_text = "Passwords do not match!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - HydraFit</title>
    <link rel="stylesheet" href="assets/css/auth_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="auth-card">
        <h2>Reset Password 🔓</h2>
        <p>Create a new strong password for your account.</p>

        <form method="POST">
            <input type="password" name="pass1" class="form-control" placeholder="New Password" required minlength="6">
            <input type="password" name="pass2" class="form-control" placeholder="Confirm Password" required minlength="6">
            
            <button type="submit" name="reset_password" class="btn-primary">Change Password</button>
        </form>
    </div>

    <?php if ($swal_icon != ""): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $swal_icon; ?>',
            title: '<?php echo $swal_title; ?>',
            text: '<?php echo $swal_text; ?>',
            confirmButtonColor: '#00ADB5'
        }).then((result) => {
            <?php if (isset($redirect_url)): ?>
                window.location = '<?php echo $redirect_url; ?>';
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>