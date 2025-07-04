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
        //session_start(); // Ensure session available
        $id = $_POST['id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $existingProfile = $_POST['existing_profile'] ?? 'assets/uploads/default.png';
        $profilePath = $existingProfile;
        $role = $_POST['role'] ?? null;

        $loggedInUserId = $_SESSION['admin_id'] ?? 0;

        if ($id == $loggedInUserId) {
            // Prevent role change for logged-in user
            $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($existingRole);
            $stmt->fetch();
            $stmt->close();
            $role = $existingRole;
        }

        // Always force default admin to stay admin
        $stmt = $conn->prepare("SELECT is_default_admin FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['is_default_admin'] == 1) {
                $role = 'admin';
            } elseif ($role === null) {
                return "Role is required.";
            }
        }

        // ✅ Handle image upload if any
        if (!empty($_FILES['user_profile']['name'])) {
            $originalName = basename($_FILES['user_profile']['name']);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $targetDir = "assets/uploads/";
            $newProfilePath = $targetDir . $uniqueName;

            if (move_uploaded_file($_FILES['user_profile']['tmp_name'], $newProfilePath)) {
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

    function handleDeleteUser($conn, $userId, $currentUserId) {
        $userId = intval($userId);

        if (!$userId) {
            return "Missing user ID.";
        }

        if ($userId === intval($currentUserId)) {
            return "You cannot delete your own account.";
        }

        $stmt = $conn->prepare("SELECT is_default_admin FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($isDefault);
        $stmt->fetch();
        $stmt->close();

        if ($isDefault) {
            return "Default admin cannot be deleted.";
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);

        return $stmt->execute() ? true : $stmt->error;
    }


