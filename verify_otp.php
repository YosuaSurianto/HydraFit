<?php
session_start();
include 'koneksi.php'; 

// Cek sesi (Keamanan)
if (!isset($_SESSION['email_reset'])) {
    header("Location: forgot_password.php");
    exit();
}

$swal_icon = "";
$swal_title = "";
$swal_text = "";
$email = $_SESSION['email_reset'];

if (isset($_POST['verify'])) {
    $otp_input = mysqli_real_escape_string($conn, $_POST['otp_code']);

    // Cek Database: Email cocok + Kode cocok + Belum expired
    $query = "SELECT * FROM users WHERE email = '$email' AND otp_code = '$otp_input' AND otp_expiry >= NOW()";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Sukses!
        // Kita tidak hapus OTP dulu, biarkan nanti dihapus pas ganti password
        // Langsung redirect ke halaman ganti password
        header("Location: reset_password.php");
        exit();
    } else {
        $swal_icon = "error";
        $swal_title = "Invalid OTP";
        $swal_text = "The code is incorrect or has expired.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - HydraFit</title>
    <link rel="stylesheet" href="assets/css/auth_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="auth-card">
        <h2>Verification 🔐</h2>
        <p>We have sent a code to <strong><?php echo $email; ?></strong></p>

        <form method="POST">
            <input type="text" name="otp_code" class="form-control" placeholder="6-Digit Code" maxlength="6" required style="text-align: center; font-size: 20px; letter-spacing: 5px;">
            
            <button type="submit" name="verify" class="btn-primary">Verify Code</button>
        </form>

        <a href="forgot_password.php" class="back-link">Resend Code</a>
    </div>

    <?php if ($swal_icon != ""): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $swal_icon; ?>',
            title: '<?php echo $swal_title; ?>',
            text: '<?php echo $swal_text; ?>',
            confirmButtonColor: '#d33'
        });
    </script>
    <?php endif; ?>

</body>
</html>