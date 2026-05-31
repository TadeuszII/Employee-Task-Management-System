<?php



session_start(); // Start the session to access session variables



if (isset($_SESSION['role']) && isset($_SESSION['id']) && in_array($_SESSION['role'], ['admin', 'manager'])) { // Check if the user is logged in by verifying session variables



    include "DB_connection.php"; // Include the database connection file
    include "app/Model/task.php"; // Include the task model file to access task-related functions
    include "app/Model/user.php"; // Include the user model file to access user-related functions



    $text = "All Task";
    $role = $_SESSION['role'];
    $id   = $_SESSION['id'];

    // If manager selected a specific employee from the dropdown, scope tasks to that employee only
    $filter_employee = ($role === 'manager' && isset($_GET['employee']) && $_GET['employee'] != '') ? (int)$_GET['employee'] : null;
    $scope_id   = ($filter_employee) ? $filter_employee : $id;
    $scope_role = ($filter_employee) ? 'employee' : $role;



    if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") { // Check if the 'due_date' parameter is set in the URL
        $text = "Due Today";
        $tasks = get_tasks_scoped($conn, $scope_role, $scope_id, 'due_today'); // Retrieve all tasks from the database
        // lets add if tasks zero then num task assign zero else count tasks
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") { // Check if the 'due_date' parameter is set in the URL
        $text = "Overdue";
        $tasks = get_tasks_scoped($conn, $scope_role, $scope_id, 'overdue'); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") { // Check if the 'due_date' parameter is set in the URL
        $text = "No Deadline";
        $tasks = get_tasks_scoped($conn, $scope_role, $scope_id, 'no_deadline'); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['STATUS']) && $_GET['STATUS'] == "pending") { // Check if the 'due_date' parameter is set in the URL
        $text = "Pending";
        $tasks = get_tasks_scoped($conn, $scope_role, $scope_id, 'status', 'pending'); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['STATUS']) && $_GET['STATUS'] == "in_progress") { // Check if the 'due_date' parameter is set in the URL
        $text = "In Progress";
        $tasks = get_tasks_scoped($conn, $scope_role, $scope_id, 'status', 'in_progress'); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }else if (isset($_GET['STATUS']) && $_GET['STATUS'] == "completed") { // Check if the 'due_date' parameter is set in the URL
        $text = "Completed";
        $tasks = get_tasks_scoped($conn, $scope_role, $scope_id, 'status', 'completed'); // Retrieve all tasks from the database
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }
    else { 
        $tasks = get_tasks_scoped($conn, $scope_role, $scope_id); // Retrieve all tasks scoped to role
        $num_tasks = $tasks ? count($tasks) : 0; // Count the number of tasks retrieved
    }



    $users = get_all_assignable_users($conn); // Retrieve all users from the database "employee"

    // For manager dropdown: fetch only employees assigned under this manager
    $manager_employees = ($role === 'manager') ? get_assignable_users_for_manager($conn, $id) : [];



    




?>
    <!DOCTYPE html>
    <html lang="en">



    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $role === 'manager' ? 'My Team Tasks' : 'All tasks' ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">




    </head>



    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">



            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title-2">
                    <a href="create_task.php" class="btn">Create Task</a>
                    <!-- Manager sees employee dropdown to filter tasks by specific employee -->
                    <?php if ($role === 'manager') { ?>
                    <select id="employee-filter" onchange="applyEmployeeFilter()" style="margin-left: 8px;">
                        <option value="">All Employees</option>
                        <?php if ($manager_employees): foreach($manager_employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($filter_employee == $emp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['full_name']) ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                    <?php } ?>
                    <a href="all_tasks.php?due_date=Due Today<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">Due Today</a>
                    <a href="all_tasks.php?due_date=Overdue<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">Overdue</a>
                    <a href="all_tasks.php?due_date=No Deadline<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">No Deadline</a>
                    <a href="all_tasks.php?<?= $filter_employee ? 'employee='.$filter_employee : '' ?>">All Tasks</a>
                    <a href="all_tasks.php?STATUS=pending<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">Pending</a>
                    <a href="all_tasks.php?STATUS=in_progress<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">In Progress</a>
                    <a href="all_tasks.php?STATUS=completed<?= $filter_employee ? '&employee='.$filter_employee : '' ?>">Completed</a>



                </h4>



                <h4 class="title-2"><?= $role === 'manager' ? 'My Team Tasks' : $text ?> <?= $filter_employee ? '— ' . htmlspecialchars($manager_employees[array_search($filter_employee, array_column($manager_employees, 'id'))]['full_name'] ?? '') : '' ?> (<?php echo $num_tasks; ?>) </h4>



                 <?php
                    if (isset($_GET['success'])) { ?>
                        <div class="success" role="alert">
                            <?php echo stripcslashes($_GET['success']); ?>
                        </div>
                    <?php }?>



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



                        <?php $i=0; foreach($tasks as $task) { ?>



                        <tr>
                            <td><?=++$i?></td>
                            <td><?=$task['title'] ?></td>
                            <td><?=$task['description'] ?></td>
                            <!-- lets show to who assigned full_name of user -->
                            <td>
                                <?php
                                    $assigned = false;
                                    // sprawdzamy czy użytkownik jest przypisany
                                    if (is_array($users)) {
                                        foreach($users as $user){
                                            if($user['id'] == $task['assigned_to']){
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
                            <td><?=$task['STATUS']  ?></td>
                            <td>
                                <a href="edit-task.php?id=<?=$task['id']?>" class="edit-btn">Edit</a> <!-- Link to edit task with id task -->
                                <a href="delete-task.php?id=<?=$task['id']?>" class="delete-btn">Delete</a> <!-- Link to delete task with id task -->
                            </td>
                        </tr>
                        <?php } ?>
                    </table>



                <?php } else { ?>
                    <h3>Empty User List</h3>
                <?php } ?>



            </section>
        </div>




        <script type="text/javascript">
            var active = document.querySelector("#navList li:nth-child(4)"); // Select the second list item in the navigation list
            active.classList.add("active"); // Add the "active" class to the selected list item
        </script>

        <?php if ($role === 'manager') { ?>
        <script type="text/javascript">
            // When manager changes the employee dropdown, reload the page preserving any active filter
            function applyEmployeeFilter() {
                var employeeId = document.getElementById('employee-filter').value;
                var params = new URLSearchParams(window.location.search);
                if (employeeId) {
                    params.set('employee', employeeId);
                } else {
                    params.delete('employee');
                }
                window.location.href = 'all_tasks.php?' + params.toString();
            }
        </script>
        <?php } ?>
        <!-- skrypt do sortowania tabeli -->
        <script src="js/table-sorter.js"></script>
    </body>



    </html>



<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>