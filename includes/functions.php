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
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $profilePath = 'assets/uploads/default.png'; // fallback default

    if (!empty($_FILES['user_profile']['name'])) {
        $originalName = basename($_FILES['user_profile']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetDir = "assets/uploads/";
        $targetFile = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES['user_profile']['tmp_name'], $targetFile)) {
            $profilePath = $targetFile;
        } else {
            return "Failed to upload image.";
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, user_profile, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssssss", $username, $email, $password, $full_name, $profilePath, $role);

    return $stmt->execute() ? true : $stmt->error;
}

function handleUpdateUser($conn) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $existingProfile = $_POST['existing_profile'] ?? 'assets/uploads/default.png';
    $profilePath = $existingProfile;
    $role = $_POST['role'];

    if (!empty($_FILES['user_profile']['name'])) {
        $originalName = basename($_FILES['user_profile']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uniqueName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetDir = "assets/uploads/";
        $newProfilePath = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES['user_profile']['tmp_name'], $newProfilePath)) {
            // ✅ Delete old image if not default and different
            if (
                file_exists($existingProfile) &&
                $existingProfile !== 'assets/uploads/default.png' &&
                $existingProfile !== $newProfilePath
            ) {
                unlink($existingProfile);
            }
            $profilePath = $newProfilePath;
        } else {
            return "Failed to upload new profile image.";
        }
    }

    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, full_name=?, user_profile=?, role=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("sssssi", $username, $email, $full_name, $profilePath, $role, $id);

    return $stmt->execute() ? true : $stmt->error;
}

// function handleUpdateUser($conn) {
//         $id = $_POST['id'];
//         $username = trim($_POST['username']);
//         $email = trim($_POST['email']);
//         $full_name = trim($_POST['full_name']);
//         $existingProfile = $_POST['existing_profile'] ?? 'assets/uploads/default.png';
//         $profilePath = $existingProfile;
//         $role = $_POST['role'];

//         if (!empty($_FILES['user_profile']['name'])) {
//             $tmpPath = $_FILES['user_profile']['tmp_name'];
//             $originalName = basename($_FILES['user_profile']['name']);
//             $targetDir = "assets/uploads/";
//             $newProfilePath = $targetDir . $originalName;

//             if (file_exists($newProfilePath)) {
//                 $_SESSION['error'] = "Image already exists. Please rename your file or choose another one.";
//                 header("Location: users.php?edit={$_POST['id']}&username=" . urlencode($_POST['username']) . "&email=" . urlencode($_POST['email']) . "&full_name=" . urlencode($_POST['full_name']) . "&user_profile=" . urlencode($_POST['existing_profile']) . "&role=" . urlencode($_POST['role']));
//                 exit;
//             } else {
//                 if (move_uploaded_file($tmpPath, $newProfilePath)) {
//                     // ✅ Delete old image if not default and different from new one
//                     if (file_exists($existingProfile) && $existingProfile !== 'assets/uploads/default.png' && $existingProfile !== $newProfilePath) {
//                         unlink($existingProfile);
//                     }

//                     $profilePath = $newProfilePath;
//                 } else {
//                     $_SESSION['error'] = "Failed to upload new profile image.";
//                     header("Location: users.php?edit={$_POST['id']}&username=" . urlencode($_POST['username']) . "&email=" . urlencode($_POST['email']) . "&full_name=" . urlencode($_POST['full_name']) . "&user_profile=" . urlencode($_POST['existing_profile']) . "&role=" . urlencode($_POST['role']));
//                     exit;
//                 }
//             }
//         }

//         $stmt = $conn->prepare("UPDATE users SET username=?, email=?, full_name=?, user_profile=?, role=?, updated_at=NOW() WHERE id=?");
//         $stmt->bind_param("sssssi", $username, $email, $full_name, $profilePath, $role, $id);

//         return $stmt->execute() ? true : $stmt->error;
// }
