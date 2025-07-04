<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_user') {
        $result = handleAddUser($conn);
    } elseif ($action === 'update_user') {
        $result = handleUpdateUser($conn);
    } elseif ($action === 'delete_user') {
        $currentUserId = $_SESSION['admin_id'] ?? null;
        $userIdToDelete = $_POST['id'] ?? null;
        $result = handleDeleteUser($conn, $userIdToDelete, $currentUserId);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        exit;
    }

    echo json_encode([
        'success' => $result === true,
        'message' => $result === true ? 'Operation successful.' : $result
    ]);
    exit;
}
