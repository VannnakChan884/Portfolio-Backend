<?php
header("Content-Type: application/json");

// Allow access from frontend (adjust if needed)
header("Access-Control-Allow-Origin: *");

require_once '../includes/db.php';

$lang = $_GET['lang'] ?? 'en';

$stmt = $conn->prepare("SELECT id, title, description, image FROM projects WHERE lang = ? ORDER BY id DESC");
$stmt->bind_param("s", $lang);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode($projects);
?>
