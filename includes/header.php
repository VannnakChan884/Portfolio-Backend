<?php
    session_start();
    if (!isset($_SESSION["admin_logged_in"])) {
        header("Location: auth/login.php");
        exit();
    }
    require_once 'includes/db.php';

    // Fetch counts
    $totalSkills = $conn->query("SELECT COUNT(*) as total FROM skills")->fetch_assoc()['total'];
    $totalProjects = $conn->query("SELECT COUNT(*) as total FROM projects")->fetch_assoc()['total'];
    $totalMessages = $conn->query("SELECT COUNT(*) as total FROM messages")->fetch_assoc()['total'];
    $totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

    // Fetch skill data for chart (default lang = 'en')
    $lang = 'en';
    $stmt = $conn->prepare("SELECT title, percentage FROM skills WHERE lang = ? ORDER BY id ASC");
    $stmt->bind_param("s", $lang);
    $stmt->execute();
    $result = $stmt->get_result();

    $skillLabels = [];
    $skillPercentages = [];
    while ($row = $result->fetch_assoc()) {
        $skillLabels[] = $row['title'];
        $skillPercentages[] = (int)$row['percentage'];
    }
    
    // Fetch user/site name
    $adminId = $_SESSION['admin_id'];
    $userData = $conn->query("SELECT username, full_name, user_profile FROM users WHERE id = $adminId")->fetch_assoc();
    $siteTitle = $userData['full_name'] ?? 'Admin Dashboard';
    $userName = $userData['username'] ?? 'Admin';
    $userProfile = $userData['user_profile'];
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($siteTitle)?> - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
 