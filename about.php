<?php include 'includes/header.php'; ?>
<?php
// Add/Edit Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $lang = $_POST['lang'] ?? 'en';

    if ($title && $description) {
        if (!empty($_POST['about_id'])) {
            // Edit
            $id = intval($_POST['about_id']);
            $stmt = $conn->prepare("UPDATE about SET title = ?, description = ?, lang = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("sssi", $title, $description, $lang, $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "About section updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update about section.";
            }
        } else {
            // Add
            $stmt = $conn->prepare("INSERT INTO about (title, description, lang) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $description, $lang);
            if ($stmt->execute()) {
                $_SESSION['success'] = "About section added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add about section.";
            }
        }
    } else {
        $_SESSION['error'] = "All fields are required.";
    }

    header("Location: about.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete_about'])) {
    $id = intval($_GET['delete_about']);
    $conn->query("DELETE FROM about WHERE id = $id");
    $_SESSION['success'] = "About section deleted.";
    header("Location: about.php");
    exit;
}

// Handle Edit Data Load
$editData = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM about WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $editData = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Invalid About ID.";
        header("Location: about.php");
        exit;
    }
}

// Fetch all abouts
$aboutResult = $conn->query("SELECT * FROM about ORDER BY created_at DESC");
?>
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php include 'includes/topbar.php'; ?>
        <div class="max-w-full mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
            <?php include 'components/back-button.php'; ?>

            <div class="my-6">
                <h2 class="text-2xl font-bold"><?= $editData ? 'Edit About' : 'Add About' ?> Section</h2>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Add/Edit Form -->
                <form method="POST" class="my-6 space-y-4 max-w-3xl">
                    <?php if ($editData): ?>
                        <input type="hidden" name="about_id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>

                    <input type="text" name="title" placeholder="About Title" required class="w-full dark:bg-gray-700 p-2 border dark:border-gray-600 rounded"
                        value="<?= htmlspecialchars($editData['title'] ?? '') ?>">

                    <textarea name="description" placeholder="Description" class="w-full dark:bg-gray-700 p-2 border dark:border-gray-600 rounded"><?= htmlspecialchars($editData['description'] ?? '') ?></textarea>

                    <select name="lang" class="w-full dark:bg-gray-700 p-2 border dark:border-gray-600 rounded">
                        <option value="en" <?= (isset($editData['lang']) && $editData['lang'] == 'en') ? 'selected' : '' ?>>English</option>
                        <option value="kh" <?= (isset($editData['lang']) && $editData['lang'] == 'kh') ? 'selected' : '' ?>>Khmer</option>
                    </select>

                    <div class="flex gap-4 items-center">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <?= $editData ? 'Update' : 'Add' ?> About
                        </button>
                        <?php if ($editData): ?>
                            <a href="about.php" class="text-gray-600 underline">Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- List of About -->
                <table class="w-full border border-collape text-sm">
                    <thead class="bg-gray-200 dark:bg-gray-700">
                        <tr class="text-left">
                            <th class="p-2 border dark:border-gray-600">Title</th>
                            <th class="p-2 border dark:border-gray-600">Description</th>
                            <th class="p-2 border dark:border-gray-600">Language</th>
                            <th class="text-center p-2 border dark:border-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $aboutResult->fetch_assoc()): ?>
                            <tr class="border-b">
                                <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['description']) ?></td>
                                <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['lang']) ?></td>
                                <td class="text-center p-2 border dark:border-gray-600">
                                    <a href="experiences.php?about_id=<?= $row['id'] ?>" class="inline-block text-sm px-2 py-1 mr-2 rounded bg-green-100 text-green-600 mr-2">
                                        <i class="fa-solid fa-briefcase"></i>
                                    </a>
                                    <a href="?edit=<?= $row['id'] ?>" class="inline-block text-sm px-2 py-1 mr-2 rounded bg-orange-100 text-orange-600">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="?delete_about=<?= $row['id'] ?>" onclick="return confirm('Delete this about?')" class="inline-block text-sm px-2 py-1 rounded bg-red-100 text-red-600">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-sm">
                <p class="mb-2 font-medium">Switch Language:</p>
                <div class="space-x-2">
                    <a href="?lang=en" class="text-blue-600 hover:underline">English</a>
                    <a href="?lang=kh" class="text-blue-600 hover:underline">Khmer</a>
                    <a href="?lang=zh" class="text-blue-600 hover:underline">Chinese</a>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include 'includes/footer.php'; ?>
