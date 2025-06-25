<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../includes/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$errors = [];

foreach ($data as $index => $expId) {
    $expId = intval($expId);
    $stmt = $conn->prepare("UPDATE experiences SET `order` = ? WHERE id = ?");
    $stmt->bind_param("ii", $index, $expId);
    if (!$stmt->execute()) {
        $errors[] = "Failed to update ID $expId";
    }
}

if (count($errors) > 0) {
    echo json_encode(["success" => false, "message" => $errors]);
} else {
    echo json_encode(["success" => true, "message" => "Experience order updated"]);
}


file_put_contents("debug.log", json_encode($data));
echo json_encode(["success" => true, "message" => "Experience order updated"]);
