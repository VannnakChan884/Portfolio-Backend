<?php
session_start();
require_once '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        if (!password_verify($password, $hashed_password)) {
            $error = "Incorrect password.";
        } elseif (empty($role)) {
            $error = "Your account is pending approval by an admin.";
        } else {
            // Check if user has unverified valid code
            $codeStmt = $conn->prepare("SELECT code, is_used, expires_at FROM login_codes WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
            $codeStmt->bind_param("i", $id);
            $codeStmt->execute();
            $codeResult = $codeStmt->get_result();
            $codeData = $codeResult->fetch_assoc();

            if ($codeData && !$codeData['is_used'] && strtotime($codeData['expires_at']) > time()) {
                $_SESSION['pending_user_id'] = $id;
                header("Location: verify.php");
                exit;
            } else {
                // Login success (already verified)
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_role'] = $role;
                header("Location: ../dashboard.php");
                exit;
            }
        }
    } else {
        $error = "User not found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <form method="post" class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <h2 class="text-xl font-bold mb-4 text-center">Admin Login</h2>
        <?php if (isset($_GET['registered'])): ?>
            <p class="bg-green-100 text-green-600 p-2 rounded mb-4 text-sm text-center">
                Registered successfully! Please log in with your code.
            </p>
        <?php endif; ?>


        <?php if ($error): ?>
            <p class="bg-red-500/10 text-red-500 text-sm mb-3 py-2 text-center rounded"><?= $error ?></p>
        <?php endif; ?>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" class="w-full px-3 py-2 border rounded mb-3" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" class="w-full px-3 py-2 border rounded mb-4" required>

        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Login</button>

        <p class="mt-4 text-center text-sm">
            Don't have an account?
            <a href="../register.php" class="text-blue-600 hover:underline">Register here</a>
        </p>
    </form>
</body>
</html>
