<?php include 'includes/header.php'; ?>
<?php
    $lang = $_GET['lang'] ?? 'en'; // default to English

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $title = $_POST['title'];
        $bio = $_POST['bio'];
        $lang = $_POST['lang'];

        // Image upload
        $imagePath = '';
        if ($_FILES['profile_image']['name']) {
            $targetDir = "assets/uploads/";
            $filename = basename($_FILES["profile_image"]["name"]);
            $imagePath = $targetDir . time() . "_" . $filename;

            move_uploaded_file($_FILES["profile_image"]["tmp_name"], $imagePath);
        }

        // Check if data exists for selected language
        $check = $conn->prepare("SELECT id FROM home WHERE lang = ?");
        $check->bind_param("s", $lang);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // Update
            if ($imagePath) {
                $stmt = $conn->prepare("UPDATE home SET name=?, title=?, bio=?, profile_image=? WHERE lang=?");
                $stmt->bind_param("sssss", $name, $title, $bio, $imagePath, $lang);
            } else {
                $stmt = $conn->prepare("UPDATE home SET name=?, title=?, bio=? WHERE lang=?");
                $stmt->bind_param("ssss", $name, $title, $bio, $lang);
            }
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO home (title, description, profile_image, lang) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $title, $imagePath, $lang);
        }

        $stmt->execute();
        echo "<script>alert('home info saved!'); window.location='home.php?lang=$lang';</script>";
        exit;
    }

    // Get current data
    $data = $conn->query("SELECT * FROM home WHERE lang = '$lang'")->fetch_assoc();
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>
        <div class="max-w-full mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
            <?php include 'components/back-button.php'; ?>

            <form method="post" enctype="multipart/form-data" class="space-y-4 max-w-3xl my-6">
                <h2 class="text-2xl font-bold">Edit Home Section (<?= strtoupper($lang) ?>)</h2>
                <input type="hidden" name="lang" value="<?= $lang ?>">
                <label class="flex flex-col">
                    <span class="text-gray-700 dark:text-gray-300 after:ml-0.5 after:text-red-500 after:content-['*']">Name</span>
                    <input type="text" name="name" value="<?= $data['name'] ?? '' ?>" required class="w-full dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-2">
                </label>
                <label class="flex flex-col">
                    <span class="text-gray-700 dark:text-gray-300 after:ml-0.5 after:text-red-500 after:content-['*']">Title</span>
                    <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" required class="w-full dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-2">
                </label>
                <label class="flex flex-col">
                    <span class="text-gray-700 dark:text-gray-300 after:ml-0.5 after:text-red-500">Bio</span>
                    <textarea name="bio" rows="4" class="w-full dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-2"><?= $data['bio'] ?? '' ?></textarea>
                </label>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Profile Image:</label>
                    <?php if (!empty($data['profile_image'])): ?>
                        <img src="<?= $data['profile_image'] ?>" width="100" class="mb-2 rounded">
                    <?php endif; ?>
                    <input type="file" name="profile_image" class="block w-full p-2 rounded dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sm text-gray-500">
                </div>

                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                    Save
                </button>
            </form>

            <div class="mt-6 text-sm">
                <p class="mb-2 font-medium">Switch Language:</p>
                <div class="space-x-2">
                    <a href="?lang=en" class="text-blue-600 hover:underline">English </a>|
                    <a href="?lang=kh" class="text-blue-600 hover:underline">Khmer </a>|
                    <a href="?lang=zh" class="text-blue-600 hover:underline">Chinese</a>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include 'includes/footer.php'; ?>