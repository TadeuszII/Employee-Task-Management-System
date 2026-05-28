<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "../DB_connection.php"; // Include the database connection file
    include "Model/task.php"; // Include the task model file to access task-related functions
    include "Model/user.php"; // Include the user model file to access user-related functions
    include "Model/notification.php"; // Include the notification model file to access notification-related functions

    $notifications = count_my_notifications($conn, $_SESSION['id']); // Retrieve all notifications from the database for the current user

    if ($notifications) {
        echo "&nbsp;" . $notifications . "&nbsp;";

    }
    


} else {
    echo 0;
}
?>