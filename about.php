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
    $check = $conn->prepare("SELECT id FROM about WHERE lang = ?");
    $check->bind_param("s", $lang);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // Update
        if ($imagePath) {
            $stmt = $conn->prepare("UPDATE about SET name=?, title=?, bio=?, profile_image=? WHERE lang=?");
            $stmt->bind_param("sssss", $name, $title, $bio, $imagePath, $lang);
        } else {
            $stmt = $conn->prepare("UPDATE about SET name=?, title=?, bio=? WHERE lang=?");
            $stmt->bind_param("ssss", $name, $title, $bio, $lang);
        }
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO about (name, title, bio, profile_image, lang) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $title, $bio, $imagePath, $lang);
    }

    $stmt->execute();
    echo "<script>alert('About info saved!'); window.location='about.php?lang=$lang';</script>";
    exit;
}

// Get current data
$data = $conn->query("SELECT * FROM about WHERE lang = '$lang'")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage About - <?= strtoupper($lang) ?></title>
</head>
<body>
    <h2>Edit About Section (<?= strtoupper($lang) ?>)</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="lang" value="<?= $lang ?>">

        <label>Name:</label><br>
        <input type="text" name="name" value="<?= $data['name'] ?? '' ?>" required><br><br>

        <label>Title:</label><br>
        <input type="text" name="title" value="<?= $data['title'] ?? '' ?>" required><br><br>

        <label>Bio:</label><br>
        <textarea name="bio" rows="4" cols="40"><?= $data['bio'] ?? '' ?></textarea><br><br>

        <label>Profile Image:</label><br>
        <?php if (!empty($data['profile_image'])): ?>
            <img src="<?= $data['profile_image'] ?>" width="100"><br>
        <?php endif; ?>
        <input type="file" name="profile_image"><br><br>

        <button type="submit">Save</button>
    </form>

    <p>Switch Language:
        <a href="?lang=en">English</a> |
        <a href="?lang=kh">Khmer</a> |
        <a href="?lang=zh">Chinese</a>
    </p>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>
</body>
</html>
