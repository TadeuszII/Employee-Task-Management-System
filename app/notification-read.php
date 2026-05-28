<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "../DB_connection.php"; // Include the database connection file
    include "Model/task.php"; // Include the task model file to access task-related functions
    include "Model/user.php";
    include "Model/notification.php";

    if (isset($_GET['notification_id'])) { // Check if the 'notification_id' parameter is set in the URL
        $notification_id = $_GET['notification_id']; // Get the notification ID from the URL

        notification_make_read($conn, $_SESSION['id'], $notification_id); // Mark the notification as read
        header('Location: ../notifications.php'); // Redirect to the notifications page if 'notification_id' is not set
        exit();
    }else{
        
        header('Location: index.php'); // Redirect to the index page if 'notification_id' is not set
        exit();
    }


} else {
    $error_message = "Login at first";
    header('Location: ../login.php?error=' . $error_message);
    exit();
}
?>