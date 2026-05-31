<?php
session_start();


if (isset($_SESSION['role']) && isset($_SESSION['id']) && in_array($_SESSION['role'], ['admin', 'manager'])) {


    include "DB_connection.php";
    include "app/Model/task.php";

    if (!isset($_GET['id'])) {
        header('Location: all_tasks.php?error=Task id is required');
        exit();
    }

    $id   = (int)$_GET['id'];
    $task = get_task_by_id($conn, $id);

    if ($task == 0) {
        header('Location: all_tasks.php?error=Task not found');
        exit();
    }

    
    if ($_SESSION['role'] === 'manager' && $task['created_by'] != $_SESSION['id']) {
        header('Location: all_tasks.php?error=You can only delete tasks you created');
        exit();
    }
    
    // wysłanie powiadomienia o usunięciu zadania (jeśli to nie jest ta sama osoba)
    if ($task && !empty($task['assigned_to']) && $task['assigned_to'] != $_SESSION['id']) {
        include "app/Model/notification.php"; // pobranie modelu powiadomień
        $msg = "Task '" . $task['title'] . "' has been deleted by supervisor.";
        $notification_data = array($msg, $task['assigned_to'], 'Task Deleted');
        insert_notifications($conn, $notification_data);
    }

    delete_task($conn, [$id]);
    header('Location: all_tasks.php?success=Task deleted successfully');
    exit();

} else {
    header('Location: login.php?error=Login at first');
    exit();
}
?>