<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: auth/login.php");
    exit();
}
require_once 'includes/db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM messages WHERE id = $id");
    header("Location: messages.php");
    exit;
}

$messages = $conn->query("SELECT * FROM messages ORDER BY sent_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Messages</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6 font-sans">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Contact Messages</h2>

        <div class="overflow-x-auto">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Subject</th>
                        <th class="p-2 border">Message</th>
                        <th class="p-2 border">Sent At</th>
                        <th class="p-2 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $messages->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="p-2 border"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row['subject']) ?></td>
                            <td class="p-2 border"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                            <td class="p-2 border"><?= $row['sent_at'] ?></td>
                            <td class="p-2 border">
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this message?')" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="inline-block mt-4 text-gray-600 hover:underline">← Back to Dashboard</a>
    </div>
</body>
</html>
