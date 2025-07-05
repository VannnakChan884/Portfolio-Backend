<?php
require_once 'includes/db.php';

if (empty($_SESSION['admin_logged_in']) && isset($_COOKIE['remember'])) {
    list($selector, $validator) = explode(':', $_COOKIE['remember']);

    $stmt = $conn->prepare("SELECT * FROM auth_tokens WHERE selector = ?");
    $stmt->bind_param("s", $selector);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($token = $result->fetch_assoc()) {
        if (password_verify($validator, $token['hashed_validator'])) {
            // Token is valid, log the user in
            $userStmt = $conn->prepare("SELECT id, role, is_default_admin FROM users WHERE id = ?");
            $userStmt->bind_param("i", $token['user_id']);
            $userStmt->execute();
            $userResult = $userStmt->get_result();

            if ($user = $userResult->fetch_assoc()) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['is_default_admin'] = $user['is_default_admin'];

                // Optionally refresh the token
                $newValidator = bin2hex(random_bytes(32));
                $newHashedValidator = password_hash($newValidator, PASSWORD_DEFAULT);
                $newExpires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);

                $updateStmt = $conn->prepare("UPDATE auth_tokens SET hashed_validator = ?, expires_at = ? WHERE id = ?");
                $updateStmt->bind_param("ssi", $newHashedValidator, $newExpires, $token['id']);
                $updateStmt->execute();

                setcookie(
                    'remember',
                    $selector . ':' . $newValidator,
                    [
                        'expires' => time() + 60 * 60 * 24 * 30,
                        'path' => '/',
                        'domain' => '',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );
            }
        } else {
            // Invalid validator - delete the token
            $conn->query("DELETE FROM auth_tokens WHERE id = {$token['id']}");
            setcookie('remember', '', time() - 3600, '/');
        }
    }
}
?>