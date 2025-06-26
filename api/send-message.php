<?php
// Allow preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    exit(0); // Stop further execution
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


require_once '../includes/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON payload"]);
    exit;
}


$name = trim($data['name']);
$email = trim($data['email']);
$subject = trim($data['subject']);
$message = trim($data['message']);

if (!$name || !$email || !$subject || !$message) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Store message in DB
$stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $subject, $message);
$success = $stmt->execute();

// Send email to admin
if ($success) {
    $adminEmail = "vannakchan884@gmail.com"; // Update this
    $emailSubject = "📩 New Contact Message: $subject";
    $emailBody = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    //mail($adminEmail, $emailSubject, $emailBody);  // Use PHPMailer for better formatting (optional)

    echo json_encode(["success" => true, "message" => "Message sent and admin notified."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to store message."]);
}
