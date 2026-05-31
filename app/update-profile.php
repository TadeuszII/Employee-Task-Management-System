<?php
session_start();

if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

    if (isset($_POST['full_name']) && in_array($_SESSION['role'], ['admin', 'employee', 'manager'])) {

        include "../DB_connection.php";

        function validate_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $full_name        = validate_input($_POST['full_name']);
        $password         = $_POST['password']         ?? '';
        $new_password     = $_POST['new_password']     ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $id               = $_SESSION['id'];

        if (empty($full_name)) {
            header('Location: ../edit_profile.php?error=Full name is required');
            exit();
        }

        include "Model/user.php";
        $user = get_user_by_id($conn, $id);

        if (!$user) {
            header('Location: ../edit_profile.php?error=Unknown error occurred');
            exit();
        }

        // ---- Sprawdź czy user chce zmienić hasło ----
        $wants_to_change_password = !empty($password) || !empty($new_password) || !empty($confirm_password);

        if ($wants_to_change_password) {
            // Walidacja wszystkich pól hasła
            if (empty($password) || empty($new_password) || empty($confirm_password)) {
                header('Location: ../edit_profile.php?error=Fill all password fields to change password');
                exit();
            }
            if ($new_password !== $confirm_password) {
                header('Location: ../edit_profile.php?error=New password and confirm password do not match');
                exit();
            }
            if (!password_verify($password, $user['password'])) {
                header('Location: ../edit_profile.php?error=Incorrect current password');
                exit();
            }
        }

        // ---- Obsługa zdjęcia profilowego ----
        $profile_picture_name = null;

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
            $size = $_FILES['profile_picture']['size'];

            if (!in_array($ext, $allowed)) {
                header('Location: ../edit_profile.php?error=Invalid file type. Allowed: jpg, png, webp');
                exit();
            }
            if ($size > 25 * 1024 * 1024) {
                header('Location: ../edit_profile.php?error=The file is too large. Maximum size is 25MB');
                exit();
            }

            $profile_picture_name = 'user_' . $id . '.' . $ext;
            $destination = '../images/profiles/' . $profile_picture_name;

            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                header('Location: ../edit_profile.php?error=Failed to upload image');
                exit();
            }
        }

        // ---- Zapis do bazy ----
        if ($wants_to_change_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            if ($profile_picture_name) {
                $stmt = $conn->prepare("UPDATE user SET full_name=?, password=?, profile_picture=? WHERE id=?");
                $stmt->execute([$full_name, $hashed_password, $profile_picture_name, $id]);
            } else {
                $stmt = $conn->prepare("UPDATE user SET full_name=?, password=? WHERE id=?");
                $stmt->execute([$full_name, $hashed_password, $id]);
            }
        } else {
            // Tylko full_name i/lub zdjęcie - hasło bez zmian
            if ($profile_picture_name) {
                $stmt = $conn->prepare("UPDATE user SET full_name=?, profile_picture=? WHERE id=?");
                $stmt->execute([$full_name, $profile_picture_name, $id]);
            } else {
                $stmt = $conn->prepare("UPDATE user SET full_name=? WHERE id=?");
                $stmt->execute([$full_name, $id]);
            }
        }

        header('Location: ../edit_profile.php?success=Profile updated successfully');
        exit();

    } else {
        header('Location: ../edit_profile.php?error=Unknown error occurred');
        exit();
    }

} else {
    header('Location: ../login.php?error=Login at first');
    exit();
}
?>