<?php
session_start();
require_once 'includes/db.php'; // Adjust path as needed

$token = $_GET['token'] ?? '';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($token) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } else {
        // Validate token
        $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($email, $expires_at);
            $stmt->fetch();

            if (strtotime($expires_at) >= time()) {
                // Update password
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $update->bind_param("ss", $hashed, $email);
                $update->execute();
                $update->close();

                // Clean up token
                $delete = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                $delete->bind_param("s", $token);
                $delete->execute();
                $delete->close();

                $success = "Your password has been reset. You can now <a href='auth/login.php' class='underline'>login</a>.";
            } else {
                $errors[] = "This reset link has expired.";
            }
        } else {
            $errors[] = "Invalid or expired token.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center">

<div class="max-w-md w-full p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white text-center mb-6">Reset Your Password</h2>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4"><?= $success ?></div>
    <?php endif; ?>

    <?php if (empty($success)): ?>
    <form method="POST" class="space-y-4">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
            <input type="password" name="password" required
                   class="w-full mt-1 p-2 border border-gray-300 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
            <input type="password" name="confirm_password" required
                   class="w-full mt-1 p-2 border border-gray-300 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600">
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Reset Password
        </button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
