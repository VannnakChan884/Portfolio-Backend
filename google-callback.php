<?php
session_start();
require_once 'vendor/autoload.php'; // Google Client
require_once 'includes/db.php';
require_once 'auth/send_code.php';
require_once 'thirdparty-register.php';

$client = new Google\Client();
$client->setClientId('73933804377-jd44frkago7qqrp40qlgno7u788lr4rf.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/portfolio-backend/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        $_SESSION['login_error'] = "Google login failed: " . htmlspecialchars($token['error_description'] ?? $token['error']);
        header("Location: auth/login.php");
        exit;
    }

    $client->setAccessToken($token);
    $oauth = new Google\Service\Oauth2($client);
    $googleUser = $oauth->userinfo->get();

    $email = $googleUser->email;
    $name = $googleUser->name;
    $profileImage = $googleUser->picture ?? 'assets/uploads/default.png';

    // ✅ Check if user already exists
    $stmt = $conn->prepare("SELECT id, role, user_profile FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // ✅ Already registered
        $stmt->bind_result($user_id, $role, $user_profile);
        $stmt->fetch();

        if (empty($role)) {
            $_SESSION['login_error'] = "This email is already registered and pending approval.";
            header("Location: auth/login.php");
            exit;
        }

        // Check if login code already exists and is still valid
        $codeStmt = $conn->prepare("SELECT code, is_used, expires_at FROM login_codes WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $codeStmt->bind_param("i", $user_id);
        $codeStmt->execute();
        $codeData = $codeStmt->get_result()->fetch_assoc();

        if ($codeData && !$codeData['is_used'] && strtotime($codeData['expires_at']) > time()) {
            $_SESSION['pending_user_id'] = $user_id;
            header("Location: auth/verify.php");
            exit;
        } else {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user_id;
            $_SESSION['admin_role'] = $role;
            $_SESSION['admin_profile'] = $user_profile ?: 'assets/uploads/default.png';
            header("Location: dashboard.php");
            exit;
        }
    } else {
        // ✅ New user — register and send code
        registerThirdPartyUser($name, $email, $conn, $profileImage);
    }
} else {
    header("Location: auth/login.php");
    exit;
}
