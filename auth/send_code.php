<?php
date_default_timezone_set('Asia/Phnom_Penh'); // Or your timezone
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

function send_login_code($conn, $userId) {
    // Generate new code
    $newCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', time() + 600); // 600 = 10 min

    // Invalidate old codes
    $conn->query("UPDATE login_codes SET is_used = 1 WHERE user_id = $userId AND is_used = 0");

    // Insert new code
    $stmt = $conn->prepare("INSERT INTO login_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $newCode, $expiresAt);
    if (!$stmt->execute()) return "Failed to generate new code.";

    // Fetch user's email and name
    $user = $conn->query("SELECT username, email FROM users WHERE id = $userId")->fetch_assoc();
    $username = $user['username'];
    $email = $user['email'];

    // Send email
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'vannakchan884@gmail.com';
        $mail->Password   = 'fahf rmxe xpep crwm'; // App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('vannakchan884@gmail.com', 'Portfolio App');
        $mail->addAddress($email, $username);
        $mail->Subject = 'Your Login Verification Code';
        $mail->Body    = "Hi $username,\n\nYour new login verification code is: $newCode\n\nThis code will expire in 10 minutes.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Mailer Error: {$mail->ErrorInfo}";
    }
}
