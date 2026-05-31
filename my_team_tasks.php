<?php

session_start();

if (isset($_SESSION['role']) && $_SESSION['id'] && $_SESSION['role'] === 'admin') {

    include "DB_connection.php";
    include "app/Model/task.php";
    include "app/Model/user.php";

    $admin_id = $_SESSION['id'];

    // Get admin's direct reports — if none, redirect away
    $team = get_direct_reports($conn, $admin_id);
    if (empty($team)) {
        header('Location: index.php');
        exit();
    }

    // Employee filter from dropdown
    $filter_employee = (isset($_GET['employee']) && $_GET['employee'] != '') ? (int)$_GET['employee'] : null;

    // Validate that selected employee is actually in this admin's team
    if ($filter_employee) {
        $valid = false;
        foreach ($team as $member) {
            if ($member['id'] == $filter_employee) { $valid = true; break; }
        }
        if (!$valid) $filter_employee = null;
    }

    $text = "All Team Tasks";

    if ($filter_employee) {
        // Scoped to one employee
        if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") {
            $text  = "Due Today";
            $tasks = get_tasks_by_team($conn, $admin_id, 'employee_due_today', $filter_employee);
        } elseif (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") {
            $text  = "Overdue";
            $tasks = get_tasks_by_team($conn, $admin_id, 'employee_overdue', $filter_employee);
        } elseif (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") {
            $text  = "No Deadline";
            $tasks = get_tasks_by_team($conn, $admin_id, 'employee_no_deadline', $filter_employee);
        } elseif (isset($_GET['STATUS']) && $_GET['STATUS'] == "pending") {
            $text  = "Pending";
            $tasks = get_tasks_by_team($conn, $admin_id, 'employee_status', [$filter_employee, 'pending']);
        } elseif (isset($_GET['STATUS']) && $_GET['STATUS'] == "in_progress") {
            $text  = "In Progress";
            $tasks = get_tasks_by_team($conn, $admin_id, 'employee_status', [$filter_employee, 'in_progress']);
        } elseif (isset($_GET['STATUS']) && $_GET['STATUS'] == "completed") {
            $text  = "Completed";
            $tasks = get_tasks_by_team($conn, $admin_id, 'employee_status', [$filter_employee, 'completed']);
        } else {
            $tasks = get_tasks_by_team($conn, $admin_id, 'employee', $filter_employee);
        }
    } else {
        // All team members
        if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") {
            $text  = "Due Today";
            $tasks = get_tasks_by_team($conn, $admin_id, 'due_today');
        } elseif (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") {
            $text  = "Overdue";
            $tasks = get_tasks_by_team($conn, $admin_id, 'overdue');
        } elseif (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") {
            $text  = "No Deadline";
            $tasks = get_tasks_by_team($conn, $admin_id, 'no_deadline');
        } elseif (isset($_GET['STATUS']) && $_GET['STATUS'] == "pending") {
            $text  = "Pending";
            $tasks = get_tasks_by_team($conn, $admin_id, 'status', 'pending');
        } elseif (isset($_GET['STATUS']) && $_GET['STATUS'] == "in_progress") {
            $text  = "In Progress";
            $tasks = get_tasks_by_team($conn, $admin_id, 'status', 'in_progress');
        } elseif (isset($_GET['STATUS']) && $_GET['STATUS'] == "completed") {
            $text  = "Completed";
            $tasks = get_tasks_by_team($conn, $admin_id, 'status', 'completed');
        } else {
            $tasks = get_tasks_by_team($conn, $admin_id);
        }
    }

    $num_tasks = $tasks ? count($tasks) : 0;
    $all_users = get_all_assignable_users($conn); // for showing assigned_to full_name in table

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Team Tasks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title-2">
                <a href="create_task.php" class="btn">Create Task</a>
                <!-- Employee filter dropdown -->
                <select id="employee-filter" onchange="applyEmployeeFilter()" style="margin-left: 8px;">
                    <option value="">All Employees</option>
                    <?php foreach ($team as $member): ?>
                        <option value="<?= $member['id'] ?>" <?= ($filter_employee == $member['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($member['username']) ?> (<?= htmlspecialchars($member['role']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <a href="my_team_tasks.php?due_date=Due Today<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">Due Today</a>
                <a href="my_team_tasks.php?due_date=Overdue<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">Overdue</a>
                <a href="my_team_tasks.php?due_date=No Deadline<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">No Deadline</a>
                <a href="my_team_tasks.php?<?= $filter_employee ? 'employee='.$filter_employee : '' ?>">All Tasks</a>
                <a href="my_team_tasks.php?STATUS=pending<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">Pending</a>
                <a href="my_team_tasks.php?STATUS=in_progress<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">In Progress</a>
                <a href="my_team_tasks.php?STATUS=completed<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">Completed</a>
            </h4>

            <h4 class="title-2">
                My Team Tasks — <?php
                    if ($filter_employee) {
                        foreach ($team as $m) {
                            if ($m['id'] == $filter_employee) {
                                echo htmlspecialchars($m['username']) . ' (' . htmlspecialchars($m['role']) . ')';
                            }
                        }
                    } else {
                        echo 'All Members';
                    }
                ?> — <?= $text ?> (<?= $num_tasks ?>)
            </h4>

            <?php if (isset($_GET['success'])) { ?>
                <div class="success" role="alert"><?= stripcslashes($_GET['success']) ?></div>
            <?php } ?>

            <?php if ($tasks != 0) { ?>
                <table class="main-table">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php $i = 0; foreach ($tasks as $task) { ?>
                    <tr>
                        <td><?= ++$i ?></td>
                        <td><?= $task['title'] ?></td>
                        <td><?= $task['description'] ?></td>
                        <td>
                            <?php
                                $assigned = false;
                                // sprawdzamy czy użytkownik jest przypisany
                                if (is_array($all_users)) {
                                    foreach ($all_users as $user) {
                                        if ($user['id'] == $task['assigned_to']) {
                                            echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
                                            echo ' (' . htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') . ')';
                                            $assigned = true;
                                            break;
                                        }
                                    }
                                }
                                // jeśli nie ma użytkownika, wyświetlamy None
                                if (!$assigned) {
                                    echo '<span>None</span>';
                                }
                            ?>
                        </td>
                        <td><?= (is_null($task['due_date']) || $task['due_date'] === '0000-00-00') ? 'No Due Date' : $task['due_date'] ?></td>
                        <td><?= $task['STATUS'] ?></td>
                        <td>
                            <a href="edit-task.php?id=<?= $task['id'] ?>" class="edit-btn">Edit</a>
                            <a href="delete-task.php?id=<?= $task['id'] ?>" class="delete-btn">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <h3>No tasks found</h3>
            <?php } ?>
        </section>
    </div>

    <script type="text/javascript">
        // Highlight My Team Tasks in nav
        var currentPage = window.location.pathname.split('/').pop();
        document.querySelectorAll('#navList li').forEach(function(li) {
            if (li.getAttribute('data-page') === currentPage) {
                li.classList.add('active');
            }
        });
    </script>
    <script type="text/javascript">
        function applyEmployeeFilter() {
            var employeeId = document.getElementById('employee-filter').value;
            var params = new URLSearchParams(window.location.search);
            if (employeeId) {
                params.set('employee', employeeId);
            } else {
                params.delete('employee');
            }
            window.location.href = 'my_team_tasks.php?' + params.toString();
        }
    </script>
    <!-- skrypt do sortowania tabeli -->
    <script src="js/table-sorter.js"></script>
</body>
</html>

<?php } else {
    header('Location: login.php?error=Login at first');
    exit();
}
?>