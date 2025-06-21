<?php
header("Content-Type: application/json");

// Allow access from frontend (adjust if needed)
header("Access-Control-Allow-Origin: *");

require_once '../includes/db.php';

$lang = $_GET['lang'] ?? 'en';

$stmt = $conn->prepare("SELECT * FROM projects WHERE lang = ? ORDER BY id DESC");
$stmt->bind_param("s", $lang);
$stmt->execute();
$result = $stmt->get_result();

$aboutMe = [];
while ($row = $result->fetch_assoc()) {
    $aboutMe[] = $row;
}

echo json_encode($aboutMe);
?>
