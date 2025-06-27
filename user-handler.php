<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_user') {
        $result = handleAddUser($conn);
    } elseif ($action === 'update_user') {
        $result = handleUpdateUser($conn);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        exit;
    }

    if ($result === true) {
        echo json_encode(['success' => true, 'message' => 'User saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => $result ?? 'An error occurred.']);
    }
    exit;
}
