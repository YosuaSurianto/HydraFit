<?php
// FILE: send_otp.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Path sesuai struktur folder kamu
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function sendOtpEmail($userEmail, $otpCode) {
    $mail = new PHPMailer(true);

    try {
        // --- MATIKAN DEBUG MODE ---
        $mail->SMTPDebug = 0; // Ubah jadi 0 biar tulisan kode hilang

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // --- EMAIL & PASSWORD ---
        $mail->Username   = 'dracooproject@gmail.com'; 
        $mail->Password   = 'usdm mxdh xscz odtj'; 
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('no-reply@hydrafit.com', 'HydraFit Support');
        $mail->addAddress($userEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password - HydraFit';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <h2 style='color: #00ADB5;'>Password Reset</h2>
                <p>Here is your OTP code:</p>
                <h1 style='letter-spacing: 5px; background: #f4f4f4; padding: 10px; display: inline-block;'>{$otpCode}</h1>
                <p>Valid for 5 minutes.</p>
            </div>
        ";
        $mail->AltBody = "Your OTP Code is: {$otpCode}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Kita return false aja biar SweetAlert yang handle error-nya
        return false;
    }
}
?>