<?php include 'includes/header.php'; ?>
<?php
    // Handle Add/Edit Experience
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $about_id = intval($_POST['about_id']);
        $title = trim($_POST['title']);
        $company = trim($_POST['company']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $description = trim($_POST['description']);

        if (!empty($_POST['experience_id'])) {
            // Update experience
            $experience_id = intval($_POST['experience_id']);
            $stmt = $conn->prepare("UPDATE experiences SET title=?, company=?, start_date=?, end_date=?, description=?, updated_at=NOW() WHERE id=? AND about_id=?");
            $stmt->bind_param("ssssssi", $title, $company, $start_date, $end_date, $description, $experience_id, $about_id);
            $stmt->execute();
            $_SESSION['success'] = "Experience updated.";
        } else {
            // Add new experience
            $stmt = $conn->prepare("INSERT INTO experiences (about_id, title, company, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $about_id, $title, $company, $start_date, $end_date, $description);
            $stmt->execute();
            $_SESSION['success'] = "Experience added.";
        }

        header("Location: experiences.php?about_id=$about_id");
        exit;
    }

    // Handle delete experience
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $about_id = intval($_GET['about_id']);
        $conn->query("DELETE FROM experiences WHERE id = $id");
        $_SESSION['success'] = "Experience deleted.";
        header("Location: experiences.php?about_id=$about_id");
        exit;
    }

    $about_id = isset($_GET['about_id']) ? intval($_GET['about_id']) : 0;
    $about = $conn->query("SELECT * FROM about WHERE id = $about_id")->fetch_assoc();

    $experiences = $conn->query("SELECT * FROM experiences WHERE about_id = $about_id ORDER BY start_date DESC");

    // Load experience data for editing
    $editExp = null;
    if (isset($_GET['edit'])) {
        $edit_id = intval($_GET['edit']);
        $res = $conn->query("SELECT * FROM experiences WHERE id = $edit_id");
        if ($res && $res->num_rows > 0) {
            $editExp = $res->fetch_assoc();
        }
    }
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>

        <div class="max-w-full mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">
                Manage Experiences for:
                <span class="text-blue-600"><?= htmlspecialchars($about['title']) ?></span>
            </h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); endif; ?>

            <form method="POST" class="my-6 space-y-4 max-w-3xl">
                <input type="hidden" name="about_id" value="<?= htmlspecialchars((string)$about_id) ?>">
                <?php if ($editExp): ?>
                    <input type="hidden" name="experience_id" value="<?= $editExp['id'] ?>">
                <?php endif; ?>
                <input type="text" name="title" placeholder="Position Title" class="w-full dark:bg-gray-700 p-2 border dark:border-gray-800 rounded"
                    value="<?= htmlspecialchars($editExp['title'] ?? '') ?>">

                <input type="text" name="company" placeholder="Company Name" class="w-full dark:bg-gray-700 p-2 border dark:border-gray-800 rounded"
                    value="<?= htmlspecialchars($editExp['company'] ?? '') ?>">

                <div class="grid grid-cols-2 gap-4">
                    <input type="date" name="start_date" class="dark:bg-gray-700 p-2 border dark:border-gray-800 rounded w-full"
                        value="<?= htmlspecialchars($editExp['start_date'] ?? '') ?>">

                    <input type="date" name="end_date" class="dark:bg-gray-700 p-2 border dark:border-gray-800 rounded w-full"
                        value="<?= htmlspecialchars($editExp['end_date'] ?? '') ?>">
                </div>
                <textarea name="description" placeholder="Description" class="w-full dark:bg-gray-700 p-2 border dark:border-gray-800 rounded" rows="3"><?= htmlspecialchars($editExp['description'] ?? '') ?></textarea>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <?= $editExp ? 'Update Experience' : 'Add Experience' ?>
                </button>

                <?php if ($editExp): ?>
                    <a href="experiences.php?about_id=<?= $about_id ?>" class="text-gray-600 underline ml-4">Cancel Edit</a>
                <?php endif; ?>
            </form>

            <h3 class="text-lg font-semibold mb-2">Experiences List</h3>
            <table class="w-full border-collapse border text-sm">
                <thead class="bg-gray-200 dark:bg-gray-700">
                    <tr>
                        <th class="p-2 border border-gray-300 dark:border-gray-500">Title</th>
                        <th class="p-2 border border-gray-300 dark:border-gray-500">Company</th>
                        <th class="p-2 border border-gray-300 dark:border-gray-500">Start</th>
                        <th class="p-2 border border-gray-300 dark:border-gray-500">End</th>
                        <th class="p-2 border border-gray-300 dark:border-gray-500">Description</th>
                        <th class="p-2 border border-gray-300 dark:border-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($exp = $experiences->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="p-2 border dark:border-gray-500"><?= htmlspecialchars($exp['title']) ?></td>
                        <td class="p-2 border dark:border-gray-500"><?= htmlspecialchars($exp['company']) ?></td>
                        <td class="p-2 border dark:border-gray-500"><?= $exp['start_date'] ?></td>
                        <td class="p-2 border dark:border-gray-500"><?= $exp['end_date'] ?></td>
                        <td class="p-2 border dark:border-gray-500"><?= htmlspecialchars($exp['description']) ?></td>
                        <td class="p-2 border dark:border-gray-500 text-center">
                            <a href="experiences.php?edit=<?= $exp['id'] ?>&about_id=<?= $about_id ?>" class="inline-block text-sm px-2 py-1 mr-2 rounded bg-orange-100 text-orange-600">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="?delete=<?= $exp['id'] ?>&about_id=<?= $about_id ?>" onclick="return confirm('Delete this experience?')" class="inline-block text-sm px-2 py-1 rounded bg-red-100 text-red-600">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <div class="mt-6">
                <?php 
                    $link = 'about.php';
                    include "components/back-button.php"; 
                ?>
            </div>
        </div>
    </main>
</div>
<?php include 'includes/footer.php'; ?>
