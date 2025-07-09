<?php
session_start();
require_once '../includes/db.php';

$email = $_GET['email'] ?? ''; // ✅ define email early

// If user tries to access verify.php without an email
if (!isset($_SESSION['pending_user_id']) || empty($email)) {
    header("Location: login.php");
    exit;
}

$toastMessage = '';
$toastType = '';
if (isset($_SESSION['resend_success'])) {
    $toastMessage = $_SESSION['resend_success'];
    $toastType = 'success';
    unset($_SESSION['resend_success']);
} elseif (isset($_SESSION['resend_error'])) {
    $toastMessage = $_SESSION['resend_error'];
    $toastType = 'error';
    unset($_SESSION['resend_error']);
}

// Handle resend
if (isset($_GET['resend']) && $_GET['resend'] === 'true') {
    // $email = $_GET['email'] ?? '';
    require_once 'send_code.php';
    $result = send_login_code($conn, $_SESSION['pending_user_id']);
    if ($result === true) {
        $_SESSION['resend_success'] = "OTP code has been sent!";
    } else {
        $_SESSION['resend_error'] = $result;
    }
    header("Location: verify.php?email=" . urlencode($email));
    exit;
}

// Handle verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = implode('', $_POST['otp'] ?? []);
    $userId = $_SESSION['pending_user_id'];

    $stmt = $conn->prepare("SELECT id, expires_at FROM login_codes WHERE user_id = ? AND code = ? AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("is", $userId, $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($codeId, $expiresAt);
        $stmt->fetch();

        if (strtotime($expiresAt) < time()) {
            $toastMessage = "OTP expired!";
            $toastType = "error";
        } else {
            $update = $conn->prepare("UPDATE login_codes SET is_used = 1 WHERE id = ?");
            if ($update) {
                $update->bind_param("i", $codeId);
                $update->execute();
                $update->close();
            } else {
                die("Prepare failed: " . $conn->error);
            }

            $userStmt = $conn->prepare("SELECT role, user_profile FROM users WHERE id = ?");
            $userStmt->bind_param("i", $userId);
            $userStmt->execute();
            $userStmt->bind_result($role, $userProfile);
            $userStmt->fetch();

            if (empty($role)) {
                $_SESSION['login_error'] = "Your account is registered but not approved yet.";
                header("Location: login.php");
                exit;
            }

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $userId;
            $_SESSION['admin_role'] = $role;
            $_SESSION['admin_profile'] = $userProfile ?: 'assets/uploads/default.png';

            header("Location: ../dashboard.php");
            exit;
        }
    } else {
        $toastMessage = "Invalid OTP!";
        $toastType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
        import { toast } from '../assets/js/toast-utils.js';

        const toastMsg = <?= json_encode($toastMessage) ?>;
        const toastType = <?= json_encode($toastType) ?>;
        if (toastMsg) toast(toastMsg, toastType);
    </script>
</head>

<body class="bg-gray-100 flex justify-center items-center h-screen">
    <form method="POST" id="otpForm" class="bg-white p-6 rounded-lg shadow w-full max-w-sm text-center space-y-4">
        <h2 class="text-xl font-bold">Verify Your Code</h2>
        <!-- <p class="text-sm text-gray-600">We’ve sent a code to your email</p> -->
        <p class="text-sm text-gray-600">
            We’ve sent a code to <span class="font-semibold text-gray-800"><?= htmlspecialchars($email) ?></span>
        </p>

        <!-- 6-Digit OTP Boxes -->
        <div class="flex justify-between gap-2">
            <?php for ($i = 0; $i < 6; $i++): ?>
                <input type="text" name="otp[]" maxlength="1" inputmode="numeric" pattern="\d*" required
                    class="w-10 h-12 text-xl text-center border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-400"
                    oninput="moveNext(this, <?= $i ?>)">
            <?php endfor; ?>
        </div>

        <p class="text-sm text-gray-500" id="countdown">Please wait 60 seconds...</p>
        <!-- <a href="?resend=true" id="resendBtn" class="text-blue-500 font-medium hidden hover:underline">Resend Code</a> -->
        <a href="?resend=true&email=<?= urlencode($email) ?>" id="resendBtn"
            class="text-blue-500 font-medium hidden hover:underline">Resend Code</a>
    </form>

    <script>
        const inputs = document.querySelectorAll('input[name="otp[]"]');

        // Auto move to next input
        function moveNext(current, index) {
            if (current.value.length === 1 && index < 5) {
                inputs[index + 1].focus();
            }

            // Submit when all fields filled
            if ([...inputs].every(inp => inp.value.length === 1)) {
                document.getElementById('otpForm').submit();
            }
        }

        // Handle paste
        inputs.forEach((input, idx) => {
            input.addEventListener('paste', e => {
                const data = e.clipboardData.getData('text').trim();
                if (data.length === 6 && /^\d{6}$/.test(data)) {
                    e.preventDefault();
                    data.split('').forEach((num, i) => {
                        inputs[i].value = num;
                    });
                    document.getElementById('otpForm').submit();
                }
            });
        });

        // Countdown logic
        let seconds = 60;
        const countdown = document.getElementById('countdown');
        const resendBtn = document.getElementById('resendBtn');
        const timer = setInterval(() => {
            seconds--;
            countdown.textContent = `Please wait ${seconds} seconds...`;
            if (seconds <= 0) {
                clearInterval(timer);
                countdown.classList.add('hidden');
                resendBtn.classList.remove('hidden');
            }
        }, 1000);
    </script>
</body>

</html>