<?php
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");

    require_once '../includes/db.php';

    $lang = $_GET['lang'] ?? 'en';

    $stmt = $conn->prepare("SELECT * FROM home WHERE lang = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $lang);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        $home_id = $data['id'];
        $skills = [];

        $skillResult = $conn->query("SELECT name FROM skills WHERE home_id = $home_id");
        while ($row = $skillResult->fetch_assoc()) {
            $skills[] = $row['name'];
        }
        
        $data['skills'] = $skills;
    }

    echo json_encode($data ?? []);
?>