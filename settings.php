<?php include 'includes/header.php'; ?>
<?php
    require_once 'includes/functions.php';

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
        <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">General Settings</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-600 p-3 rounded mb-4">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4 my-6">
                <input type="text" name="site_title" placeholder="Site Title" value="<?= $settings['site_title'] ?? '' ?>" class="w-full p-2 border rounded">

                <input type="email" name="email" placeholder="Email" value="<?= $settings['email'] ?? '' ?>" class="w-full p-2 border rounded">

                <input type="text" name="phone" placeholder="Phone" value="<?= $settings['phone'] ?? '' ?>" class="w-full p-2 border rounded">

                <textarea name="description" placeholder="Site Description" class="w-full p-2 border rounded"><?= $settings['description'] ?? '' ?></textarea>

                <input type="text" name="facebook" placeholder="Facebook URL" value="<?= $settings['facebook'] ?? '' ?>" class="w-full p-2 border rounded">

                <input type="text" name="telegram" placeholder="Telegram URL" value="<?= $settings['telegram'] ?? '' ?>" class="w-full p-2 border rounded">

                <input type="text" name="Github" placeholder="Github URL" value="<?= $settings['github'] ?? '' ?>" class="w-full p-2 border rounded">

                <input type="file" name="logo" class="w-full p-2 border rounded">
                <?php if (!empty($settings['logo'])): ?>
                    <img src="<?= $settings['logo'] ?>" alt="Logo" class="w-32 h-auto mt-2">
                <?php endif; ?>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Settings</button>
            </form>
        </div>
    </main>
</div>
<?php include 'includes/footer.php'; ?>