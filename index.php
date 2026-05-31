<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/task.php"; // Include the task model file to access task-related functions
    include "app/Model/user.php"; // Include the user model file to access user-related functions
    include "app/Model/notification.php"; // Include the notification model file to access notification-related functions

    if ($_SESSION['role'] == 'admin') {

        // All tasks (system-wide)
        $all_tasks     = get_all_tasks($conn);
        $num_all_tasks = $all_tasks ? count($all_tasks) : 0;

        $due_today     = get_all_tasks_due_today($conn);
        $num_due_today = $due_today ? count($due_today) : 0;

        $overdue_tasks     = get_all_tasks_overdue($conn);
        $num_overdue_tasks = $overdue_tasks ? count($overdue_tasks) : 0;

        $no_deadline_tasks     = get_all_tasks_no_deadline($conn);
        $num_no_deadline_tasks = $no_deadline_tasks ? count($no_deadline_tasks) : 0;

        $users     = get_all_users($conn);
        $num_users = $users ? count($users) : 0;

        $pending     = get_tasks_by_status($conn, 'pending');
        $num_pending = $pending ? count($pending) : 0;

        $in_progress     = get_tasks_by_status($conn, 'in_progress');
        $num_in_progress = $in_progress ? count($in_progress) : 0;

        $completed     = get_tasks_by_status($conn, 'completed');
        $num_completed = $completed ? count($completed) : 0;

        $notifications             = get_my_unread_notifications($conn, $_SESSION['id']);
        $num_unread_notifications  = $notifications ? count($notifications) : 0;

        // My Team — direct reports of this admin
        $admin_team = get_direct_reports($conn, $_SESSION['id']);
        $has_team   = !empty($admin_team);

        if ($has_team) {
            $team_all       = get_tasks_by_team($conn, $_SESSION['id']);
            $num_team_tasks = $team_all ? count($team_all) : 0;

            $team_due_today       = get_tasks_by_team($conn, $_SESSION['id'], 'due_today');
            $num_team_due_today   = $team_due_today ? count($team_due_today) : 0;

            $team_overdue         = get_tasks_by_team($conn, $_SESSION['id'], 'overdue');
            $num_team_overdue     = $team_overdue ? count($team_overdue) : 0;

            $team_no_deadline     = get_tasks_by_team($conn, $_SESSION['id'], 'no_deadline');
            $num_team_no_deadline = $team_no_deadline ? count($team_no_deadline) : 0;

            $team_pending         = get_tasks_by_team($conn, $_SESSION['id'], 'status', 'pending');
            $num_team_pending     = $team_pending ? count($team_pending) : 0;

            $team_in_progress     = get_tasks_by_team($conn, $_SESSION['id'], 'status', 'in_progress');
            $num_team_in_progress = $team_in_progress ? count($team_in_progress) : 0;

            $team_completed       = get_tasks_by_team($conn, $_SESSION['id'], 'status', 'completed');
            $num_team_completed   = $team_completed ? count($team_completed) : 0;

            $num_team_users = count($admin_team);
        }

    } elseif ($_SESSION['role'] == 'manager') {

        // Manager personal tasks (assigned directly to manager)
        $my_tasks     = get_all_tasks_by_id($conn, $_SESSION['id']);
        $num_my_tasks = $my_tasks ? count($my_tasks) : 0;

        $my_due_today     = get_all_tasks_by_id_due_today($conn, $_SESSION['id']);
        $num_my_due_today = $my_due_today ? count($my_due_today) : 0;

        $my_overdue_tasks     = get_all_tasks_by_id_overdue($conn, $_SESSION['id']);
        $num_my_overdue_tasks = $my_overdue_tasks ? count($my_overdue_tasks) : 0;

        $my_no_deadline_tasks     = get_all_tasks_by_id_no_deadline($conn, $_SESSION['id']);
        $num_my_no_deadline_tasks = $my_no_deadline_tasks ? count($my_no_deadline_tasks) : 0;

        $my_pending     = get_tasks_by_id_status($conn, $_SESSION['id'], 'pending');
        $num_my_pending = $my_pending ? count($my_pending) : 0;

        $my_in_progress     = get_tasks_by_id_status($conn, $_SESSION['id'], 'in_progress');
        $num_my_in_progress = $my_in_progress ? count($my_in_progress) : 0;

        $my_completed     = get_tasks_by_id_status($conn, $_SESSION['id'], 'completed');
        $num_my_completed = $my_completed ? count($my_completed) : 0;

        // zadania zespołu (pracownicy ze wskazaniem na manager_id tego menedżera)
        $team_all       = get_tasks_scoped($conn, 'manager', $_SESSION['id']);
        $num_team_tasks = $team_all ? count($team_all) : 0;

        $team_due_today       = get_tasks_scoped($conn, 'manager', $_SESSION['id'], 'due_today');
        $num_team_due_today   = $team_due_today ? count($team_due_today) : 0;

        $team_overdue         = get_tasks_scoped($conn, 'manager', $_SESSION['id'], 'overdue');
        $num_team_overdue     = $team_overdue ? count($team_overdue) : 0;

        $team_no_deadline     = get_tasks_scoped($conn, 'manager', $_SESSION['id'], 'no_deadline');
        $num_team_no_deadline = $team_no_deadline ? count($team_no_deadline) : 0;

        $team_pending         = get_tasks_scoped($conn, 'manager', $_SESSION['id'], 'status', 'pending');
        $num_team_pending     = $team_pending ? count($team_pending) : 0;

        $team_in_progress     = get_tasks_scoped($conn, 'manager', $_SESSION['id'], 'status', 'in_progress');
        $num_team_in_progress = $team_in_progress ? count($team_in_progress) : 0;

        $team_completed       = get_tasks_scoped($conn, 'manager', $_SESSION['id'], 'status', 'completed');
        $num_team_completed   = $team_completed ? count($team_completed) : 0;

        $notifications            = get_my_unread_notifications($conn, $_SESSION['id']);
        $num_unread_notifications = $notifications ? count($notifications) : 0;

    } else {

        // Employee
        $my_tasks     = get_all_tasks_by_id($conn, $_SESSION['id']);
        $num_my_tasks = $my_tasks ? count($my_tasks) : 0;

        $my_due_today     = get_all_tasks_by_id_due_today($conn, $_SESSION['id']);
        $num_my_due_today = $my_due_today ? count($my_due_today) : 0;

        $my_overdue_tasks     = get_all_tasks_by_id_overdue($conn, $_SESSION['id']);
        $num_my_overdue_tasks = $my_overdue_tasks ? count($my_overdue_tasks) : 0;

        $my_no_deadline_tasks     = get_all_tasks_by_id_no_deadline($conn, $_SESSION['id']);
        $num_my_no_deadline_tasks = $my_no_deadline_tasks ? count($my_no_deadline_tasks) : 0;

        $my_pending     = get_tasks_by_id_status($conn, $_SESSION['id'], 'pending');
        $num_my_pending = $my_pending ? count($my_pending) : 0;

        $my_in_progress     = get_tasks_by_id_status($conn, $_SESSION['id'], 'in_progress');
        $num_my_in_progress = $my_in_progress ? count($my_in_progress) : 0;

        $my_completed     = get_tasks_by_id_status($conn, $_SESSION['id'], 'completed');
        $num_my_completed = $my_completed ? count($my_completed) : 0;

        $notifications            = get_my_unread_notifications($conn, $_SESSION['id']);
        $num_unread_notifications = $notifications ? count($notifications) : 0;

    }

    // sprawdzenie czy są zadania na dziś
    $has_tasks_due_today = false;
    $due_today_count = 0;
    if ($_SESSION['role'] == 'admin') {
        if ($num_due_today > 0) {
            $has_tasks_due_today = true;
            $due_today_count = $num_due_today;
        }
    } else {
        if ($num_my_due_today > 0) {
            $has_tasks_due_today = true;
            $due_today_count = $num_my_due_today;
        }
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

            <!-- alert o zadaniach na dzisiaj -->
            <?php if ($has_tasks_due_today) { ?>
                <div class="warning" style="background-color: #ff9800; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Attention: You have <?= $due_today_count ?> task(s) due today!
                </div>
            <?php } ?>

            <?php if ($_SESSION['role'] == 'admin') { ?>

                <!-- ── ADMIN: All Tasks (system-wide) ── -->
                <h4 class="title-2">All Tasks</h4>
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
                </div>

                <!-- ── ADMIN: My Team (only if admin has direct reports) ── -->
                <?php if ($has_team) { ?>
                <h4 class="title-2">My Team</h4>
                <div class="dashboard">
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_team_tasks.php'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-people-group"></i>
                            <span><?= $num_team_users ?> Team Members</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_team_tasks.php'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa fa-tasks"></i>
                            <span><?= $num_team_tasks ?> Team Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_team_tasks.php?due_date=Due%20Today'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-sun"></i>
                            <span><?= $num_team_due_today ?> Due Today</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_team_tasks.php?due_date=Overdue'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-rectangle-xmark"></i>
                            <span><?= $num_team_overdue ?> Overdue Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_team_tasks.php?due_date=No%20Deadline'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-clock"></i>
                            <span><?= $num_team_no_deadline ?> No Deadline Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_team_tasks.php?STATUS=pending'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span><?= $num_team_pending ?> Pending</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_team_tasks.php?STATUS=in_progress'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-spinner"></i>
                            <span><?= $num_team_in_progress ?> In Progress</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='my_team_tasks.php?STATUS=completed'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-clipboard-check"></i>
                            <span><?= $num_team_completed ?> Completed</span>
                        </button>
                    </div>
                </div>
                <?php } // end has_team ?>

            <?php } elseif ($_SESSION['role'] == 'manager') { ?>

                <!-- ── MANAGER: My Tasks (personal) ── -->
                <h4 class="title-2">My Tasks</h4>
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

                <!-- ── MANAGER: My Team's Tasks ── -->
                <h4 class="title-2">My Team's Tasks</h4>
                <div class="dashboard">
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-people-group"></i>
                            <span><?= $num_team_tasks ?> Team Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?due_date=Due%20Today'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-sun"></i>
                            <span><?= $num_team_due_today ?> Due Today</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?due_date=Overdue'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-rectangle-xmark"></i>
                            <span><?= $num_team_overdue ?> Overdue Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?due_date=No%20Deadline'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-clock"></i>
                            <span><?= $num_team_no_deadline ?> No Deadline Tasks</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?STATUS=pending'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span><?= $num_team_pending ?> Pending</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?STATUS=in_progress'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-spinner"></i>
                            <span><?= $num_team_in_progress ?> In Progress</span>
                        </button>
                    </div>
                    <div class="dashboard-item">
                        <button class="view-btn" onclick="window.location.href='all_tasks.php?STATUS=completed'" style="background-color: transparent; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-clipboard-check"></i>
                            <span><?= $num_team_completed ?> Completed</span>
                        </button>
                    </div>
                </div>

            <?php } else { ?>

                <!-- ── EMPLOYEE ── -->
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

<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>