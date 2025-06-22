<?php
function uploadProfileImage($inputName, &$error) {
    $profilePath = null;
    if (!empty($_FILES['user_profile']['name'])) {
        $originalName = basename($_FILES['user_profile']['name']);
        $targetDir = "assets/uploads/";
        $targetFile = $targetDir . $originalName;

        if (file_exists($targetFile)) {
            $error = "Image already exists. Please rename your file or choose another one.";
        } else {
            if (move_uploaded_file($_FILES['user_profile']['tmp_name'], $targetFile)) {
                $profilePath = $targetFile;
            } else {
                $error = "Failed to upload image.";
            }
        }
    }
    return $profilePath;
}

function handleAddUser($conn) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $profilePath = 'assets/uploads/default.png'; // default fallback

    if (!empty($_FILES['user_profile']['name'])) {
        $originalName = basename($_FILES['user_profile']['name']);
        $targetDir = "assets/uploads/";
        $newFilename = time() . "_" . $originalName;
        $targetFile = $targetDir . $newFilename;

        if (file_exists($targetFile)) {
            $error = "Image already exists. Please rename your file or choose another one.";
        } else {
            if (move_uploaded_file($_FILES['user_profile']['tmp_name'], $targetFile)) {
                $profilePath = $targetFile;
            } else {
                $error = "Failed to upload image.";
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, user_profile, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sssss", $username, $email, $password, $full_name, $profilePath);

    return $stmt->execute() ? true : $stmt->error;
}

    function handleUpdateUser($conn) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];
        $existingProfile = $_POST['existing_profile'] ?? 'assets/uploads/default.png';
        $profilePath = $existingProfile;

        if (!empty($_FILES['user_profile']['name'])) {
            $tmpPath = $_FILES['user_profile']['tmp_name'];
            $originalName = basename($_FILES['user_profile']['name']);
            $targetDir = "assets/uploads/";
            $newProfilePath = $targetDir . $originalName;

            if (file_exists($newProfilePath)) {
                $_SESSION['error'] = "Image already exists. Please rename your file or choose another one.";
                header("Location: users.php?edit=$id&username=" . urlencode($username) . "&email=" . urlencode($email) . "&full_name=" . urlencode($full_name) . "&user_profile=" . urlencode($profilePath));
                exit;
            } else {
                if (move_uploaded_file($tmpPath, $newProfilePath)) {
                    // ✅ Delete old image if not default and different from new one
                    if (file_exists($existingProfile) && $existingProfile !== 'assets/uploads/default.png' && $existingProfile !== $newProfilePath) {
                        unlink($existingProfile);
                    }

                    $profilePath = $newProfilePath;
                } else {
                    $_SESSION['error'] = "Failed to upload new profile image.";
                    header("Location: users.php?edit=$id&username=" . urlencode($username) . "&email=" . urlencode($email) . "&full_name=" . urlencode($full_name) . "&user_profile=" . urlencode($profilePath));
                    exit;
                }
            }
        }

        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, full_name=?, user_profile=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssssi", $username, $email, $full_name, $profilePath, $id);

        return $stmt->execute() ? true : $stmt->error;
    }
