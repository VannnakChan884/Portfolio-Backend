<?php
session_start();
date_default_timezone_set('Asia/Phnom_Penh');
require_once '../includes/db.php';

$MAX_ATTEMPTS = 5;
$LOCKOUT_DURATION = 300; // 5 minutes

$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'] ?? '';
  $remember = isset($_POST['remember']);

  if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Email and password are required.";
  } else {
    $stmt = $conn->prepare("SELECT id, password, role, is_default_admin, failed_attempts, last_failed_login FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
      $stmt->bind_result($id, $hashed_password, $role, $is_default_admin, $failedAttempts, $lastFailed);
      $stmt->fetch();

      $now = time();
      $lastFailedTime = strtotime($lastFailed);
      $isLocked = $failedAttempts >= $MAX_ATTEMPTS && ($now - $lastFailedTime) < $LOCKOUT_DURATION;

      if ($isLocked) {
        $_SESSION['error'] = "Too many wrong attempts. Please try again after 5 minutes.";
      } elseif (!password_verify($password, $hashed_password)) {
        $stmt->close();
        $stmt = $conn->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $_SESSION['error'] = "Your credentials do not match our records!";
      } elseif (empty($role)) {
        $_SESSION['error'] = "Your account is pending approval by an admin.";
        // Removed the redirect to show message on current page
      } else {
        // Reset failed attempts
        $stmt->close();
        $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Check OTP
        $codeStmt = $conn->prepare("SELECT code, is_used, expires_at FROM login_codes WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $codeStmt->bind_param("i", $id);
        $codeStmt->execute();
        $codeResult = $codeStmt->get_result();
        $codeData = $codeResult->fetch_assoc();

        if ($codeData && !$codeData['is_used'] && strtotime($codeData['expires_at']) > time()) {
          $_SESSION['pending_user_id'] = $id;
          header("Location: verify.php?email=" . urlencode($email));
          exit;
        } else {
          $_SESSION['admin_logged_in'] = true;
          $_SESSION['admin_id'] = $id;
          $_SESSION['admin_role'] = $role;
          $_SESSION['is_default_admin'] = $is_default_admin;

          if ($remember) {
            $selector = bin2hex(random_bytes(12));
            $validator = bin2hex(random_bytes(32));
            $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);
            $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);

            $conn->query("DELETE FROM auth_tokens WHERE user_id = $id");

            $tokenStmt = $conn->prepare("INSERT INTO auth_tokens (selector, hashed_validator, user_id, expires_at) VALUES (?, ?, ?, ?)");
            $tokenStmt->bind_param("ssis", $selector, $hashedValidator, $id, $expires);
            $tokenStmt->execute();

            setcookie(
              'remember',
              $selector . ':' . $validator,
              [
                'expires' => time() + 60 * 60 * 24 * 30,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
              ]
            );
          }

          header("Location: ../dashboard.php");
          exit;
        }
      }
    } else {
      $_SESSION['error'] = "User not found.";
    }

    $stmt->close();
  }
}

// Toast data for JS
$toastMessage = $_SESSION['success'] ?? $_SESSION['error'] ?? '';
$toastType = isset($_SESSION['success']) ? 'success' : (isset($_SESSION['error']) ? 'error' : '');
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-gray-900 flex items-center justify-center h-screen">
  <div class="bg-white w-full max-w-4xl rounded-2xl shadow-lg flex overflow-hidden">
    <!-- Left Side -->
    <div
      class="w-1/2 bg-gradient-to-br from-blue-500 to-blue-300 text-white flex flex-col justify-center items-center p-10 relative">
      <h2 class="text-4xl font-bold mb-2">LOGIN</h2>
      <p class="text-sm">Welcome back! Please login to your account.</p>
    </div>

    <!-- Right Side -->
    <div class="w-1/2 p-10">
      <h3 class="text-2xl font-bold text-center mb-6">Admin Login</h3>

      <?php if (isset($_GET['registered'])): ?>
        <div class="bg-green-100 text-green-700 px-4 py-2 text-sm rounded mb-4 text-center">
          Registered successfully! Please log in.
        </div>
      <?php endif; ?>

      <form method="post" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Email</label>
          <div class="flex items-center border rounded px-3 py-2">
            <i class="fa-solid fa-envelope text-gray-400 mr-2"></i>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required
              placeholder="Enter your email" class="w-full outline-none">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium">Password</label>
          <div class="flex items-center border rounded px-3 py-2">
            <i class="fa-solid fa-lock text-gray-400 mr-2"></i>
            <input type="password" name="password" required placeholder="Enter your password"
              class="w-full outline-none">
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember"
              class="h-4 w-4 text-blue-500 rounded border-gray-300 focus:ring-blue-500">
            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
          </div>

          <div class="text-right">
            <a href="../forgot-password.php" class="text-sm text-blue-500 hover:underline">Forgot Password?</a>
          </div>
        </div>

        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">
          LOGIN
        </button>
      </form>

      <div class="mt-6 text-sm text-center">
        Or login with
        <a href="../google-login.php"
          class="flex items-center justify-center px-4 py-2 mt-2 border rounded text-sm hover:bg-gray-50">
          <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-4 h-4 mr-2" alt="Google">
          Google
        </a>
      </div>

      <p class="mt-4 text-center text-sm">
        Don't have an account? <a href="../register.php" class="text-blue-500 hover:underline">Register here</a>
      </p>
    </div>
  </div>

  <?php if ($toastMessage): ?>
    <script type="module">
      import { toast } from '../assets/js/toast-utils.js';
      toast(<?= json_encode($toastMessage) ?>, <?= json_encode($toastType) ?>);
    </script>
  <?php endif; ?>
</body>

</html>
