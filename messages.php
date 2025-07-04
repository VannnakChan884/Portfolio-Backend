<?php
require_once 'includes/header.php';

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_id'])) {
    $replyId = intval($_POST['reply_id']);
    $replyText = trim($_POST['reply']);

    // Update reply and mark that specific message as read
    $stmt = $conn->prepare("UPDATE messages SET reply = ?, is_read = 1, replied_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $replyText, $replyId);
    $stmt->execute();

    $_SESSION['success'] = "Reply saved successfully!";
    header("Location: messages.php");
    exit;
}


// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM messages WHERE id = $id");
    header("Location: messages.php");
    exit;
}

$allMessagesResult = $conn->query("SELECT * FROM messages ORDER BY sent_at DESC");

// Count unread
// $unreadResult = $conn->query("SELECT COUNT(*) AS unread FROM messages WHERE is_read = 0");
// $unreadCount = $unreadResult->fetch_assoc()['unread'];
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php include 'includes/topbar.php'; ?>
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
                        <?php while ($row = $allMessagesResult->fetch_assoc()): ?>
                            <?php $isNew = $row['is_read'] == 0; ?>
                            <tr class="border-b <?= $isNew ? 'bg-green-400/50' : '' ?>">
                                <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="p-2 border dark:border-gray-600">
                                    <button onclick="toggleReply(<?= $row['id'] ?>, event)"
                                        class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded">
                                        <?= htmlspecialchars($row['subject']) ?>
                                        <?php if ($row['is_read'] == 0): ?>
                                            <span class="ml-1 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
                                        <?php endif; ?>
                                    </button>
                                </td>
                                <td class="p-2 border dark:border-gray-600"><?= nl2br(htmlspecialchars($row['message'])) ?>
                                </td>
                                <td class="p-2 border dark:border-gray-600"><?= $row['sent_at'] ?></td>
                                <td class="p-2 border dark:border-gray-600 text-center">
                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this message?')"
                                        class="inline-block text-sm px-2 py-1 rounded bg-red-100 text-red-600">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <!-- Collapsible reply form -->
                            <tr id="reply-row-<?= $row['id'] ?>" class="hidden">
                                <td colspan="6" class="bg-gray-50 dark:bg-gray-800 p-2">
                                    <div class="reply-container" id="reply-container-<?= $row['id'] ?>">
                                        <form method="post">
                                            <input type="hidden" name="reply_id" value="<?= $row['id'] ?>">
                                            <textarea id="reply-textarea-<?= $row['id'] ?>" name="reply" rows="2"
                                                class="w-full p-1 border dark:bg-gray-700"><?= htmlspecialchars($row['reply'] ?? '') ?></textarea>
                                            <button type="submit"
                                                class="mt-1 px-3 py-1 bg-green-600 text-white text-sm rounded">Save
                                                Reply</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
    let currentOpenReplyId = null;

    function toggleReply(id, event) {
        event.stopPropagation(); // Prevent immediate close

        const row = document.getElementById('reply-row-' + id);
        const wasHidden = row.classList.contains('hidden');

        // Hide all reply rows first
        document.querySelectorAll('[id^="reply-row-"]').forEach(el => el.classList.add('hidden'));
        currentOpenReplyId = null;

        if (wasHidden) {
            row.classList.remove('hidden');
            currentOpenReplyId = id;

            setTimeout(() => {
                const textarea = document.getElementById('reply-textarea-' + id);
                if (textarea) textarea.focus();
            }, 100);
        }
    }

    // Detect outside clicks
    document.addEventListener('click', function (event) {
        if (currentOpenReplyId !== null) {
            const container = document.getElementById('reply-container-' + currentOpenReplyId);
            if (!container.contains(event.target)) {
                document.getElementById('reply-row-' + currentOpenReplyId).classList.add('hidden');
                currentOpenReplyId = null;
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>