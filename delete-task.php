<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables
    
    include "DB_connection.php"; // Include the database connection file
    include "app/Model/task.php"; // Include the user model file to access user-related functions

    if (!isset($_GET['id'])) {
        $error_message = "Task id is required";
        header('Location: manage_tasks.php?error=' . $error_message);
        exit();
    }
    $id = $_GET['id'];
    $task = get_task_by_id($conn, $id); // Retrieve the task from the database

    if ($task == 0) {
        $error_message = "Task not found";
        header('Location: manage_tasks.php?error=' . $error_message);
        exit();
    }
    $data = array($id);
    $success_message = "Deleted task successfully";
    delete_task($conn, $data); // Call the function to delete the task from the database
    header('Location: all_tasks.php?success=' . $success_message);
    exit();


} else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>