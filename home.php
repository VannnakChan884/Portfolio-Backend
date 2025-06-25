<?php include 'includes/header.php'; ?>
<?php
    $lang = $_GET['lang'] ?? 'en'; // default to English

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $bio = trim($_POST['bio']);
        $lang = $_POST['lang'];

        // Image upload
        $imagePath = '';
        if ($_FILES['profile_image']['name']) {
            $targetDir = "assets/uploads/";
            $filename = basename($_FILES["profile_image"]["name"]);
            $imagePath = $targetDir . $filename;

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
                $stmt = $conn->prepare("UPDATE home SET name=?, bio=?, profile_image=? WHERE lang=?");
                $stmt->bind_param("ssss", $name, $bio, $imagePath, $lang);
            } else {
                $stmt = $conn->prepare("UPDATE home SET name=?, bio=? WHERE lang=?");
                $stmt->bind_param("sss", $name, $bio, $lang);
            }
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO home (name, bio, profile_image, lang) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $bio, $imagePath, $lang);
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

            <form method="post" enctype="multipart/form-data" class="space-y-4 max-w-3xl mb-6">
                <h2 class="text-2xl font-bold">Edit Your Home Section</h2>
                <input type="hidden" name="lang" value="<?= $lang ?>">
                <label class="flex flex-col">
                    <span class="text-gray-700 dark:text-gray-300 after:ml-0.5 after:text-red-500 after:content-['*']">Name</span>
                    <input type="text" name="name" value="<?= $data['name'] ?? '' ?>" required class="w-full dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded px-3 py-2">
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

            <div class="flex flex-row gap-4">
                <?php include 'components/back-button.php'; ?>
                <a href="skills.php?home_id=<?= $data['id'] ?>" class="flex items-center justify-center gap-2 inline-block text-sm px-2 py-1 mr-2 rounded bg-green-100 text-green-600 mr-2">
                    <i class="fa-solid fa-briefcase"></i>
                    Manage Your Skill
                </a>
            </div>
        </div>
    </main>
</div>
<?php include 'includes/footer.php'; ?>