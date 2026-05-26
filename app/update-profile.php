<?php
session_start();

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    if ( isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_POST['password']) && isset($_POST['full_name']) && $_SESSION['role'] == 'employee') { // Check if the username and password fields are set and if the user is logged in by verifying session variables

        include "../DB_connection.php"; // Include the database connection file to establish a connection to the database

        function validate_input($data) // Function to sanitize user input
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        
        $password  = validate_input($_POST['password']);
        $full_name = validate_input($_POST['full_name']);
        $new_password = validate_input($_POST['new_password']);
        $confirm_password        = validate_input($_POST['confirm_password']);
        $id = $_SESSION['id'];

        if (empty($password) || empty($new_password) || empty($confirm_password)) { // Check if any of the password fields are empty
            $error_message = "All password fields are required";
            header('Location: ../edit_profile.php?error=' . $error_message);
            exit();
        } else if ($new_password !== $confirm_password) { // Check if the new password and confirm password match
            $error_message = "New password and confirm password do not match";
            header('Location: ../edit_profile.php?error=' . $error_message);
            exit();
        } else if (empty($full_name)) { // Check if the full name field is empty
            $error_message = "Full name is required";
            header('Location: ../edit_profile.php?error=' . $error_message);
            exit();
        } else { // If both fields are filled, proceed with database update

            include "Model/user.php"; // Include the user model file to access user-related functions
            $user = get_user_by_id($conn, $id);

            if ($user ){
            
                if (password_verify($password, $user['password'])) {
                                            
                

                    // For now I set that there is admin and employee, In future need to add more roles and admin can give different roles

                    $new_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the password

                    $data = array($full_name, $new_password, $id); # <----Change to role in future
                    update_profile($conn, $data);

                    $success_message = "User updated successfully";
                    header('Location: ../edit_profile.php?success=' . $success_message);
                    exit();
                }else{
                    $error_message = "Incorrect password";
                    header('Location: ../edit_profile.php?error=' . $error_message);
                    exit();
                }
            }else{
                $error_message = "Unknown error occurred";
                header('Location: ../edit_profile.php?error=' . $error_message);
                exit();
            }

            
        }

    } else { // If the username or password fields are not set, redirect back with an error message
        $error_message = "Unknown error occurred";
        header('Location: ../edit_profile.php?error=' . $error_message);
        exit();
    }

} else {
    $error_message = "Login at first";
    header('Location: ../login.php?error=' . $error_message);
    exit();
}
?>