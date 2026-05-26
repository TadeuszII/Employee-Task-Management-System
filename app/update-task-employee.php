<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables
if (isset($_POST['id']) && isset($_POST['status']) && $_SESSION['role'] == 'employee') { // Check if the username and password fields are set and if the user is logged in by verifying session variables


    include "../DB_connection.php"; // Include the database connection file to establish a connection to the database
    

    function validate_input($data) // Function to sanitize user input
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    $status = validate_input($_POST['status']);
    $id = validate_input($_POST['id']);

    if (empty($status)) { // Check if the title field is empty
        $error_message = "Status is required";
        header('Location: ../edit-task-employee.php?id=' . $id . '&error=' . $error_message);
        exit();
    } else { // If both fields are filled, proceed with database verification

        include "Model/task.php"; // Include the user model file to access user-related functions

        $data = array($status, $id);
        update_task_status($conn, $data);

        $error_message = "Task updated successfully"; # Using error message to show success message
        header('Location: ../edit-task-employee.php?id=' . $id . '&success=' . $error_message);
        exit();

    }
} else { // If the username or password fields are not set, redirect back to the login page with an error message
    $error_message = "Unknown error occurred";
    header('Location: ../edit-task-employee.php?error=' . $error_message);
    exit();
}
} else {
    $error_message = "Login at first";
    header('Location: ../login.php?error=' . $error_message);
    exit();
}

