<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portfolio_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable foreign key checks (optional in MySQL)
$conn->query("SET foreign_key_checks = 0");

// // Users Table (parent table)
// $conn->query(" CREATE TABLE IF NOT EXISTS users (
//         id INT AUTO_INCREMENT PRIMARY KEY,
//         username VARCHAR(50) NOT NULL,
//         email VARCHAR(100) NOT NULL,
//         password VARCHAR(255) NOT NULL,
//         full_name VARCHAR(100),
//         user_profile VARCHAR(255),
//         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//     )
// ");

// // HOME table
// $conn->query("CREATE TABLE IF NOT EXISTS home (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     name VARCHAR(100) NOT NULL,
//     bio TEXT,
//     profile_image VARCHAR(255),
//     lang VARCHAR(10) DEFAULT 'en',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// )");

// // SKILLS table
// $conn->query("CREATE TABLE IF NOT EXISTS skills (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     home_id INT NOT NULL,
//     name VARCHAR(100) NOT NULL,
//     level VARCHAR(50),
//     lang VARCHAR(10) DEFAULT 'en',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (home_id) REFERENCES home(id) ON DELETE CASCADE
// )");

// // ABOUT table
// $conn->query("CREATE TABLE IF NOT EXISTS about (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     title VARCHAR(100),
//     description TEXT,
//     lang VARCHAR(10) DEFAULT 'en',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// )");

// // EXPERIENCES table (linked to about)
// $conn->query("CREATE TABLE IF NOT EXISTS experiences (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     about_id INT NOT NULL,
//     title VARCHAR(100),
//     company VARCHAR(100),
//     start_date DATE,
//     end_date DATE,
//     description TEXT,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (about_id) REFERENCES about(id) ON DELETE CASCADE
// )");

// // Add 'order' column to experiences table if it doesn't exist
// $checkOrderColumn = $conn->query("SHOW COLUMNS FROM experiences LIKE 'order'");
// if ($checkOrderColumn->num_rows === 0) {
//     $conn->query("ALTER TABLE experiences ADD COLUMN `order` INT DEFAULT 0");
// }

// // PROJECTS table
// $conn->query("CREATE TABLE IF NOT EXISTS projects (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     title VARCHAR(255) NOT NULL,
//     description TEXT,
//     image VARCHAR(255),
//     project_link VARCHAR(255),
//     lang VARCHAR(10) DEFAULT 'en',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// )");

// // MESSAGES table
// $conn->query("CREATE TABLE IF NOT EXISTS messages (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     name VARCHAR(100),
//     email VARCHAR(100),
//     subject VARCHAR(255),
//     message TEXT,
//     reply TEXT,
//     replied_at DATETIME,
//     is_read BOOLEAN DEFAULT 0,
//     sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// )");

// // Add 'is_read' column to messages table if it doesn't exist
// // $checkIsReadMessage = $conn->query("SHOW COLUMNS FROM messages LIKE 'is_read'");
// // if ($checkIsReadMessage->num_rows === 0) {
// //     $conn->query("ALTER TABLE messages ADD COLUMN is_read BOOLEAN DEFAULT 0");
// // }

// // Add 'reply' column if it doesn't exist
// // $checkReplyColumn = $conn->query("SHOW COLUMNS FROM messages LIKE 'reply'");
// // if ($checkReplyColumn->num_rows === 0) {
// //     $conn->query("ALTER TABLE messages ADD COLUMN reply TEXT DEFAULT NULL");
// // }

// // Add 'replied_at' column if it doesn't exist
// // $checkRepliedAtColumn = $conn->query("SHOW COLUMNS FROM messages LIKE 'replied_at'");
// // if ($checkRepliedAtColumn->num_rows === 0) {
// //     $conn->query("ALTER TABLE messages ADD COLUMN replied_at DATETIME DEFAULT NULL");
// // }


// // If Want Multiple Replies per Message (like a conversation)
// // $conn->query(" CREATE TABLE IF NOT EXISTS message_replies (
// //     id INT AUTO_INCREMENT PRIMARY KEY,
// //     message_id INT NOT NULL,
// //     reply TEXT NOT NULL,
// //     replied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
// //     FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
// // )");

// // LOGIN_CODE table
// $conn->query("CREATE TABLE IF NOT EXISTS login_codes (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     user_id INT NOT NULL,
//     code VARCHAR(6) NOT NULL,
//     expires_at DATETIME,
//     is_used BOOLEAN DEFAULT 0,
//     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
//     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
// )");

// // code_requests table
// // $conn->query("CREATE TABLE IF NOT EXISTS code_requests (
// //     id INT AUTO_INCREMENT PRIMARY KEY,
// //     user_id INT NOT NULL,
// //     requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
// //     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
// // )");

// // SETTINGS table
// $conn->query("CREATE TABLE IF NOT EXISTS settings (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     setting_key VARCHAR(255) NOT NULL,
//     setting_value TEXT NOT NULL,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// )");

// // echo "✅ All tables created successfully.";
// // $conn->close();