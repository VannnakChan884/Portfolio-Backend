<?php include 'includes/header.php'; ?>
<?php
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
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>
        <div class="max-w-full mx-auto bg-white p-6 rounded shadow">
            <?php include 'components/back-button.php'; ?>

            <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 my-6 max-w-3xl">
                <h2 class="text-2xl font-bold">Manage Projects (<?= strtoupper($lang) ?>)</h2>
                <input type="hidden" name="lang" value="<?= $lang ?>">
                <?php if ($editProject): ?>
                    <input type="hidden" name="id" value="<?= $editProject['id'] ?>">
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-4">
                    <label class="flex flex-col">
                        <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Project</span>
                        <input class="border p-2 rounded" type="text" name="title" placeholder="Project Title" required value="<?= $editProject['title'] ?? '' ?>">
                    </label>
                    <label class="flex flex-col">
                        <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Link</span>
                        <input class="border p-2 rounded" type="text" name="project_link" placeholder="Project Link" value="<?= $editProject['project_link'] ?? '' ?>">
                    </label>
                </div>
                <label class="flex flex-col">
                    <span class="text-gray-700 after:ml-0.5 after:text-red-500 after:content-['*']">Description</span>
                    <textarea class="border p-2 rounded" name="description" rows="3" placeholder="Description"><?= $editProject['description'] ?? '' ?></textarea>
                </label>

                <?php if (!empty($editProject['image'])): ?>
                    <img src="<?= $editProject['image'] ?>" width="100" class="rounded shadow">
                <?php endif; ?>

                <!-- Upload Box -->
                <div id="uploadBox"
                    class="border-2 border-dashed border-gray-300 bg-gray-50 text-center rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <label for="image" class="block p-6 cursor-pointer">
                        <span class="block text-gray-700">Drag & drop your files here or
                            <span class="text-blue-600 underline">browse</span></span>
                        <input id="image" type="file" name="image" accept="image/*" class="hidden">
                        <p class="text-xs text-gray-500 mt-2">File must be .jpg .jpeg .png .gif .bmp</p>
                    </label>
                </div>

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
    </main>
</div>
<?php include 'includes/footer.php'; ?>