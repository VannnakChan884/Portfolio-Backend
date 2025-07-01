<?php
require_once 'includes/db.php';
require_once 'auth/send_code.php'; // ✅ your PHPMailer function here

// function registerThirdPartyUser($username, $email, $conn, $profileImage = 'assets/uploads/default.png') {
//     // Check if user exists
//     $check = $conn->prepare("SELECT id, role, user_profile FROM users WHERE email = ?");
//     $check->bind_param("s", $email);
//     $check->execute();
//     $check->store_result();

//     if ($check->num_rows > 0) {
//         $check->bind_result($userId, $role, $userProfile);
//         $check->fetch();
//         $_SESSION['admin_profile'] = $userProfile ?: 'assets/uploads/default.png';

//         if ($role) {
//             $_SESSION['admin_logged_in'] = true;
//             $_SESSION['admin_id'] = $userId;
//             $_SESSION['admin_role'] = $role;
//             header("Location: dashboard.php");
//             exit;
//         } else {
//             send_login_code($conn, $userId);
//             $_SESSION['pending_user_id'] = $userId;
//             $_SESSION['login_notice'] = "Check your email for the login code.";
//             header("Location: auth/verify.php");
//             exit;
//         }
//     }

//     // Insert new third-party user
//     $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, user_profile, role, created_at, updated_at) VALUES (?, ?, NULL, ?, ?, NULL, NOW(), NOW())");
//     $stmt->bind_param("ssss", $username, $email, $username, $profileImage);

//     if ($stmt->execute()) {
//         $newUserId = $stmt->insert_id;
//         send_login_code($conn, $newUserId);
//         $_SESSION['pending_user_id'] = $newUserId;
//         $_SESSION['login_notice'] = "Check your email for the login code.";
//         header("Location: auth/verify.php");
//         exit;
//     } else {
//         echo "Error: " . $stmt->error;
//         exit;
//     }
// }

function registerThirdPartyUser($username, $email, $conn, $profileImage = 'assets/uploads/default.png') {
    // Check if user already exists
    $check = $conn->prepare("SELECT id, role FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->bind_result($userId, $role);
        $check->fetch();

        // ✅ User already exists — don't re-register
        $_SESSION['login_error'] = "This email is already registered. Please log in instead.";
        header("Location: auth/login.php");
        exit;
    }

    // ✅ Continue with registration for new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, user_profile, role, created_at, updated_at) VALUES (?, ?, NULL, ?, ?, NULL, NOW(), NOW())");
    $stmt->bind_param("ssss", $username, $email, $username, $profileImage);

    if ($stmt->execute()) {
        $newUserId = $stmt->insert_id;

        // Send login code
        $sendResult = send_login_code($conn, $newUserId);
        if ($sendResult !== true) {
            $_SESSION['login_error'] = "Failed to send login code: $sendResult";
            header("Location: auth/login.php");
            exit;
        }

        $_SESSION['pending_user_id'] = $newUserId;
        $_SESSION['login_notice'] = "Check your email for the login code.";
        header("Location: auth/verify.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
        exit;
    }
}

