<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: auth/login.php");
    exit();
}

$lang = $_GET['lang'] ?? 'en';
$search = $_GET['search'] ?? '';

// Pagination variables
$itemsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $itemsPerPage;

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_skill'])) {
    $title = trim($_POST['title']);
    $percentage = intval($_POST['percentage']);

    $stmt = $conn->prepare("INSERT INTO skills (title, percentage, lang) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $title, $percentage, $lang);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Skill added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add skill.";
    }
    header("Location: skills.php?lang=$lang");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM skills WHERE id = $id")) {
        $_SESSION['success'] = "Skill deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete skill.";
    }
    header("Location: skills.php?lang=$lang");
    exit;
}

// Handle Edit
$editSkill = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->prepare("SELECT * FROM skills WHERE id = ?");
    $result->bind_param("i", $id);
    $result->execute();
    $editSkill = $result->get_result()->fetch_assoc();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_skill'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $percentage = intval($_POST['percentage']);

    $stmt = $conn->prepare("UPDATE skills SET title = ?, percentage = ? WHERE id = ?");
    $stmt->bind_param("sii", $title, $percentage, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Skill updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update skill.";
    }
    header("Location: skills.php?lang=$lang");
    exit;
}

// Count total skills for pagination
if ($search) {
    $searchParam = "%$search%";
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM skills WHERE lang = ? AND title LIKE ?");
    $countStmt->bind_param("ss", $lang, $searchParam);
} else {
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM skills WHERE lang = ?");
    $countStmt->bind_param("s", $lang);
}
$countStmt->execute();
$countStmt->bind_result($totalSkills);
$countStmt->fetch();
$countStmt->close();

$totalPages = ceil($totalSkills / $itemsPerPage);

// Fetch paginated skills
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM skills WHERE lang = ? AND title LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $lang, $searchParam, $itemsPerPage, $offset);
} else {
    $stmt = $conn->prepare("SELECT * FROM skills WHERE lang = ? ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $lang, $itemsPerPage, $offset);
}
$stmt->execute();
$skills = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Skills</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-4">Manage Skills (<?= strtoupper(htmlspecialchars($lang)) ?>)</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-500 text-white p-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-500 text-white p-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

    <!-- Skill Form -->
    <form method="post" class="grid grid-cols-1 gap-4 mb-6">
        <input type="hidden" name="lang" value="<?= htmlspecialchars($lang) ?>">
        <?php if ($editSkill): ?>
            <input type="hidden" name="id" value="<?= $editSkill['id'] ?>">
        <?php endif; ?>

        <input class="border p-2 rounded" type="text" name="title" placeholder="Skill Title" required
               value="<?= htmlspecialchars($editSkill['title'] ?? '') ?>">
        <input class="border p-2 rounded" type="number" name="percentage" placeholder="Percentage (0-100)" required
               value="<?= htmlspecialchars($editSkill['percentage'] ?? '') ?>">
        <button name="<?= $editSkill ? 'update_skill' : 'add_skill' ?>" type="submit"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
            <?= $editSkill ? 'Update Skill' : 'Add Skill' ?>
        </button>
    </form>

    <!-- Search Bar -->
    <form method="get" class="mb-4 flex flex-col sm:flex-row sm:items-center sm:space-x-4">
        <input type="hidden" name="lang" value="<?= htmlspecialchars($lang) ?>">
        <input class="border p-2 rounded w-full max-w-sm mb-2 sm:mb-0" type="text" name="search" placeholder="Search skills..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 whitespace-nowrap">Search
        </button>
    </form>

    <!-- Skills Table -->
    <table class="w-full border">
        <thead class="bg-gray-100">
        <tr>
            <th class="p-2 border">Title</th>
            <th class="p-2 border">Percentage</th>
            <th class="p-2 border">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $skills->fetch_assoc()): ?>
            <tr class="text-center border-b <?= $editSkill && $editSkill['id'] == $row['id'] ? 'bg-yellow-100' : '' ?>">
                <td class="p-2 border"><?= htmlspecialchars($row['title']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($row['percentage']) ?>%</td>
                <td class="p-2 border">
                    <a href="?edit=<?= $row['id'] ?>&lang=<?= htmlspecialchars($lang) ?>" class="text-blue-600 hover:underline">Edit</a>
                    |
                    <a href="?delete=<?= $row['id'] ?>&lang=<?= htmlspecialchars($lang) ?>" onclick="return confirm('Are you sure?')"
                       class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center space-x-2">
        <?php if ($page > 1): ?>
            <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>"
               class="px-3 py-1 border rounded hover:bg-gray-200">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>"
               class="px-3 py-1 border rounded <?= $i === $page ? 'bg-blue-600 text-white' : 'hover:bg-gray-200' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>"
               class="px-3 py-1 border rounded hover:bg-gray-200">Next &raquo;</a>
        <?php endif; ?>
    </div>

    <!-- Language Switch -->
    <div class="mt-4">
        <p>Switch Language:
            <a href="?lang=en&search=<?= urlencode($search) ?>&page=1" class="text-blue-500">English</a> |
            <a href="?lang=kh&search=<?= urlencode($search) ?>&page=1" class="text-blue-500">Khmer</a> |
            <a href="?lang=zh&search=<?= urlencode($search) ?>&page=1" class="text-blue-500">Chinese</a>
        </p>
        <a href="dashboard.php" class="inline-block mt-2 text-gray-600 hover:underline">&larr; Back to Dashboard</a>
    </div>

</div>
</body>
</html>
