<?php
session_start();
require_once 'includes/db.php';

// Clear all session variables
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Delete remember me cookie and token
if (isset($_COOKIE['remember'])) {
    list($selector, $validator) = explode(':', $_COOKIE['remember']);
    $conn->query("DELETE FROM auth_tokens WHERE selector = '$selector'");
    setcookie('remember', '', time() - 3600, '/');
}

header("Location: auth/login.php");
exit;
?>