<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables
if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && in_array($_SESSION['role'], ['admin', 'manager']) && isset($_POST['due_date'])) { // Check if the username and password fields are set and if the user is logged in by verifying session variables


    include "../DB_connection.php"; // Include the database connection file to establish a connection to the database


    function validate_input($data) // Function to sanitize user input
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $title = validate_input($_POST['title']);
    $description = validate_input($_POST['description']);
    $assigned_to = validate_input($_POST['assigned_to']);
    $due_date = validate_input($_POST['due_date']);
    $due_date = empty($due_date) ? null : $due_date; // ustawienie na null jeśli data jest pusta
    if (empty($title)) { // Check if the title field is empty
        $error_message = "Title is required";
        header('Location: ../create_task.php?error=' . $error_message);
        exit();
    } else if (empty($description)) { // Check if the description field is empty
        $error_message = "Description is required";
        header('Location: ../create_task.php?error=' . $error_message);
        exit();
    }else if ($assigned_to == 0) { // Check if the assigned_to field is empty
        $error_message = "Assigned To is required";
        header('Location: ../create_task.php?error=' . $error_message);
        exit();
    }
    else { // If both fields are filled, proceed with database verification

        include "Model/task.php"; // Include the user model file to access user-related functions
        include "Model/notification.php"; // Include the notification model file to access notification-related functions


        $data = array($title, $description, $assigned_to, $due_date, $_SESSION['id']);
        insert_task($conn, $data);
        $notification_data = array("'$title' has been assigned to you.", $assigned_to, 'New Task Assigned');
        insert_notifications($conn, $notification_data);



        $error_message = "Task created successfully"; # Using error message to show success message
        header('Location: ../create_task.php?success=' . $error_message);
        exit();

    }
} else { // If the username or password fields are not set, redirect back to the login page with an error message
    $error_message = "Unknown error occurred";
    header('Location: ../create_task.php?error=' . $error_message);
    exit();
}
} else {
    $error_message = "Login at first";
    header('Location: ../create_task.php?error=' . $error_message);
    exit();
}

