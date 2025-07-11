<?php
    require_once 'includes/header.php';
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
                <div id="uploadBox" class="dark:bg-gray-700 dark:border-gray-600 border-2 border-dashed border-gray-300 bg-gray-50 text-center rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <label for="logo" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="block p-6 opacity-50 cursor-not-allowed"':'class="block p-6 cursor-pointer"'?>>
                        <span class="block text-gray-700 dark:text-gray-300">Drag & drop your files here or
                            <span class="text-blue-600 underline">browse</span>
                        </span>
                        <input id="logo" type="file" name="logo" accept="image/*" <?= $_SESSION['admin_role'] !== 'admin' ? 'disabled class="hidden opacity-50 cursor-not-allowed"':'class="hidden" '?>>
                        <p class="text-xs text-gray-500 mt-2">File must be .jpg .jpeg .png .gif .bmp</p>
                    </label>
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
<?php include 'includes/footer.php'; ?>