<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/task.php"; // Include the task model file to access task-related functions
    include "app/Model/user.php"; // Include the user model file to access user-related functions
    include "app/Model/notification.php"; // Include the notification model file to access notification-related functions

    $notifications = get_all_my_notifications($conn, $_SESSION['id']); // Retrieve all notifications from the database for the current user

    // I think maybe add that user can see who assigned to him task
    //$users = get_all_users($conn); // Retrieve all users from the database "employee"

    $text = "All Task";

    if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") { // Check if the 'due_date' parameter is set in the URL
        $text = "Due Today";
        $tasks = get_all_tasks_by_id_due_today($conn, $_SESSION['id']); // Retrieve all tasks from the database
        // lets add if tasks zero then num task assign zero else count tasks
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") { // Check if the 'due_date' parameter is set in the URL
        $text = "Overdue";
        $tasks = get_all_tasks_by_id_overdue($conn, $_SESSION['id']); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") { // Check if the 'due_date' parameter is set in the URL
        $text = "No Deadline";
        $tasks = get_all_tasks_by_id_no_deadline($conn, $_SESSION['id']); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['STATUS']) && $_GET['STATUS'] == "pending") { // Check if the 'due_date' parameter is set in the URL
        $text = "Pending";
        $tasks = get_tasks_by_id_status($conn, $_SESSION['id'], 'pending'); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['STATUS']) && $_GET['STATUS'] == "in_progress") { // Check if the 'due_date' parameter is set in the URL
        $text = "In Progress";
        $tasks = get_tasks_by_id_status($conn, $_SESSION['id'], 'in_progress'); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['STATUS']) && $_GET['STATUS'] == "completed") { // Check if the 'due_date' parameter is set in the URL
        $text = "Completed";
        $tasks = get_tasks_by_id_status($conn, $_SESSION['id'], 'completed'); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }
    else { 
        $tasks = get_all_tasks_by_id($conn, $_SESSION['id']); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }



?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Notifications</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title-2">All Notifications</h4>
                <h4 class="title-2"><?= $text ?> (<?php echo $num_tasks; ?>) </h4>

                 <?php
                    if (isset($_GET['success'])) { ?>
                        <div class="success" role="alert">
                            <?php echo htmlspecialchars(stripcslashes($_GET['success']), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php }?>

                <?php if ($notifications != 0) { ?>

                    <table class="main-table">
                        <tr>
                            <th>#</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>

                            <!-- Idea to changed it to who assigned -->
                            <!-- <th>Assigned To</th> -->
                            
                        </tr>

                        <?php $i=0; foreach($notifications as $notification) { ?>

                        <tr>
                            <td><?=++$i?></td>
                            <td><?=$notification['message'] ?></td>
                            <td><?=$notification['type'] ?></td>
                            <td><?=$notification['date'] ?></td>
                            <!-- if is read == 1 should be read else not read -->
                            <td><?= $notification['is_read'] == 1 ? 'Read' : 'Unread' ?></td>

                        </tr>
                        <?php } ?>
                    </table>

                <?php } else { ?>
                    <h3>You don't have any notifications</h3>
                <?php } ?>

            </section>
        </div>





    </body>

    </html>

<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>