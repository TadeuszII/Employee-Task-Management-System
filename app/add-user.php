<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables
if (isset($_POST['user_name']) && isset($_POST['password']) && isset($_POST['full_name']) && $_SESSION['role'] == 'admin') { // Check if the username and password fields are set and if the user is logged in by verifying session variables


    include "../DB_connection.php"; // Include the database connection file to establish a connection to the database


    function validate_input($data) // Function to sanitize user input
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $user_name = validate_input($_POST['user_name']);
    $password = validate_input($_POST['password']);
    $full_name = validate_input($_POST['full_name']);

    if (empty($user_name)) { // Check if the username field is empty
        $error_message = "Username is required";
        header('Location: ../add-user.php?error=' . $error_message);
        exit();
    } else if (empty($password)) { // Check if the password field is empty
        $error_message = "Password is required";
        header('Location: ../add-user.php?error=' . $error_message);
        exit();
    } else if (empty($full_name)) { // Check if the full name field is empty
        $error_message = "Full name is required";
        header('Location: ../add-user.php?error=' . $error_message);
        exit();
    }else { // If both fields are filled, proceed with database verification

        include "Model/user.php"; // Include the user model file to access user-related functions

        // For now I set that there is admin and employee, In future need to add more roles and admin can give different roles

        $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $data = array($full_name, $user_name, $password, "employee"); # <----Change to role in future
        insert_user($conn, $data);


        $error_message = "User added successfully"; # Using error message to show success message
        header('Location: ../add-user.php?success=' . $error_message);
        exit();

    }
} else { // If the username or password fields are not set, redirect back to the login page with an error message
    $error_message = "Unknown error occurred";
    header('Location: ../add-user.php?error=' . $error_message);
    exit();
}
} else {
    $error_message = "Login at first";
    header('Location: ../add-user.php?error=' . $error_message);
    exit();
}
