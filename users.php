<?php include 'includes/header.php'; ?>
<?php
    require_once 'includes/functions.php';

    // Handle Assign Role
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_role_submit'])) {
        $userId = intval($_POST['user_id']);
        $role = $_POST['assign_role'];

        $stmt = $conn->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $role, $userId);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Role assigned successfully.";
        } else {
            $_SESSION['error'] = "Failed to assign role.";
        }
        header("Location: users.php");
        exit;
    }

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
            header("Location: users.php?edit={$_POST['id']}&username=" . urlencode($_POST['username']) . "&email=" . urlencode($_POST['email']) . "&full_name=" . urlencode($_POST['full_name']) . "&user_profile=" . urlencode($_POST['existing_profile']) . "&role=" . urlencode($_POST['role']));
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
    $users = $conn->query("SELECT id, username, email, full_name, user_profile, role, created_at, updated_at FROM users ORDER BY created_at DESC");
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>
       
        <div class="max-w-full mx-auto bg-white p-6 rounded shadow">
            <?php include 'components/back-button.php'; ?>

            <form method="POST" enctype="multipart/form-data" class="my-6 grid grid-cols-1 gap-4 max-w-3xl">
                <h2 class="text-2xl font-bold">Manage Admin Users</h2>
                <!-- Notifications -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div id="successMessage" class="bg-green-500 text-white p-3 rounded mb-4">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); endif; ?>

                <?php if (isset($_SESSION['error'])): ?>

                <div id="errorMessage" class="bg-red-500 text-white p-3 rounded mb-4">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); endif; ?>
                <input type="hidden" name="id" value="<?= $_GET['edit'] ?? '' ?>">

                <!-- Text Inputs -->
                <div class="grid grid-cols-2 gap-x-4">
                    <label class="flex flex-col">
                        <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Username</span>
                        <input class="border p-2 rounded" type="text" name="username" placeholder="Username" value="<?= $_GET['username'] ?? '' ?>" required>
                    </label>
                    <label class="flex flex-col">
                        <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Full Name</span>
                        <input class="border p-2 rounded" type="text" name="full_name" placeholder="Your Name" value="<?= $_GET['full_name'] ?? '' ?>" required>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-x-4">
                    <label class="flex flex-col">
                        <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Email</span>
                        <input class="border p-2 rounded" type="email" name="email" placeholder="you@example.com" value="<?= $_GET['email'] ?? '' ?>" required>
                    </label>
                
                    <?php
                        // Default role for form value
                        $selectedRole = $_GET['role'] ?? 'user';
                    ?>
                    <label class="flex flex-col">
                        <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Role</span>
                        <select name="role" class="border p-2 rounded" required>
                            <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="user" <?= $selectedRole === 'user' ? 'selected' : '' ?>>User</option>
                        </select>
                    </label>
                </div>

                <input type="hidden" name="existing_profile" value="<?= $_GET['user_profile'] ?? '' ?>">

                <!-- Upload Box -->
                <div id="uploadBox"
                    class="border-2 border-dashed border-gray-300 bg-gray-50 text-center rounded-lg cursor-pointer hover:bg-gray-100 transition <?= isset($_GET['user_profile']) ? 'hidden' : '' ?>">
                    <label for="user_profile" class="block p-6 cursor-pointer">
                        <span class="block text-gray-700">Drag & drop your files here or
                            <span class="text-blue-600 underline">browse</span></span>
                        <input id="user_profile" type="file" name="user_profile" accept="image/*" class="hidden">
                        <p class="text-xs text-gray-500 mt-2">File must be .jpg .jpeg .png .gif .bmp</p>
                    </label>
                </div>

                <!-- Image Preview + Remove -->
                <div id="previewContainer" class="relative mt-2 w-max <?= isset($_GET['user_profile']) ? '' : 'hidden' ?>">
                    <img id="imagePreview" src="<?= $_GET['user_profile'] ?? '' ?>" alt="Preview"
                        class="w-40 h-40 object-cover rounded-lg">
                    <button type="button" id="removeImageBtn"
                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full text-xs flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-white"></i>
                    </button>
                </div>

                <!-- Submit/Update + Cancel -->
                <div class="flex gap-3 mt-4">
                    <?php if (isset($_GET['edit'])): ?>
                        <button name="update_user" type="submit" class="bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700 disabled:opacity-50 disabled:pointer-events-none">Update User</button>
                    <?php else: ?>
                        <!-- Password field shown only in add mode -->
                        <input class="border p-2 rounded flex-1" type="password" name="password" placeholder="Password" required minlength="6">
                        <!-- File Button (visible only after upload or image already exists) -->
                        <div id="fileActionBtn" class="<?= isset($_GET['user_profile']) ? '' : 'hidden' ?>">
                            <?php if (!isset($_GET['edit'])): ?>
                                <button name="add_user" type="submit"
                                    class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Add User</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <a href="users.php" id="cancelBtn"
                        class="bg-gray-400 text-white py-2 px-4 rounded hover:bg-gray-500 opacity-50 pointer-events-none">
                        Cancel
                    </a>
                </div>
            </form>

            <h3 class="text-lg font-semibold mb-2">Existing Users</h3>
            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">Username</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Full Name</th>
                        <th class="p-2 border">Profile</th>
                        <th class="p-2 border">Role</th>
                        <th class="p-2 border">Created At</th>
                        <th class="p-2 border">Updated At</th>
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
                                <img src="<?= htmlspecialchars($user['user_profile'] ?: 'assets/uploads/default.png') ?>"
                                    alt="Profile" class="w-10 h-10 rounded-full mx-auto object-cover">
                            </td>
                            <td class="p-2 border text-center">
                                <?php if (empty($user['role'])): ?>
                                    <form method="POST" class="flex items-center gap-2">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <select name="assign_role" class="border p-1 rounded">
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                        <button type="submit" name="assign_role_submit"
                                            class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Assign</button>
                                    </form>
                                <?php else: ?>
                                    <span
                                        class="text-sm px-2 py-1 rounded bg-blue-100 text-blue-600"><?= htmlspecialchars($user['role']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-2 border"><?= $user['created_at'] ?></td>
                            <td class="p-2 border"><?= $user['updated_at'] ?? '—' ?></td>
                            <td class="p-2 border text-center">
                                <a href="users.php?edit=<?= $user['id'] ?>&username=<?= urlencode(trim($user['username'])) ?>&email=<?= urlencode(trim($user['email'])) ?>&full_name=<?= urlencode(trim($user['full_name'])) ?>&user_profile=<?= urlencode(trim($user['user_profile'])) ?>&role=<?= urlencode(trim($user['role'])) ?>"
                                    class="inline-block  text-sm px-2 py-1 mr-2 rounded bg-orange-100 text-orange-600">
                                    <i class="fa-solid fa-user-pen"></i>
                                </a>
                                <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Are you sure? You want to delete this user?')"
                                    class="inline-block text-sm px-2 py-1 rounded bg-red-100 text-red-600">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
     </main>
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

        const dropArea = document.getElementById('uploadBox');
        const fileInput = document.getElementById('user_profile');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        // Highlight on dragover
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.add('bg-blue-50', 'border-blue-500');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.remove('bg-blue-50', 'border-blue-500');
            });
        });

        // Handle dropped files
        dropArea.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type.startsWith('image/')) {
                fileInput.files = files;

                // Show preview
                const reader = new FileReader();
                reader.onload = (event) => {
                    document.getElementById('imagePreview').src = event.target.result;
                    document.getElementById('previewContainer').classList.remove('hidden');
                    document.getElementById('fileActionBtn').classList.remove('hidden');
                    dropArea.classList.add('hidden');
                };
                reader.readAsDataURL(files[0]);
            }
        });
    </script>
<?php include 'includes/footer.php'; ?>