<?php
session_start();


if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables


    if (isset($_POST['user_name']) && isset($_POST['full_name']) && $_SESSION['role'] == 'admin') { // Check if the username and password fields are set and if the user is logged in by verifying session variables


        include "../DB_connection.php"; // Include the database connection file to establish a connection to the database
        include "Model/user.php"; // Include the user model file to access user-related functions


        function validate_input($data) // Function to sanitize user input
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }


        $user_name = validate_input($_POST['user_name']);
        $full_name = validate_input($_POST['full_name']);
        $id        = (int)$_POST['id'];
        $allowed_roles = ['employee', 'manager', 'admin'];
        $role = isset($_POST['role']) && in_array($_POST['role'], $allowed_roles) ? $_POST['role'] : 'employee';
        $manager_id = null;
        if (in_array($role, ['employee', 'manager']) && !empty($_POST['manager_id']) && is_numeric($_POST['manager_id'])) {
            $manager_id = (int)$_POST['manager_id'];
        }


        if (empty($user_name)) { // Check if the username field is empty
            $error_message = "Username is required";
            header('Location: ../edit-user.php?error=' . $error_message . '&id=' . $id);
            exit();
        } else if (empty($full_name)) { // Check if the full name field is empty
            $error_message = "Full name is required";
            header('Location: ../edit-user.php?error=' . $error_message . '&id=' . $id);
            exit();
        } else { // If both fields are filled, proceed with database update


            // For now I set that there is admin and employee, In future need to add more roles and admin can give different roles

            // only hash if new password provided, otherwise keep existing password
            if (!empty($_POST['password'])) {
                $password = password_hash(validate_input($_POST['password']), PASSWORD_DEFAULT); // Hash the password
            } else {
                $existing = get_user_by_id($conn, $id);
                $password = $existing['password']; // Keep the current password
            }
            

            //use selected role instead of hardcoded 'employee' 
            $data = array($full_name, $user_name, $password, $role, $manager_id, $id);
            update_user($conn, $data);


            $success_message = "User updated successfully";
            header('Location: ../edit-user.php?success=' . $success_message . '&id=' . $id);
            exit();
        }


    } else { // If the username or password fields are not set, redirect back with an error message
        $error_message = "Unknown error occurred";
        header('Location: ../edit-user.php?error=' . $error_message);
        exit();
    }


} else {
    $error_message = "Login at first";
    header('Location: ../login.php?error=' . $error_message);
    exit();
}
?>