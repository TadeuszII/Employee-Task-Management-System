<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/user.php"; // Include the user model file to access user-related functions

    if (!isset($_GET['id'])) {
        $error_message = "User id is required";
        header('Location: manage_users.php?error=' . $error_message);
        exit();
    }
    $id = $_GET['id'];
    $user = get_user_by_id($conn, $id); // Retrieve all users from the database "employee"
    
    if ($user == 0) {
         $error_message = "User not found";
        header('Location: manage_users.php?error=' . $error_message);
        exit();
    }
    $data = array($id, "employee");
    $success_message = "Deleted user successfully";
    delete_user($conn, $data); // Call the function to delete the user from the database
    header('Location: manage_users.php?success=' . $success_message);
    exit();


} else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>