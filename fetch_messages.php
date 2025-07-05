<?php
require_once 'includes/db.php';

$messages = [];
$res = $conn->query("SELECT name, subject, sent_at FROM messages ORDER BY sent_at DESC LIMIT 5");
while ($row = $res->fetch_assoc()) {
    $messages[] = $row;
}

header('Content-Type: application/json');
echo json_encode($messages);
?>
