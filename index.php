<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/task.php"; // Include the task model file to access task-related functions
    include "app/Model/user.php"; // Include the user model file to access user-related functions
    include "app/Model/notification.php"; // Include the notification model file to access notification-related functions
    

    if ($_SESSION['role'] == 'admin'){
        // All tasks
        $all_tasks = get_all_tasks($conn); // Retrieve all tasks from the database
        $num_all_tasks = $all_tasks ? count($all_tasks) : 0; // Count the number of tasks retrieved

        // Due today tasks
        $due_today = get_all_tasks_due_today($conn);
        $num_due_today = $due_today ? count($due_today) : 0;

        // Overdue tasks
        $overdue_tasks = get_all_tasks_overdue($conn);
        $num_overdue_tasks = $overdue_tasks ? count($overdue_tasks) : 0;

        // No deadline tasks
        $no_deadline_tasks = get_all_tasks_no_deadline($conn);
        $num_no_deadline_tasks = $no_deadline_tasks ? count($no_deadline_tasks) : 0;

        // Get all users
        $users = get_all_users($conn); 
        $num_users = $users ? count($users) : 0; // Count the number of users retrieved

        // Get tasks by status
        $pending = get_tasks_by_status($conn, 'pending');
        $num_pending = $pending ? count($pending) : 0;

        // Get tasks by status
        $in_progress = get_tasks_by_status($conn, 'in_progress');
        $num_in_progress = $in_progress ? count($in_progress) : 0;

        // Get tasks by status
        $completed = get_tasks_by_status($conn, 'completed');
        $num_completed = $completed ? count($completed) : 0;

        // Get unread notifications for admin
        $notifications = get_my_unread_notifications($conn, $_SESSION['id']);
        $num_unread_notifications = $notifications ? count($notifications) : 0;
    }else{
        // All tasks employee
        $my_tasks = get_all_tasks_by_id($conn, $_SESSION['id']);
        $num_my_tasks = $my_tasks ? count($my_tasks) : 0;

        // Due today tasks employee
        $my_due_today = get_all_tasks_by_id_due_today($conn, $_SESSION['id']);
        $num_my_due_today = $my_due_today ? count($my_due_today) : 0;

        // Overdue tasks employee
        $my_overdue_tasks = get_all_tasks_by_id_overdue($conn, $_SESSION['id']);
        $num_my_overdue_tasks = $my_overdue_tasks ? count($my_overdue_tasks) : 0;

        // No deadline tasks employee
        $my_no_deadline_tasks = get_all_tasks_by_id_no_deadline($conn, $_SESSION['id']);
        $num_my_no_deadline_tasks = $my_no_deadline_tasks ? count($my_no_deadline_tasks) : 0;

        // Employee tasks STATUS pending
        $my_pending = get_tasks_by_id_status($conn, $_SESSION['id'], 'pending');
        $num_my_pending = $my_pending ? count($my_pending) : 0;

        // Employee tasks STATUS in_progress
        $my_in_progress = get_tasks_by_id_status($conn, $_SESSION['id'], 'in_progress');
        $num_my_in_progress = $my_in_progress ? count($my_in_progress) : 0;

        // Employee tasks STATUS completed
        $my_completed = get_tasks_by_id_status($conn, $_SESSION['id'], 'completed');
        $num_my_completed = $my_completed ? count($my_completed) : 0;

        $notifications = get_my_unread_notifications($conn, $_SESSION['id']);
        $num_unread_notifications = $notifications ? count($notifications) : 0;


    }

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
            
                <?php if ($_SESSION['role'] == 'admin') { ?> 
                    <div class="dashboard"> 
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='manage_users.php'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa fa-users"></i>
                        <span><?= $num_users ?> Users</span>                        
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa fa-tasks"></i>
                        <span><?= $num_all_tasks ?> All Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?due_date=Due%20Today'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-sun"></i>
                        <span><?= $num_due_today ?> Due Today</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?due_date=Overdue'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-rectangle-xmark"></i>
                        <span><?= $num_overdue_tasks ?> Overdue Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?due_date=No%20Deadline'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-clock"></i>
                        <span><?= $num_no_deadline_tasks ?> No Deadline Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-bell"></i>
                        <span><?= $num_unread_notifications ?> Unread Notifications</span>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?STATUS=pending'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span><?= $num_pending ?> Pending</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?STATUS=in_progress'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-spinner"></i>
                        <span><?= $num_in_progress ?> In Progress</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?STATUS=completed'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span><?= $num_completed ?> Completed</span>
                        </button>
                    </div>
                <?php } else { ?> 
                <!-- Employee -->
                <div class="dashboard"> 
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_tasks.php'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa fa-tasks"></i>
                        <span><?= $num_my_tasks ?> My Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        
                        <button class="view-btn" onclick="window.location.href='my_tasks.php?due_date=Due%20Today'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-sun"></i>
                        <span><?= $num_my_due_today ?> Due Today</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_tasks.php?due_date=Overdue'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-rectangle-xmark"></i>
                        <span><?= $num_my_overdue_tasks ?> Overdue Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_tasks.php?due_date=No%20Deadline'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-clock"></i>
                        <span><?= $num_my_no_deadline_tasks ?> No Deadline Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <i class="fa fa-bell"></i>
                        <span><?= $num_unread_notifications ?> Unread Notifications</span>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_tasks.php?STATUS=pending'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span><?= $num_my_pending ?> Pending</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_tasks.php?STATUS=in_progress'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-spinner"></i>
                        <span><?= $num_my_in_progress ?> In Progress</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_tasks.php?STATUS=completed'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span><?= $num_my_completed ?> Completed</span>
                        </button>
                    </div>
                </div>
                <?php } ?>                  

            </section>
        </div>




    </html>

    <script type="text/javascript">
        var active = document.querySelector("#navList li:nth-child(1)"); // Select the second list item in the navigation list
        active.classList.add("active"); // Add the "active" class to the selected list item
    </script>

<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>