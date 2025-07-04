<?php
    require_once 'includes/header.php';
    require_once 'includes/functions.php';
    require_once 'components/disable-action.php';

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

    // ✅ 1. Replace static SQL with pagination-aware SQL
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $offset = ($page - 1) * $limit;

    $searchSql = $search ? "WHERE username LIKE '%$search%' OR email LIKE '%$search%' OR full_name LIKE '%$search%'" : '';
    $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM users $searchSql");
    $totalRows = $totalQuery->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    // Fetch users
    $users = $conn->query("SELECT * FROM users $searchSql ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>
       
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
            <!-- Add User Modal -->
            <div id="addUserModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 invisible">
                <!-- Modal Content -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-xl w-full transform transition-transform scale-95">
                    <!-- <button id="closeModalBtn" class="absolute top-2 right-2 text-gray-500 hover:text-red-500">✖</button> -->
                    <form id="userForm" method="post" enctype="multipart/form-data" class="grid grid-cols-1 gap-4" data-mode="<?= isset($_GET['edit']) ? 'update' : 'add' ?>">
                        <h2 class="text-2xl font-bold"> <?= isset($_GET['edit']) ? 'Update User' : 'Add New User' ?> </h2>
                        
                        <!-- ✅ Toast Notification -->
                        <?php if (!empty($success) || !empty($error)): ?>
                            <div id="toast" class="fixed top-5 right-5 z-50 px-4 py-3 rounded shadow-lg transition-all duration-300">
                                <?= htmlspecialchars($success ?: $error) ?>
                            </div>
                        <?php endif; ?>

                        <input type="hidden" name="id" value="<?= $_GET['edit'] ?? '' ?>">

                        <!-- Text Inputs -->
                        <div class="grid grid-cols-2 gap-x-4">
                            <label class="flex flex-col">
                                <span class="text-gray-700 dark:text-gray-300 after:ml-0.5 after:text-red-500 after:content-['*']">Username</span>
                                <input class="dark:bg-gray-700 border dark:border-gray-600 p-2 rounded" type="text" name="username" placeholder="Username" value="<?= $_GET['username'] ?? '' ?>" required autofocus>
                            </label>
                            <label class="flex flex-col">
                                <span class="text-gray-700 dark:text-gray-300 after:ml-0.5 after:text-red-500 after:content-['*']">Full Name</span>
                                <input class="dark:bg-gray-700 border dark:border-gray-600 p-2 rounded" type="text" name="full_name" placeholder="Your Name" value="<?= $_GET['full_name'] ?? '' ?>" required>
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4">
                            <label class="flex flex-col">
                                <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Email</span>
                                <input class="dark:bg-gray-700 border dark:border-gray-600 p-2 rounded" type="email" name="email" placeholder="you@example.com" value="<?= $_GET['email'] ?? '' ?>" required>
                            </label>

                            <?php
                                $selectedRole = $_GET['role'] ?? 'user';
                                $editingOwnAccount = isset($_GET['edit']) && $_GET['edit'] == $_SESSION['admin_id'];
                            ?>
                            <label class="flex flex-col">
                                <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Role</span>
                                <select id="edit-role" name="role"
                                    class="dark:bg-gray-700 border dark:border-gray-600 p-2 rounded"
                                    <?= $editingOwnAccount ? 'disabled title="You cannot change your own role."' : '' ?>
                                    required>
                                    <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="user" <?= $selectedRole === 'user' ? 'selected' : '' ?>>User</option>
                                </select>

                                <?php if ($editingOwnAccount): ?>
                                    <!-- Hidden input to preserve current role (since disabled selects are not submitted) -->
                                    <input type="hidden" name="role" value="<?= htmlspecialchars($selectedRole) ?>">
                                <?php endif; ?>
                            </label>

                        </div>

                        <!-- Password field shown only in add mode -->
                        <?php if (!isset($_GET['edit'])): ?>
                            <label class="flex flex-col">
                                <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Password</span>
                                <input class="dark:bg-gray-700 border dark:border-gray-600 p-2 rounded flex-1" type="password" name="password" placeholder="Password" required minlength="6">
                            </label>
                        <?php endif; ?>

                        <input type="hidden" name="existing_profile" value="<?= $_GET['user_profile'] ?? '' ?>">

                        <!-- Upload Box -->
                        <div id="uploadBox"
                            class="dark:bg-gray-700 dark:border-gray-600 border-2 border-dashed border-gray-300 bg-gray-50 text-center rounded-lg cursor-pointer hover:bg-gray-100 transition <?= isset($_GET['user_profile']) ? 'hidden' : '' ?>">
                            <label for="user_profile" class="block p-6 cursor-pointer">
                                <span class="block text-gray-700 dark:text-gray-300">Drag & drop your files here or
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
                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" id="closeModalBtn" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                            <?php if (isset($_GET['edit'])): ?>
                                    <button type="submit" name="update_user" data-action="update_user" class="bg-yellow-600 text-white px-4 py-2 rounded">Update User</button>
                            <?php else: ?>
                                <button type="submit" name="add_user" data-action="add_user" class="bg-blue-600 text-white px-4 py-2 rounded">Add User</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="flex flex-row mb-6">
                <h3 class="flex-1 text-2xl font-semibold mb-2">Users List Management</h3>
                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                <button id="openModalBtn" class="w-26 flex-none bg-green-500 text-white text-sm px-4 py-2 rounded hover:bg-green-600">
                    <i class="fa-solid fa-user-plus mr-1"></i> Add New
                </button>
                <?php endif; ?>
            </div>

            <!-- ✅ 2. Add search + per page -->
            <div class="flex justify-between mb-4">
                <div class="border px-3 py-1 rounded focus:outline-2 focus:outline-offset-2 focus:outline-violet-500">
                    <label for="searchInput"><i class="fa-solid fa-filter"></i></label>
                    <input type="text" id="searchInput" name="searchInput" value="<?= htmlspecialchars($search) ?>" placeholder="Filter user ..." class="dark:bg-transparent ml-2 outline-none focus:outline-none">
                </div>
            </div>

            <table id="userTableBody" class="w-full border border-collape">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-2 border dark:border-gray-600">Username</th>
                        <th class="p-2 border dark:border-gray-600">Email</th>
                        <th class="p-2 border dark:border-gray-600">Full Name</th>
                        <th class="p-2 border dark:border-gray-600">Profile</th>
                        <th class="p-2 border dark:border-gray-600">Role</th>
                        <th class="p-2 border dark:border-gray-600">Created At</th>
                        <th class="p-2 border dark:border-gray-600">Updated At</th>
                        <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                        <th class="p-2 border dark:border-gray-600">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <?php
                            $viewingDefaultAdmin = $user['is_default_admin'] == 1;
                            $loggedInUserIsDefaultAdmin = isset($_SESSION['is_default_admin']) && $_SESSION['is_default_admin'] == 1;
                        ?>
                        <tr>
                            <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($user['username']) ?></td>
                            <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($user['full_name']) ?></td>
                            <td class="p-2 border dark:border-gray-600">
                                <img src="<?= htmlspecialchars($user['user_profile'] ?: 'assets/uploads/default.png') ?>"
                                    alt="Profile" class="w-10 h-10 rounded-full mx-auto object-cover">
                            </td>
                            <td class="p-2 border dark:border-gray-600 text-center">
                                <?php if (empty($user['role'])): ?>
                                    <form method="POST" class="flex items-center justify-center gap-2">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <?php if ($_SESSION['admin_role'] !== 'admin'):?>
                                            <select name="assign_role" class="hidden border p-1 rounded">
                                                <option value="user">User</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        <?php else: ?>
                                            <select name="assign_role" class="border p-1 rounded">
                                                <option value="user">User</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['admin_role'] !== 'admin'):?>
                                            <span class="text-sm px-2 py-1 rounded bg-yellow-100 text-yellow-600">Admin not approve yet!</span>
                                            <button type="submit" name="assign_role_submit" class="hidden bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Assign</button> 
                                        <?php else: ?>
                                            <button type="submit" name="assign_role_submit" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Assign</button> 
                                        <?php endif; ?>
                                    </form>
                                <?php else: ?>
                                    <?php if ($viewingDefaultAdmin):?>
                                        <span class="text-sm px-2 py-1 rounded bg-green-100 text-green-600">Super <?= htmlspecialchars($user['role']) ?></span>
                                    <?php elseif ($user['role'] === 'admin'): ?>
                                        <span class="text-sm px-2 py-1 rounded bg-yellow-100 text-yellow-600"><?= htmlspecialchars($user['role']) ?></span>
                                    <?php else: ?>
                                        <span class="text-sm px-2 py-1 rounded bg-orange-100 text-orange-600"><?= htmlspecialchars($user['role']) ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="p-2 border dark:border-gray-600"><?= $user['created_at'] ?></td>
                            <td class="p-2 border dark:border-gray-600"><?= $user['updated_at'] ?? '—' ?></td>
                            <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                                <td class="p-2 border dark:border-gray-600 text-center">
                                    <?php if ($viewingDefaultAdmin && !$loggedInUserIsDefaultAdmin): ?>
                                        <button class="inline-block text-sm px-2 py-1 mr-2 rounded bg-orange-100 text-orange-600 opacity-50 cursor-not-allowed" disabled title="Only default admin can edit this user">
                                            <i class="fa-solid fa-user-pen"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="users.php?edit=<?= $user['id'] ?>&username=<?= urlencode(trim($user['username'])) ?>&email=<?= urlencode(trim($user['email'])) ?>&full_name=<?= urlencode(trim($user['full_name'])) ?>&user_profile=<?= urlencode(trim($user['user_profile'] ?? '')) ?>&role=<?= urlencode(trim($user['role'] ?? '')) ?>&is_default_admin=<?= $user['is_default_admin'] ?>"
                                        class="inline-block text-sm px-2 py-1 mr-2 rounded bg-orange-100 text-orange-600">
                                            <i class="fa-solid fa-user-pen"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($user['is_default_admin']): ?>
                                        <button class="text-sm px-2 py-1 rounded bg-red-100 text-red-600 opacity-50 cursor-not-allowed" disabled title="Default admin cannot be deleted">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <?php
                                            $isViewingSelf = $_SESSION['admin_id'] == $user['id'];
                                            $isDefaultAdmin = $user['is_default_admin'];
                                        ?>
                                        <?php if ($isDefaultAdmin || $isViewingSelf): ?>
                                            <button class="text-sm px-2 py-1 rounded bg-red-100 text-red-600 opacity-50 cursor-not-allowed"
                                                disabled
                                                title="<?= $isDefaultAdmin ? 'Default admin cannot be deleted' : 'You cannot delete your own account' ?>">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button data-delete-id="<?= $user['id'] ?>" class="text-sm px-2 py-1 rounded bg-red-100 text-red-600">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- ✅ 3. Update pagination controls -->
            <div class="flex justify-between items-center mt-4 border-t">
                <div><?php include 'components/back-button.php'; ?></div>
                <div class="flex items-center justify-end gap-x-4">
                    <div class="flex items-center">
                        <p class="text-base text-gray-500 mr-3">Rows per page:</p>
                        <select id="perPage" class="bg-transparent py-4 pr-6 focus:outline-none cursor-pointer">
                            <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= $limit == 30 ? 'selected' : '' ?>>30</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-x-4">
                        <?php
                            $start = $offset + 1;
                            $end = min($offset + $limit, $totalRows);
                        ?>
                        <p class="text-base text-gray-500"><?= $start ?>-<?= $end ?> of <?= $totalRows ?></p>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 border rounded <?= $i == $page ? 'bg-blue-600 text-white' : 'hover:bg-gray-200' ?>"> <?= $i ?> </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div id="deleteConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white p-6 text-left rounded-xl shadow max-w-sm w-full">
                    <h2 class="text-xl font-semibold mb-4">
                        Delete user?
                    </h2>
                    <p class="mb-1">Are you sure you want to delete this user?</p>
                    <p class="mb-4">This will delete <strong></strong>.</p>
                    <div class="flex justify-end gap-4">
                        <button id="cancelDeleteBtn" class="border border-gray-300 hover:bg-gray-300/50 px-4 py-2 rounded-full">Cancel</button>
                        <button id="confirmDeleteBtn" class="border border-red-500 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full">Delete</button>
                    </div>
                </div>
            </div>

            <!-- Toast -->
            <!-- <div id="toastSuccess" class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-lg hidden transition">User deleted successfully.</div> -->
        </div>
     </main>
</div>

    <script type="module">
        import { initImagePreview, initCancelButton, initUpdateButtonDisable } from './assets/js/form-utils.js';
        import { setupDeleteModal } from './assets/js/delete-utils.js';
        import { handleUserFormAjax } from './assets/js/user-form-utils.js';
        import { toast } from './assets/js/toast-utils.js';

        // Form user
        handleUserFormAjax('#addUserModal form', 'user-handler.php');

        // Delete user
        setupDeleteModal({
            endpoint: 'user-handler.php'
        });

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

        const urlParams = new URLSearchParams(window.location.search);
        const isEditMode = urlParams.has('edit');
        const modal = document.getElementById('addUserModal');
        const form = modal.querySelector('form');
        const usernameInput = form.querySelector('input[name="username"]');

        function showModal() {
            modal.classList.remove('invisible', 'opacity-0');
            modal.classList.add('visible', 'opacity-100');
            setTimeout(() => {
                usernameInput.focus();
            }, 100);
        }

        function hideModal() {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('invisible');

                // Remove edit & add params
                const url = new URL(window.location);
                ['edit', 'username', 'email', 'full_name', 'user_profile', 'role', 'add'].forEach(p => url.searchParams.delete(p));
                window.location.href = url.toString();
            }, 300);
        }

        document.getElementById('openModalBtn').addEventListener('click', () => {
            const url = new URL(window.location);
            url.searchParams.delete('edit');
            url.searchParams.delete('username');
            url.searchParams.delete('email');
            url.searchParams.delete('full_name');
            url.searchParams.delete('user_profile');
            url.searchParams.delete('role');
            url.searchParams.set('add', '1'); // ✅ tell the page to open modal
            window.location.href = url.toString(); // reload with clean + add
        });

        document.getElementById('closeModalBtn').addEventListener('click', hideModal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) hideModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') hideModal();
        });

        const isAddMode = urlParams.has('add');

        if (isEditMode || isAddMode) {
            setTimeout(() => showModal(), 60);
        }

        //✅ 4. Add JavaScript for real-time filter & limit
        document.getElementById('searchInput').addEventListener('input', () => {
            const search = document.getElementById('searchInput').value;
            const limit = document.getElementById('perPage').value;
            window.location.href = `?search=${encodeURIComponent(search)}&limit=${limit}&page=1`;
        });

        document.getElementById('perPage').addEventListener('change', () => {
            const search = document.getElementById('searchInput').value;
            const limit = document.getElementById('perPage').value;
            window.location.href = `?search=${encodeURIComponent(search)}&limit=${limit}&page=1`;
        });

        // ✅ Disable role dropdown if default admin
        const roleField = document.getElementById("edit-role");
        const isDefaultAdmin = urlParams.get("is_default_admin");

        if (isEditMode && isDefaultAdmin === "1" && roleField) {
            roleField.setAttribute("disabled", "disabled");
            roleField.title = "Default admin role cannot be changed";
        }

        <?php if (!empty($success) || !empty($error)): ?>
            toast("<?= htmlspecialchars($success ?: $error) ?>", "<?= $success ? 'success' : 'error' ?>");
        <?php endif; ?>
    </script>
<?php include 'includes/footer.php'; ?>
