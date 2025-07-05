<?php
session_start();
require_once 'includes/db.php';
require_once 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            $_SESSION['error'] = "Database error: " . $conn->error;
            header("Location: forgot-password.php");
            exit;
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            $_SESSION['error'] = "Database error: " . $stmt->error;
            header("Location: forgot-password.php");
            exit;
        }

        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // Generate token and expiration
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Delete any previous tokens
            $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            if ($deleteStmt) {
                $deleteStmt->bind_param("s", $email);
                $deleteStmt->execute();
                $deleteStmt->close();
            }

            // Insert reset token
            $insertStmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            if (!$insertStmt) {
                $_SESSION['error'] = "Database error: " . $conn->error;
                header("Location: forgot-password.php");
                exit;
            }

            $insertStmt->bind_param("sss", $email, $token, $expires);
            if (!$insertStmt->execute()) {
                $_SESSION['error'] = "Database error: " . $insertStmt->error;
                header("Location: forgot-password.php");
                exit;
            }
            $insertStmt->close();

            // Email configuration
            $resetLink = "http://localhost/portfolio-backend/reset-password.php?token=$token"; // CHANGE THIS

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'vannakchan884@gmail.com';
                $mail->Password = 'fahf rmxe xpep crwm';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('vannak@info.com', 'Vannak Portfolio');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Click the link to reset your password: <a href='$resetLink'>$resetLink</a>";

                $mail->send();
                $_SESSION['success'] = "Password reset email sent. Please check your inbox.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['error'] = "No account found with that email.";
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Please enter your email.";
    }

    header("Location: forgot-password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center">

    <div class="max-w-md w-full p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white text-center mb-4">Forgot Password</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email
                    Address</label>
                <input type="email" name="email" id="email" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Send
                Reset Link</button>
        </form>

        <div class="text-sm text-center text-gray-500 dark:text-gray-400 mt-4">
            <a href="login.php" class="hover:underline">Back to login</a>
        </div>
    </div>

</body>

</html>