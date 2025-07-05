<?php
require_once 'includes/db.php';

$result = $conn->query("SELECT COUNT(*) AS unread FROM messages WHERE is_read = 0");
$count = $result->fetch_assoc()['unread'];

echo $count;
?>
