<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
require_once '../includes/db.php';

$sql = " SELECT  a.id AS about_id, a.title AS about_title, a.description AS about_description, a.lang, e.id AS exp_id, e.title AS exp_title, e.company, e.start_date, e.end_date, e.description AS exp_description
FROM about a LEFT JOIN experiences e ON a.id = e.about_id ORDER BY a.id DESC, e.`order` ASC";
$result = $conn->query($sql);

$aboutData = [];

while ($row = $result->fetch_assoc()) {
    $about_id = $row['about_id'];

    // Initialize about section
    if (!isset($aboutData[$about_id])) {
        $aboutData[$about_id] = [
            'id' => $about_id,
            'title' => $row['about_title'],
            'description' => $row['about_description'],
            'lang' => $row['lang'],
            'experiences' => []
        ];
    }

    // Add experience if exists
    if (!empty($row['exp_id'])) {
        $aboutData[$about_id]['experiences'][] = [
            'id' => $row['exp_id'],
            'title' => $row['exp_title'],
            'company' => $row['company'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'description' => $row['exp_description']
        ];
    }
}

echo json_encode(array_values($aboutData), JSON_UNESCAPED_UNICODE);