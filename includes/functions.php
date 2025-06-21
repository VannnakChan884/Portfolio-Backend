<?php
    function uploadProfileImage($inputName, &$error) {
        $profilePath = null;
        if (!empty($_FILES[$inputName]['name'])) {
            $originalName = basename($_FILES[$inputName]['name']);
            $targetDir = "assets/uploads/";
            $targetFile = $targetDir . $originalName;

            if (file_exists($targetFile)) {
                $error = "Image already exists. Please rename your file or choose another one.";
            } elseif (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetFile)) {
                $profilePath = $targetFile;
            } else {
                $error = "Failed to upload image.";
            }
        }
        return $profilePath;
    }

    function handleAddUser($conn) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $password = $_POST['password'];
        $error = null;

        $profilePath = uploadProfileImage('user_profile', $error);

        if (!empty($error)) return $error;

        if (strlen($password) < 6) {
            return "Password must be at least 6 characters.";
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, full_name, password, user_profile) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $full_name, $hash, $profilePath);

        return $stmt->execute() ? true : "Error adding user: " . $conn->error;
    }

    function handleUpdateUser($conn) {
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $existingProfile = $_POST['existing_profile'];
        $error = null;

        $profilePath = uploadProfileImage('user_profile', $error);
        if (!empty($error)) return $error;

        $profilePath = $profilePath ?: $existingProfile;

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, user_profile = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $email, $full_name, $profilePath, $id);

        return $stmt->execute() ? true : "Failed to update user.";
    }
?>