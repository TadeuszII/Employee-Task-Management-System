<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables
if (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && in_array($_SESSION['role'], ['admin', 'manager']) && isset($_POST['due_date'])) { // Check if the username and password fields are set and if the user is logged in by verifying session variables


    include "../DB_connection.php"; // Include the database connection file to establish a connection to the database
    include "Model/task.php"; // Include the user model file to access user-related functions


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
    $id = (int)$_POST['id'];
    $due_date = validate_input($_POST['due_date']);


    // manager can only update tasks they created 
    if ($_SESSION['role'] === 'manager') {
        $task = get_task_by_id($conn, $id);
        if (!$task || $task['created_by'] != $_SESSION['id']) {
            header('Location: ../all_tasks.php?error=You can only edit tasks you created');
            exit();
        }
    }


    if (empty($title)) { // Check if the title field is empty
        $error_message = "Title is required";
        header('Location: ../edit-task.php?id=' . $id . '&error=' . $error_message);
        exit();
    } else if (empty($description)) { // Check if the description field is empty
        $error_message = "Description is required";
        header('Location: ../edit-task.php?id=' . $id . '&error=' . $error_message);
        exit();
    }else if (empty($due_date)) { // Check if the due_date field is empty
        $error_message = "Due Date is required";
        header('Location: ../edit-task.php?id=' . $id . '&error=' . $error_message);
        exit();
    }else if ($assigned_to == 0) { // Check if the assigned_to field is empty
        $error_message = "Assigned To is required";
        header('Location: ../edit-task.php?id=' . $id . '&error=' . $error_message);
        exit();
    }
    else { // If both fields are filled, proceed with database verification


        $data = array($title, $description, $assigned_to, $due_date, $id);
        update_task($conn, $data);


        $error_message = "Task updated successfully"; # Using error message to show success message
        header('Location: ../edit-task.php?id=' . $id . '&success=' . $error_message);
        exit();


    }
} else { // If the username or password fields are not set, redirect back to the login page with an error message
    $error_message = "Unknown error occurred";
    header('Location: ../edit-task.php?error=' . $error_message);
    exit();
}
} else {
    $error_message = "Login at first";
    header('Location: ../login.php?error=' . $error_message);
    exit();
}