<?php
    session_start();
    date_default_timezone_set('Asia/Phnom_Penh'); // Or your timezone
    require_once 'includes/db.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once __DIR__ . '/vendor/autoload.php'; // ✅ Composer autoload

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
    <title>Register - Portfolio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen">
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-lg flex overflow-hidden">
        <!-- Left Side -->
        <div class="w-1/2 bg-gradient-to-br from-blue-500 to-blue-300 text-white flex flex-col justify-center items-center p-10 relative">
            <h2 class="text-4xl font-bold mb-2">Register</h2>
            <p class="text-sm">Create a new account</p>
            <div class="absolute top-0 left-0 w-16 h-16 bg-white opacity-10 rounded-br-full"></div>
        </div>

        <!-- Right Side -->
        <div class="w-1/2 p-10">
            <h3 class="text-2xl font-bold text-center mb-6">Admin Register</h3>

            <!-- Notification Message -->
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 text-sm p-2 rounded border border-red-300"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-green-100 text-green-700 text-sm p-2 rounded border border-green-300"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="text" name="username" placeholder="Username" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

                <input type="email" name="email" placeholder="Email" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

                <input type="password" name="password" placeholder="Password" required minlength="6" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

                <input type="text" name="full_name" placeholder="Full Name (optional)" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Profile Image</label>
                    <input type="file" name="profile_image" id="profileImageInput" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        onchange="previewImage(event)" />
                </div>

                <div class="flex items-center gap-3 mt-1">
                    <img id="preview" src="#" class="w-12 h-12 object-cover rounded-full hidden border" />
                    <button type="button" onclick="removePreview()" id="removeBtn" class="text-red-500 text-xs hover:underline hidden">Remove</button>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 font-medium">Register</button>
            </form>

            <div class="mt-6 text-sm text-center">
                Or login with
                <div class="flex justify-center gap-4 mt-2">
                    <a href="google-login.php" class="flex items-center px-4 py-2 border rounded text-sm hover:bg-gray-50">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-4 h-4 mr-2" alt="Google">
                    Google
                    </a>
                    <a href="facebook-login.php" class="flex items-center px-4 py-2 border rounded text-sm hover:bg-gray-50">
                    <i class="fab fa-facebook text-blue-600 mr-2"></i> Facebook
                    </a>
                </div>
            </div>

            <p class="mt-4 text-center text-sm">
                Already registered?
                <a href="auth/login.php" class="text-blue-600 hover:underline font-medium">Login here</a>
            </p>
        </div>
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
            document.getElementById('preview').classList.add('hidden');
            document.getElementById('removeBtn').classList.add('hidden');
            document.getElementById('preview').src = '#';
            document.getElementById('profileImageInput').value = '';
        }
    </script>
</body>

</html>

