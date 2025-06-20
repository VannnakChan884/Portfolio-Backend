<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: auth/login.php");
    exit();
}
require_once 'includes/db.php';

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, full_name, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $full_name, $hash);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User added successfully.";
        } else {
            $error = "Error adding user: " . $conn->error;
        }
    }
    header("Location: users.php");
    exit;
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM users WHERE id = $id")) {
        $_SESSION['success'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete user.";
    }
    header("Location: users.php");
    exit;
}

// Fetch users
$users = $conn->query("SELECT id, username, email, full_name, created_at FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6 font-sans">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-4">Manage Admin Users</h2>

    <!-- Notifications -->
    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="bg-green-500 text-white p-3 rounded mb-4">'.htmlspecialchars($_SESSION['success']).'</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="bg-red-500 text-white p-3 rounded mb-4">'.htmlspecialchars($_SESSION['error']).'</div>';
        unset($_SESSION['error']);
    }
    if (isset($error)) {
        echo '<div class="bg-red-500 text-white p-3 rounded mb-4">'.htmlspecialchars($error).'</div>';
    }
    ?>

    <form method="POST" class="mb-6 grid grid-cols-1 gap-4 max-w-md">
        <input class="border p-2 rounded" type="text" name="username" placeholder="Username" required>
        <input class="border p-2 rounded" type="email" name="email" placeholder="Email" required>
        <input class="border p-2 rounded" type="text" name="full_name" placeholder="Full Name (optional)">
        <input class="border p-2 rounded" type="password" name="password" placeholder="Password" required minlength="6">
        <button name="add_user" type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Add User</button>
    </form>

    <h3 class="text-lg font-semibold mb-2">Existing Users</h3>
    <table class="w-full border text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Username</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Full Name</th>
                <th class="p-2 border">Created At</th>
                <th class="p-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
            <tr class="border-b">
                <td class="p-2 border"><?= htmlspecialchars($user['username']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($user['email']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($user['full_name']) ?></td>
                <td class="p-2 border"><?= $user['created_at'] ?></td>
                <td class="p-2 border">
                    <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Delete user?')" class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="inline-block mt-4 text-gray-600 hover:underline">← Back to Dashboard</a>
</div>
</body>
</html>
