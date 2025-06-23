<?php
    session_start();
    date_default_timezone_set('Asia/Phnom_Penh'); // Or your timezone
    require_once 'includes/db.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/Exception.php';

    $error = '';
    $success = '';

    // Check if first user
    $userCount = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
    $isFirstUser = $userCount == 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username   = trim($_POST['username']);
        $email      = trim($_POST['email']);
        $password   = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $fullName   = trim($_POST['full_name'] ?? '');
        $loginCode  = !$isFirstUser ? str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT) : null;
        // $expiresAt  = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $expiresAt = date('Y-m-d H:i:s', time() + 600); // 600 = 10 min
        $role       = $isFirstUser ? 'admin' : null;
        $profilePath = 'assets/uploads/default.png';

        // Check for duplicate username/email
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Username or Email already exists.";
        }

        // Handle image upload if no error yet
        if (!$error && !empty($_FILES['profile_image']['name'])) {
            $targetDir = "assets/uploads/";
            $uniqueName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
            $targetFile = $targetDir . $uniqueName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $_FILES['profile_image']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $error = "Only image files are allowed.";
            } elseif (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                $profilePath = $targetFile;
            } else {
                $error = "Failed to upload image.";
            }
        }

        // Insert into DB
        if (!$error) {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, user_profile, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("ssssss", $username, $email, $password, $fullName, $profilePath, $role);

            if ($stmt->execute()) {
                $userId = $stmt->insert_id;

                if (!$isFirstUser) {
                    $codeStmt = $conn->prepare("INSERT INTO login_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
                    $codeStmt->bind_param("iss", $userId, $loginCode, $expiresAt);
                    $codeStmt->execute();

                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'vannakchan884@gmail.com';
                        $mail->Password   = 'fahf rmxe xpep crwm'; // Gmail App Password
                        $mail->SMTPSecure = 'tls';
                        $mail->Port       = 587;

                        $mail->setFrom('vannakchan884@gmail.com', 'PHPMailer');
                        $mail->addAddress($email, $username);
                        $mail->Subject = 'Your Login Code';
                        $mail->Body    = "Hi $username,\n\nYour login code is: $loginCode\nThis code expires at 10 minutes!\nPlease wait for an admin to approve your access.";

                        $mail->send();
                        $success = "Registered successfully! Check your email for the login code.";
                        header("Location: auth/login.php?registered=1");
                        exit;
                    } catch (Exception $e) {
                        // Rollback if mail failed
                        $conn->query("DELETE FROM users WHERE id = $userId");
                        $error = "Registration failed: could not send email. Mailer Error: {$mail->ErrorInfo}";
                    }
                }

                if ($isFirstUser) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $userId;
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['admin_role'] = $role;
                    header("Location: dashboard.php");
                    exit;
                }
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
        <h2 class="text-2xl font-bold mb-4">Register</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-600 p-2 rounded mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-600 p-2 rounded mb-4"><?= htmlspecialchars($success) ?></div>
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

            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 w-full">Register</button>
            <p class="mt-4 text-center text-sm">
                Already have an account?
                <a href="auth/login.php" class="text-blue-600 hover:underline">Login here</a>
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
