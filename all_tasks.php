<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/task.php"; // Include the task model file to access task-related functions
    include "app/Model/user.php"; // Include the user model file to access user-related functions

    $tasks = get_all_tasks($conn); // Retrieve all tasks from the database
    $users = get_all_users($conn); // Retrieve all users from the database "employee"


?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>All tasks</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title">All Taks <a href="create_task.php">Create Task</a></h4>

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
                                    foreach($users as $user){
                                        if($user['id'] == $task['assigned_to']){
                                            echo $user['full_name'];
                                        }
                                    }
                                ?>
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
    </body>

    </html>

<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>