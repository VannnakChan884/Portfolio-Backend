<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: auth/login.php");
    exit();
}
require_once 'includes/db.php';

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
        $stmt = $conn->prepare("INSERT INTO home (name, title, bio, profile_image, lang) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $title, $bio, $imagePath, $lang);
    }

    $stmt->execute();
    echo "<script>alert('home info saved!'); window.location='home.php?lang=$lang';</script>";
    exit;
}

// Get current data
$data = $conn->query("SELECT * FROM home WHERE lang = '$lang'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Home - <?= strtoupper($lang) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-6">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex flex-row gap-x-8 items-center mb-4">
            <?php
                $link = 'dashboard.php';
                $label = 'Back to Dashboard';
                include 'components/back-button.php';
            ?>
            <h2 class="text-2xl font-bold">Edit Home Section (<?= strtoupper($lang) ?>)</h2>
        </div>

        <form method="post" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="lang" value="<?= $lang ?>">

            <div>
                <label class="block font-semibold mb-1">Name:</label>
                <input type="text" name="name" value="<?= $data['name'] ?? '' ?>" required
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold mb-1">Title:</label>
                <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" required
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold mb-1">Bio:</label>
                <textarea name="bio" rows="4"
                          class="w-full border border-gray-300 rounded px-3 py-2"><?= $data['bio'] ?? '' ?></textarea>
            </div>

            <div>
                <label class="block font-semibold mb-1">Profile Image:</label>
                <?php if (!empty($data['profile_image'])): ?>
                    <img src="<?= $data['profile_image'] ?>" width="100" class="mb-2 rounded">
                <?php endif; ?>
                <input type="file" name="profile_image" class="block w-full text-sm text-gray-500">
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                Save
            </button>
        </form>

        <div class="mt-6 text-sm">
            <p class="mb-2 font-medium">Switch Language:</p>
            <div class="space-x-2">
                <a href="?lang=en" class="text-blue-600 hover:underline">English</a>
                <a href="?lang=kh" class="text-blue-600 hover:underline">Khmer</a>
                <a href="?lang=zh" class="text-blue-600 hover:underline">Chinese</a>
            </div>
        </div>
    </div>
</body>
</html>