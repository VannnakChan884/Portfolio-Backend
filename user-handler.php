<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

function respond($result, $successMessage, $status = 'info') {
    echo json_encode([
        'success' => $result === true,
        'message' => $result === true ? $successMessage : $result,
        'status' => $result === true ? $status : 'error'
    ]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_user':
            respond(handleAddUser($conn), 'User added successfully.', 'info'); // blue
            break;

        case 'update_user':
            respond(handleUpdateUser($conn), 'User updated successfully.', 'success'); // green
            break;

        case 'delete_user':
            $currentUserId = $_SESSION['admin_id'] ?? null;
            $userIdToDelete = $_POST['id'] ?? null;
            respond(handleDeleteUser($conn, $userIdToDelete, $currentUserId), 'User deleted successfully.', 'danger'); // red
            break;

        default:
            respond(false, 'Invalid action.');
    }
}
