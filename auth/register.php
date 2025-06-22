<?php
session_start();
require_once '../includes/db.php'; // your database connection

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $fullName   = trim($_POST['full_name'] ?? '');

    // Image upload
    $profilePath = 'assets/uploads/default.png';
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "assets/uploads/";
        $originalName = basename($_FILES['profile_image']['name']);
        $targetFile = $targetDir . $originalName;

        if (file_exists($targetFile)) {
            $error = "Image already exists. Please rename your file.";
        } else {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
                $profilePath = $targetFile;
            } else {
                $error = "Failed to upload image.";
            }
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password,  user_profile, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("sssss", $username, $fullName, $email, $password, $profilePath);

        if ($stmt->execute()) {
            // Auto-login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: ../dashboard.php");
            exit;
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Register Admin</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-600 p-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="username" placeholder="Username" required class="w-full p-2 border rounded" />
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded" />
            <input type="password" name="password" placeholder="Password" required minlength="6" class="w-full p-2 border rounded" />
            <input type="text" name="full_name" placeholder="Full Name (optional)" class="w-full p-2 border rounded" />

            <!-- Image Upload + Preview -->
            <input type="file" name="profile_image" accept="image/*" class="w-full p-2 border rounded" onchange="previewImage(event)" id="profileImageInput" />
            <div class="mt-2 flex items-center gap-4">
                <img id="preview" src="#" alt="Preview" class="w-24 h-24 object-cover rounded-full hidden border" />
                <button type="button" onclick="removePreview()" class="text-red-500 hover:underline hidden" id="removeBtn">Remove Image</button>
            </div>

            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 w-full">Register & Login</button>
            <p class="mt-4 text-center text-sm">
                Already have an account?
                <a href="../" class="text-blue-600 hover:underline">Login here</a>
            </p>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            const removeBtn = document.getElementById('removeBtn');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    removeBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function removePreview() {
            const preview = document.getElementById('preview');
            const removeBtn = document.getElementById('removeBtn');
            const fileInput = document.getElementById('profileImageInput');

            fileInput.value = '';
            preview.src = '#';
            preview.classList.add('hidden');
            removeBtn.classList.add('hidden');
        }
    </script>
</body>
</html>
