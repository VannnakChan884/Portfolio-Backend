<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$result = $conn->query("SELECT COUNT(*) AS unread FROM messages WHERE is_read = 0");
$unreadCount = $result->fetch_assoc()['unread'] ?? 0;

echo json_encode(['unread' => $unreadCount]);
