<?php
$host = 'localhost';
$dbname = 'portfolio_db';
$user = 'root'; // or your hosting username
$pass = '';     // your password

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
