<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: auth/login.php");
    exit();
}
require_once 'includes/db.php';

$lang = $_GET['lang'] ?? 'en';

// Add new project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $link = $_POST['project_link'];

    // Upload image
    $imagePath = '';
    if ($_FILES['image']['name']) {
        $targetDir = "assets/uploads/";
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . $filename;
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $stmt = $conn->prepare("INSERT INTO projects (title, description, image, project_link, lang) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $desc, $imagePath, $link, $lang);
    $stmt->execute();
    header("Location: projects.php?lang=$lang");
    exit;
}

// Delete project
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM projects WHERE id = $id");
    header("Location: projects.php?lang=$lang");
    exit;
}

// Edit form load
$editProject = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editProject = $conn->query("SELECT * FROM projects WHERE id = $id")->fetch_assoc();
}

// Update project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $link = $_POST['project_link'];

    // Optional new image
    if ($_FILES['image']['name']) {
        $targetDir = "assets/uploads/";
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . $filename;
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);

        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, image=?, project_link=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $desc, $imagePath, $link, $id);
    } else {
        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, project_link=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $desc, $link, $id);
    }

    $stmt->execute();
    header("Location: projects.php?lang=$lang");
    exit;
}

$projects = $conn->query("SELECT * FROM projects WHERE lang = '$lang'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Projects</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body class="bg-gray-50 p-6 font-sans">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex flex-row gap-x-8 items-center mb-4">
            <?php
                $link = 'dashboard.php';
                $label = 'Back to Dashboard';
                include 'components/back-button.php';
            ?>
            <h2 class="text-2xl font-bold">Manage Projects (<?= strtoupper($lang) ?>)</h2>
        </div>

        <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 mb-6">
            <input type="hidden" name="lang" value="<?= $lang ?>">
            <?php if ($editProject): ?>
                <input type="hidden" name="id" value="<?= $editProject['id'] ?>">
            <?php endif; ?>

            <input class="border p-2 rounded" type="text" name="title" placeholder="Project Title" required value="<?= $editProject['title'] ?? '' ?>">
            <textarea class="border p-2 rounded" name="description" rows="3" placeholder="Description"><?= $editProject['description'] ?? '' ?></textarea>
            <input class="border p-2 rounded" type="text" name="project_link" placeholder="Project Link" value="<?= $editProject['project_link'] ?? '' ?>">

            <?php if (!empty($editProject['image'])): ?>
                <img src="<?= $editProject['image'] ?>" width="100" class="rounded shadow">
            <?php endif; ?>
            <input class="border p-2 rounded" type="file" name="image">

            <button name="<?= $editProject ? 'update_project' : 'add_project' ?>" type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                <?= $editProject ? 'Update Project' : 'Add Project' ?>
            </button>
        </form>

        <h3 class="text-lg font-semibold mb-2">Project List</h3>
        <div class="overflow-x-auto">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">Title</th>
                        <th class="p-2 border">Image</th>
                        <th class="p-2 border">Link</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $projects->fetch_assoc()): ?>
                        <tr class="text-center border-b">
                            <td class="p-2 border"><?= $row['title'] ?></td>
                            <td class="p-2 border">
                                <?php if ($row['image']): ?>
                                    <img src="<?= $row['image'] ?>" width="60" class="mx-auto rounded">
                                <?php endif; ?>
                            </td>
                            <td class="p-2 border"><a href="<?= $row['project_link'] ?>" class="text-blue-600" target="_blank">View</a></td>
                            <td class="p-2 border">
                                <a href="?edit=<?= $row['id'] ?>&lang=<?= $lang ?>" class="text-blue-600 hover:underline">Edit</a> |
                                <a href="?delete=<?= $row['id'] ?>&lang=<?= $lang ?>" onclick="return confirm('Are you sure?')" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <p>Switch Language:
                <a href="?lang=en" class="text-blue-500">English</a> |
                <a href="?lang=kh" class="text-blue-500">Khmer</a> |
                <a href="?lang=zh" class="text-blue-500">Chinese</a>
            </p>
        </div>
    </div>
</body>
</html>