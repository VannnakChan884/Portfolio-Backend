<?php
    session_start();

    // Redirect to login if not authenticated
    if (!isset($_SESSION["admin_logged_in"])) {
        header("Location: auth/login.php");
        exit();
    }

    require_once 'includes/db.php';

    // Initialize defaults
    $siteTitle = 'Admin Dashboard';
    $userName = 'Admin';
    $userProfile = 'assets/uploads/default.png'; // fallback image

    // ✅ Prefer session profile if set (Google login)
    if (!empty($_SESSION['admin_profile'])) {
        $userProfile = $_SESSION['admin_profile'];
    }

    // ✅ Only run user query if session ID exists
    if (isset($_SESSION['admin_id'])) {
        $adminId = $_SESSION['admin_id'];

        // Use a prepared statement for security
        $stmt = $conn->prepare("SELECT username, full_name, user_profile FROM users WHERE id = ?");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $stmt->bind_result($username, $full_name, $user_profile);
        $stmt->fetch();
        $stmt->close();

        $siteTitle = $full_name ?: $siteTitle;
        $userName = $username ?: $userName;

        // ✅ Update profile only if it's a local file or valid image
        if (!empty($user_profile)) {
            if (filter_var($user_profile, FILTER_VALIDATE_URL)) {
                $userProfile = $user_profile;
            } elseif (file_exists($user_profile)) {
                $userProfile = $user_profile;
            }
        }
        // $userProfile = (isset($user_profile) && file_exists($user_profile)) ? $user_profile : 'assets/uploads/default.png';
    }
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($userName) ?> - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($userProfile); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    transitionProperty: {
                        'opacity': 'opacity',
                        'transform': 'transform',
                    },
                }
            },
            variants: {
                extend: {
                    opacity: ['disabled'],
                    pointerEvents: ['disabled'],
                },
            },
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
