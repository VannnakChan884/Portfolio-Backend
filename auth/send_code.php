<?php
date_default_timezone_set('Asia/Phnom_Penh');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php'; // ✅ Composer autoload

// function send_login_code($conn, $userId) {
//     // 1. Generate secure code
//     $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
//     $expiresAt = date('Y-m-d H:i:s', time() + 600); // 10 minutes

//     // 2. Invalidate old codes
//     $invalidate = $conn->prepare("UPDATE login_codes SET is_used = 1 WHERE user_id = ? AND is_used = 0");
//     $invalidate->bind_param("i", $userId);
//     $invalidate->execute();

//     // 3. Insert new code
//     $stmt = $conn->prepare("INSERT INTO login_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
//     $stmt->bind_param("iss", $userId, $newCode, $expiresAt);
//     if (!$stmt->execute()) return "❌ Failed to generate new code.";

//     // 4. Fetch user email
//     $userStmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
//     $userStmt->bind_param("i", $userId);
//     $userStmt->execute();
//     $result = $userStmt->get_result();
//     $user = $result->fetch_assoc();

//     if (!$user || empty($user['email'])) return "❌ Email not found for user ID $userId";

//     $username = htmlspecialchars($user['username']);
//     $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);

//     // 5. Send email using PHPMailer
//     try {
//         $mail = new PHPMailer(true);
//         $mail->isSMTP();
//         $mail->Host       = 'smtp.gmail.com';
//         $mail->SMTPAuth   = true;
//         $mail->Username   = 'vannakchan884@gmail.com';
//         $mail->Password   = 'fahf rmxe xpep crwm'; // App Password
//         $mail->SMTPSecure = 'tls';
//         $mail->Port       = 587;

//         $mail->setFrom('vannakchan884@gmail.com', 'Portfolio App');
//         $mail->addAddress($email, $username);

//         $mail->isHTML(true);
//         $mail->Subject = '🔐 Your Login Verification Code';
//         $mail->Body    = "
//             <p>Hi <strong>$username</strong>,</p>
//             <p>Your login code is:</p>
//             <h2 style='color:#1a73e8;'>$newCode</h2>
//             <p>This code will expire in 10 minutes.</p>
//             <hr>
//             <small>If you did not request this, please ignore this email.</small>
//         ";
//         $mail->AltBody = "Hi $username,\nYour login code is: $newCode\nThis code will expire in 10 minutes.";

//         $mail->send();
//         return true;
//     } catch (Exception $e) {
//         return "❌ Mailer Error: " . $mail->ErrorInfo;
//     }
// }
function send_login_code($conn, $userId) {
    // ✅ 0. Check rate limit (5 codes per 15 minutes)
    $limitStmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM login_codes 
        WHERE user_id = ? AND created_at >= (NOW() - INTERVAL 15 MINUTE)
    ");
    $limitStmt->bind_param("i", $userId);
    $limitStmt->execute();
    $limitStmt->bind_result($count);
    $limitStmt->fetch();
    $limitStmt->close();

    if ($count >= 5) {
        return "⚠️ You’ve reached the limit of 5 code requests in 15 minutes. Try again later.";
    }

    // ✅ 1. Generate secure code
    $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', time() + 600); // 10 minutes

    // ✅ 2. Invalidate old codes
    $invalidate = $conn->prepare("UPDATE login_codes SET is_used = 1 WHERE user_id = ? AND is_used = 0");
    $invalidate->bind_param("i", $userId);
    $invalidate->execute();

    // ✅ 3. Insert new code
    $stmt = $conn->prepare("INSERT INTO login_codes (user_id, code, expires_at, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $userId, $newCode, $expiresAt);
    if (!$stmt->execute()) return "❌ Failed to generate new code.";

    // ✅ 4. Fetch user email
    $userStmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $result = $userStmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || empty($user['email'])) return "❌ Email not found for user ID $userId";

    $username = htmlspecialchars($user['username']);
    $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);

    // ✅ 5. Send email using PHPMailer
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

        $mail->isHTML(true);
        $mail->Subject = '🔐 Your Login Verification Code';
        $mail->Body    = "
            <p>Hi <strong>$username</strong>,</p>
            <p>Your login code is:</p>
            <h2 style='color:#1a73e8;'>$newCode</h2>
            <p>This code will expire in 10 minutes.</p>
            <hr>
            <small>If you did not request this, please ignore this email.</small>
        ";
        $mail->AltBody = "Hi $username,\nYour login code is: $newCode\nThis code will expire in 10 minutes.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "❌ Mailer Error: " . $mail->ErrorInfo;
    }
}

