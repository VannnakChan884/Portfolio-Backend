<?php
    session_start();
    if (!isset($_SESSION["admin_logged_in"])) {
        header("Location: auth/login.php");
        exit();
    }
    require_once 'includes/db.php';
    require_once 'includes/functions.php';

    // Handle Add User
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
        $result = handleAddUser($conn);
        if ($result === true) {
            $_SESSION['success'] = "User added successfully.";
            header("Location: users.php");
            exit;
        } else {
            $error = $result;
        }
    }

    // Handle Update User
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
        $result = handleUpdateUser($conn);
        if ($result === true) {
            $_SESSION['success'] = "User updated successfully.";
            header("Location: users.php");
            exit;
        } else {
            $_SESSION['error'] = $result;
            header("Location: users.php?edit={$_POST['id']}&username=" . urlencode($_POST['username']) . "&email=" . urlencode($_POST['email']) . "&full_name=" . urlencode($_POST['full_name']) . "&user_profile=" . urlencode($_POST['existing_profile']));
            exit;
        }
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
    $users = $conn->query("SELECT id, username, email, full_name, user_profile, created_at FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
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
<body class="bg-gray-50 p-6 font-sans">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex flex-row gap-x-8 items-center mb-4">
        <a href="dashboard.php" class="px-4 py-2 bg-gray-300/50 hover:bg-gray-300 text-xl text-gray-600 rounded">
            <i class="fas fa-arrow-left mr-1"></i>
            Back
        </a>
        <h2 class="text-2xl font-bold">Manage Admin Users</h2>
    </div>

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

    <form method="POST" enctype="multipart/form-data" class="mb-6 grid grid-cols-1 gap-4 max-w-xl">
        <input type="hidden" name="id" value="<?= $_GET['edit'] ?? '' ?>">
        <div class="grid grid-cols-2 gap-x-4">
            <input class="border p-2 rounded" type="text" name="username" placeholder="Username" value="<?= $_GET['username'] ?? '' ?>" required>
            <input class="border p-2 rounded" type="text" name="full_name" placeholder="Full Name (optional)" value="<?= $_GET['full_name'] ?? '' ?>">
        </div>
        <input class="border p-2 rounded" type="email" name="email" placeholder="Email" value="<?= $_GET['email'] ?? '' ?>" required>
        
        <input type="hidden" name="existing_profile" value="<?= $_GET['user_profile'] ?? '' ?>">
        <input class="border p-2 rounded" type="file" name="user_profile" accept="image/*">
        <div class="mt-2">
            <img id="imagePreview" src="<?= $_GET['user_profile'] ?? '' ?>" alt="Preview" class="w-20 h-20 object-cover rounded-full <?= isset($_GET['user_profile']) ? '' : 'hidden' ?>">
        </div>
        <button type="button" id="removeImageBtn"
            class="text-red-500 hover:underline <?= isset($_GET['user_profile']) ? '' : 'hidden' ?>">
            Remove
        </button>

        <div class="flex gap-3">
            <?php if (isset($_GET['edit'])): ?>
                <button name="update_user" type="submit"
                    class="bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700 disabled:opacity-50 disabled:pointer-events-none">
                    Update User
                </button>
            <?php else: ?>
                <div>
                    <input class="border p-2 rounded flex-1" type="password" name="password" placeholder="Password" required minlength="6">
                    <button name="add_user" type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Add User</button>
                </div>
            <?php endif; ?>

            <!-- Cancel Button (always shown) -->
            <a href="users.php" id="cancelBtn"
                class="bg-gray-400 text-white py-2 px-4 rounded hover:bg-gray-500 opacity-50 pointer-events-none">
                Cancel
            </a>
        </div>
    </form>


    <h3 class="text-lg font-semibold mb-2">Existing Users</h3>
    <table class="w-full border text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Username</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Full Name</th>
                <th class="p-2 border">Profile</th>
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
                
                <td class="p-2 border">
                    <?php if ($user['user_profile']): ?>
                        <img src="<?= htmlspecialchars($user['user_profile']) ?>" alt="Profile" class="w-10 h-10 rounded-full mx-auto">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td class="p-2 border"><?= $user['created_at'] ?></td>
                <td class="p-2 border">
                    <a href="?edit=<?= $user['id'] ?>
                    &username=<?= urlencode($user['username'] ?? '') ?>
                    &email=<?= urlencode($user['email'] ?? '') ?>
                    &full_name=<?= urlencode($user['full_name'] ?? '') ?>
                    &user_profile=<?= urlencode($user['user_profile'] ?? '') ?>"
                    class="text-blue-600 hover:underline">Edit </a>|
                    <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Delete user?')" class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script type="module">
    import { initImagePreview, initCancelButton, initUpdateButtonDisable } from './assets/js/form-utils.js';

    initImagePreview('input[name="user_profile"]', '#imagePreview', '#removeImageBtn');
    initCancelButton('form', '#cancelBtn');

    // FIXED: this should look for a BUTTON
    const isEditing = Boolean(document.querySelector('button[name="update_user"]'));
    if (isEditing) {
        initUpdateButtonDisable('form', 'button[name="update_user"]');
    }
</script>
</body>
</html>
