<?php
session_start();
include 'koneksi.php'; 
include 'send_otp.php'; 

$swal_icon = "";
$swal_title = "";
$swal_text = "";

if (isset($_POST['send_otp'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $check_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($check_user) > 0) {
        $otp = rand(100000, 999999);
        $update = mysqli_query($conn, "UPDATE users SET otp_code = '$otp', otp_expiry = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE email = '$email'");

        if ($update) {
            if (sendOtpEmail($email, $otp)) {
                $_SESSION['email_reset'] = $email;
                
                $swal_icon = "success";
                $swal_title = "OTP Sent!";
                $swal_text = "Please check your email inbox (or spam folder).";
                $redirect_url = "verify_otp.php"; 
            } else {
                $swal_icon = "error";
                $swal_title = "Failed";
                $swal_text = "Could not send email. Check your internet connection.";
            }
        } else {
            $swal_icon = "error";
            $swal_title = "Error";
            $swal_text = "Database error occurred.";
        }
    } else {
        $swal_icon = "error";
        $swal_title = "Not Found";
        $swal_text = "Email address is not registered!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - HydraFit</title>
    <link rel="stylesheet" href="assets/css/auth_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="auth-card">
        <h2>Forgot Password? 🔒</h2>
        <p>Enter your registered email address and we'll send you an OTP to reset your password.</p>

        <form method="POST">
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            <button type="submit" name="send_otp" class="btn-primary">Send OTP</button>
        </form>

        <a href="login.php" class="back-link">← Back to Login</a>
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