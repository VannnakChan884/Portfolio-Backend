<?php include 'includes/header.php'; ?>
<?php
// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM messages WHERE id = $id");
    header("Location: messages.php");
    exit;
}

$messages = $conn->query("SELECT * FROM messages ORDER BY sent_at DESC");
?>
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>
        <div class="max-w-full mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
            <?php include 'components/back-button.php'; ?>
    
            <div class="overflow-x-auto my-6">
                <h2 class="text-2xl font-bold mb-4">Contact Messages</h2>
                <table class="w-full border border-collape text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                        <tr>
                            <th class="p-2 border dark:border-gray-600">Name</th>
                            <th class="p-2 border dark:border-gray-600">Email</th>
                            <th class="p-2 border dark:border-gray-600">Subject</th>
                            <th class="p-2 border dark:border-gray-600">Message</th>
                            <th class="p-2 border dark:border-gray-600">Sent At</th>
                            <th class="p-2 border dark:border-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $messages->fetch_assoc()): ?>
                            <tr class="border-b">
                                <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['subject']) ?></td>
                                <td class="p-2 border dark:border-gray-600"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                                <td class="p-2 border dark:border-gray-600"><?= $row['sent_at'] ?></td>
                                <td class="p-2 border dark:border-gray-600">
                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this message?')" class="text-red-600 hover:underline">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php include 'includes/footer.php'; ?>