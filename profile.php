<?php
    require_once 'includes/header.php';

    // Fetch current user data
    $userId = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT username, full_name, email, role, user_profile, is_default_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($username, $fullName, $email, $role, $userProfile, $isDefaultAdmin);
    $stmt->fetch();
    $stmt->close();

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newName = trim($_POST['full_name']);
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $updatedImage = $userProfile;
        $updateIsDefaultAdmin = $isDefaultAdmin;

        // Allow default admin to update is_default_admin
        if ($isDefaultAdmin == 1 && isset($_POST['is_default_admin'])) {
            $updateIsDefaultAdmin = 1;
        } elseif ($isDefaultAdmin == 1) {
            $updateIsDefaultAdmin = 0;
        }

        // Handle profile image removal or upload
        $defaultImage = 'assets/uploads/default.png';

        // Remove profile photo
        if (!empty($_POST['remove_profile_photo']) && $_POST['remove_profile_photo'] === '1') {
            if ($userProfile !== $defaultImage && file_exists($userProfile)) {
                unlink($userProfile); // Delete old image
            }
            $updatedImage = $defaultImage;

        // Upload new profile photo
        } elseif (!empty($_FILES['profile_image']['name'])) {
            $targetDir = "assets/uploads/";
            $fileName = time() . '_' . basename($_FILES["profile_image"]["name"]);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
                // Delete old image if not default
                if ($userProfile !== $defaultImage && file_exists($userProfile)) {
                    unlink($userProfile);
                }
                $updatedImage = $targetFile;
            }
        }

        if (!empty($newPassword) && $newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, user_profile = ?, password = ?, is_default_admin = ? WHERE id = ?");
            $stmt->bind_param("sssii", $newName, $updatedImage, $hashedPassword, $updateIsDefaultAdmin, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, user_profile = ?, is_default_admin = ? WHERE id = ?");
            $stmt->bind_param("ssii", $newName, $updatedImage, $updateIsDefaultAdmin, $userId);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully.";
            $_SESSION['admin_profile'] = $updatedImage;
        }
        $stmt->close();
        header("Location: profile.php");
        exit;
    }
?>

<div class="flex min-h-screen">
    <?php include 'includes/sidebar.php'; ?>

    <main class="flex-1 p-6 bg-gray-100 dark:bg-gray-900">
        <?php include 'includes/topbar.php'; ?>

        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded shadow p-6">
            <h2 class="text-xl font-bold mb-6">Public profile</h2>

            <?php
                $toastMessage = $_SESSION['success'] ?? $_SESSION['error'] ?? '';
                $toastType = isset($_SESSION['success']) ? 'success' : (isset($_SESSION['error']) ? 'error' : '');
                unset($_SESSION['success'], $_SESSION['error']);
            ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <!-- Profile Image Section -->
                <label class="block text-center text-sm font-medium text-gray-700 dark:text-gray-300">Profile picture</label>
                <div class="relative w-48 h-48 group mx-auto">
                    <button type="button" id="profileImageDropdownBtn"
                        class="block w-full h-full focus:outline-none mt-1 hover:opacity-90 transition">
                        <img id="profilePreview"
                            src="<?= htmlspecialchars($userProfile ?: 'assets/uploads/default.png') ?>"
                            alt="Profile"
                            class="w-full h-full object-cover border-2 border-gray-300 rounded-full">
                        <!-- Floating Edit Icon -->
                        <span class="absolute bottom-2 right-2 px-3 py-1 bg-white dark:bg-gray-700 text-xs border rounded-lg shadow">
                            <i class="fa-solid fa-pen text-xs text-gray-700 dark:text-gray-200"></i> Edit
                        </span>
                    </button>

                    <!-- Profile Image Dropdown with proper caret -->
                    <div id="profileImageDropdown"
                        class="hidden absolute top-full mt-1 py-2 right-0 w-52 bg-white dark:bg-gray-800 border dark:border-gray-600 rounded-lg shadow-lg z-50">

                        <!-- Caret / Arrow -->
                        <div class="absolute -top-2 right-12 w-4 h-4 bg-white dark:bg-gray-800 rotate-45 border-t border-l border-gray-200 dark:border-gray-600 z-[-1]"></div>
                        
                        <label for="profileImageInput"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                            <i class="fa-solid fa-upload mr-2"></i> Upload a photo...
                        </label>

                        <button type="button" id="removePhotoBtn"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900 dark:text-red-400">
                            <i class="fa-solid fa-trash mr-2"></i> Remove photo
                        </button>
                    </div>

                    <!-- Hidden File Input -->
                    <input type="file" id="profileImageInput" name="profile_image" class="hidden" accept="image/*">

                    <!-- Hidden flag to track if user clicked "Remove photo" -->
                    <input type="hidden" id="removeProfilePhoto" name="remove_profile_photo" value="0">

                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                    <input type="text" value="<?= htmlspecialchars($username) ?>" disabled class="w-full mt-1 p-2 bg-gray-100 dark:bg-gray-700 border rounded" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" value="<?= htmlspecialchars($email) ?>" disabled class="w-full mt-1 p-2 bg-gray-100 dark:bg-gray-700 border rounded" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($fullName) ?>" class="w-full mt-1 p-2 border rounded dark:bg-gray-800" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" name="password" class="w-full mt-1 p-2 border rounded dark:bg-gray-800" placeholder="Leave blank to keep your current password" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                    <input type="password" name="confirm_password" class="w-full mt-1 p-2 border rounded dark:bg-gray-800" />
                </div>

                <?php if ($isDefaultAdmin): ?>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_default_admin" value="1" <?= $isDefaultAdmin ? 'checked' : '' ?> class="form-checkbox text-blue-600" />
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Is Default Admin</span>
                    </label>
                </div>
                <?php endif; ?>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Role: <strong><?= htmlspecialchars($role) ?></strong></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">| Default Admin: <strong><?= $isDefaultAdmin ? 'Yes' : 'No' ?></strong></span>
                </div>

                <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save Changes</button>
            </form>

            <!-- Sidebar footer -->
            <footer class="flex flex-row gap-3 items-center py-3 mt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="dashboard.php" class="w-7 h-7 rounded-full overflow-hidden flex items-center justify-center focus:outline-none">
                    <img src="<?= htmlspecialchars($userProfile) ?>" alt="Profile" class="w-full h-full object-cover hover:opacity-80">
                </a>
                <!-- Expanded version (full text) -->
                <span class="text-xs text-gray-500 dark:text-gray-400 capitalize">© <?= date('Y') ?> <?= htmlspecialchars($userName) ?>, Portfolio.</span>
            </footer>
        </div>
    </main>
</div>

<?php if ($toastMessage): ?>
    <script type="module">
        import { toast } from './assets/js/toast-utils.js';
        toast(<?= json_encode($toastMessage) ?>, <?= json_encode($toastType) ?>);
    </script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
