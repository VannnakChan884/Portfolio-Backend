<?php include 'includes/header.php'; ?>
<?php
    require_once 'includes/functions.php';
    require_once 'components/disable-action.php';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($_POST as $key => $value) {
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->bind_param("ss", $value, $key);
            $stmt->execute();
        }

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $targetDir = "assets/uploads/";
            $logoName = uniqid() . '_' . basename($_FILES['logo']['name']);
            $targetFile = $targetDir . $logoName;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo'");
                $stmt->bind_param("s", $targetFile);
                $stmt->execute();
            }
        }

        $_SESSION['success'] = "Settings updated successfully.";
        header("Location: settings.php");
        exit();
    }

    // Fetch settings
    $settings = [];
    $result = $conn->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>
        <div class="max-w-full mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">General Settings</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-600 p-3 rounded mb-4">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4 my-6 max-w-3xl">
                <input type="text" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded opacity-50 cursor-not-allowed"':''?> name="site_title" placeholder="Site Title" value="<?= $settings['site_title'] ?? '' ?>" class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded">

                <input type="email" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded opacity-50 cursor-not-allowed"':''?> name="email" placeholder="Email" value="<?= $settings['email'] ?? '' ?>" class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded">

                <input type="text" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded opacity-50 cursor-not-allowed"':''?> name="phone" placeholder="Phone" value="<?= $settings['phone'] ?? '' ?>" class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded">

                <textarea name="description" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded opacity-50 cursor-not-allowed"':''?> placeholder="Site Description" class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded"><?= $settings['description'] ?? '' ?></textarea>

                <input type="text" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded opacity-50 cursor-not-allowed"':''?> name="facebook" placeholder="Facebook URL" value="<?= $settings['facebook'] ?? '' ?>" class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded">

                <input type="text" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded opacity-50 cursor-not-allowed"':''?> name="telegram" placeholder="Telegram URL" value="<?= $settings['telegram'] ?? '' ?>" class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded">

                <input type="text" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded opacity-50 cursor-not-allowed"':''?> name="Github" placeholder="Github URL" value="<?= $settings['github'] ?? '' ?>" class="w-full p-2 border dark:bg-gray-700 dark:border-gray-600 rounded">
                <!-- Upload Box -->
                <div id="uploadBox" class="dark:bg-gray-700 dark:border-gray-600 border-2 border-dashed border-gray-300 bg-gray-50 text-center rounded-lg cursor-pointer hover:bg-gray-100 transition <?= isset($_GET['logo']) ? 'hidden' : '' ?>">
                    <label for="logo" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="block p-6 opacity-50 cursor-not-allowed"':'class="block p-6 cursor-pointer"'?>>
                        <span class="block text-gray-700 dark:text-gray-300">Drag & drop your files here or
                            <span class="text-blue-600 underline">browse</span>
                        </span>
                        <input id="logo" type="file" name="logo" accept="image/*" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="hidden opacity-50 cursor-not-allowed"':'class="hidden" '?>>
                        <p class="text-xs text-gray-500 mt-2">File must be .jpg .jpeg .png .gif .bmp</p>
                    </label>
                </div>

                <!-- Image Preview + Remove -->
                <div id="previewContainer" class="relative mt-2 w-max <?= isset($_GET['logo']) ? '' : 'hidden' ?>">
                    <img id="imagePreview" src="<?= $_GET['logo'] ?? '' ?>" alt="Preview" class="w-40 h-40 object-cover rounded-lg">
                    <button type="button" id="removeImageBtn" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full text-xs flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-white"></i>
                    </button>
                </div>

                <?php if (!empty($settings['logo'])): ?>
                    <img src="<?= $settings['logo'] ?>" alt="Logo" class="w-32 h-auto mt-2">
                <?php endif; ?>

                <?php
                    if ($_SESSION['admin_role'] !== 'admin') {
                        renderDisabledAction('Update', 'Only admins can edit users', 'fas fa-edit', false);
                    } else {
                        // Actual Edit buttons
                        echo '<button type="submit" class="flex justify-end bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-edit"></i> Update</button>';
                    }
                ?>

            </form>
        </div>
    </main>
</div>
<script type="module">
        import { initImagePreview, initCancelButton, initUpdateButtonDisable } from './assets/js/form-utils.js';

        initImagePreview('input[name="user_profile"]', '#imagePreview', '#removeImageBtn');
        initCancelButton('form', '#cancelBtn');

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