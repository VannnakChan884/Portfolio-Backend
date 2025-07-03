<?php
header('Content-Type: application/json');
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

$id = intval($_GET['id']);

// Prevent self-deletion
if ($id === $currentUserId) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
    exit;
}

// Check if user is default admin
$stmt = $conn->prepare("SELECT is_default_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($isDefault);
$stmt->fetch();
$stmt->close();

if ($isDefault) {
    echo json_encode(['success' => false, 'message' => 'Default admin cannot be deleted.']);
    exit;
}

// Proceed to delete
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}
exit;
