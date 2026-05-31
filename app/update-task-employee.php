<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables
if (isset($_POST['id']) && isset($_POST['status']) && in_array($_SESSION['role'], ['employee', 'manager'])) { // Check if the username and password fields are set and if the user is logged in by verifying session variables


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
        include "Model/user.php"; // pobranie modelu użytkownika do odczytania full_name
        include "Model/notification.php"; // pobranie modelu powiadomień do wysłania notyfikacji

        // pobranie szczegółów zadania przed aktualizacją
        $task = get_task_by_id($conn, $id);

        // pobranie pełnego imienia i nazwiska osoby zmieniającej status
        $user_updater = get_user_by_id($conn, $_SESSION['id']);
        $full_name = $user_updater ? $user_updater['full_name'] : 'Unknown User';

        $data = array($status, $id);
        update_task_status($conn, $data);

        // wysłanie powiadomienia do twórcy zadania o zmianie statusu (tylko jeśli to nie jest ta sama osoba)
        if ($task && !empty($task['created_by']) && $task['created_by'] != $_SESSION['id']) {
            $msg = $full_name . " changed the status of task '" . $task['title'] . "' to '" . $status . "'.";
            $notification_data = array($msg, $task['created_by'], 'Task Status Updated');
            insert_notifications($conn, $notification_data);
        }

        $error_message = "Task updated successfully"; # Using error message to show success message
        header('Location: ../my_tasks.php?success=Task updated successfully');
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

