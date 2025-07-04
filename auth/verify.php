<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['pending_user_id'])) {
    header("Location: login.php");
    exit;
}

$success = $_SESSION['register_success'] ?? $_SESSION['resend_success'] ?? '';
$error = $_SESSION['resend_error'] ?? '';
unset($_SESSION['register_success'], $_SESSION['resend_success'], $_SESSION['resend_error']);

// Handle resend request
if (isset($_GET['resend']) && $_GET['resend'] === 'true') {
    require_once 'send_code.php';
    $result = send_login_code($conn, $_SESSION['pending_user_id']);
    if ($result === true) {
        $_SESSION['resend_success'] = "✅ A new code has been sent to your email.";
    } else {
        $_SESSION['resend_error'] = $result;
    }
    header("Location: verify.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    $userId = $_SESSION['pending_user_id'];

    $verifyStmt = $conn->prepare("SELECT id FROM login_codes WHERE user_id = ? AND code = ? AND is_used = 0 AND expires_at > NOW()");
    $verifyStmt->bind_param("is", $userId, $code);
    $verifyStmt->execute();
    $verifyStmt->store_result();

    if ($verifyStmt->num_rows === 1) {
        $verifyStmt->bind_result($codeId);
        $verifyStmt->fetch();

        $updateStmt = $conn->prepare("UPDATE login_codes SET is_used = 1 WHERE id = ?");
        $updateStmt->bind_param("i", $codeId);
        $updateStmt->execute();

        // Log in the user
        $getUser = $conn->prepare("SELECT role, user_profile FROM users WHERE id = ?");
        $getUser->bind_param("i", $userId);
        $getUser->execute();
        $getUser->bind_result($role, $user_profile);
        $getUser->fetch();

        if (empty($role)) {
            $_SESSION['login_error'] = "Your account is registered but not approved yet. Please contact the administrator.";
            header("Location: login.php");
            exit;
        }

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $userId;
        $_SESSION['admin_role'] = $role;
        $_SESSION['admin_profile'] = $user_profile ?: 'assets/uploads/default.png';

        header("Location: ../dashboard.php");
        exit;
    } else {
        $error = "Invalid or expired code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen relative">

    <!-- ✅ Form -->
    <form method="POST" class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <h2 class="text-xl font-bold mb-4 text-center">Verify Your Code</h2>

        <input type="text" name="code" placeholder="Enter 6-digit code" required class="w-full p-2 border rounded mb-4" />

        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 mb-2">Verify</button>

        <div class="text-center text-sm">
            Didn't receive a code?
            <a href="verify.php?resend=true" class="text-blue-500 underline hover:text-blue-700">Resend Code</a>
        </div>
    </form>

    <script type="module">
        import { toast } from '../assets/js/toast-utils.js';

        <?php if (!empty($success) || !empty($error)): ?>
            toast(<?= json_encode($success ?: $error) ?>, "<?= $success ? 'success' : 'error' ?>");
        <?php endif; ?>
    </script>
</body>
</html>
