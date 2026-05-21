<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/task.php"; // Include the user model file to access user-related functions
    include "app/Model/user.php"; // Include the user model file to access user-related functions

    if (!isset($_GET['id'])) {
        $error_message = "Task id is required";
        header('Location: all_tasks.php?error=' . $error_message);
        exit();
    }
    $id = $_GET['id'];
    
    $task = get_task_by_id($conn, $id); // Retrieve the task from the database "employee"
    $users = get_all_users($conn); // Retrieve all users from the database "employee"
    
    if ($task == 0) {
         $error_message = "Task not found";
        header('Location: all_tasks.php?error=' . $error_message);
        exit();
    }

?>  
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Task</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title">Edit Task <a href="all_tasks.php">Tasks</a></h4>

                <form class="form-1"
                    action="app/update-task.php"
                    method="POST">

                    <!-- Error Message -->
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="warning" role="alert">
                            <?php echo stripcslashes($_GET['error']); ?>
                        </div>
                    <?php } ?>
                    <!-- Success Message -->
                    <?php
                    if (isset($_GET['success'])) { ?>
                        <div class="success" role="alert">
                            <?php echo stripcslashes($_GET['success']); ?>
                        </div>
                    <?php }?>
        
                    <div class="input-holder">
                        <input type="text" name="title" class="input-1" value="<?=$task["title"] ?>" placeholder="Task Title"><br><br>
                    </div>
                    <div class="input-holder">
                        <textarea name="description" class="input-1" placeholder="Task Description"><?=$task["description"] ?></textarea><br><br>
                    </div>
                    <div class="input-holder">
                        <select name="assigned_to" class="input-1">
                            <option value="0">Select Employee</option>
                            <?php 
                            if ($users != 0)
                                foreach($users as $user) { ?>
                                    <option value="<?=$user['id']?>" <?php echo ($task['assigned_to'] == $user['id']) ? 'selected' : ''; ?>><?=$user['full_name']?></option>
                            <?php } ?>
                        </select><br><br>
                    </div>
                    <input type="hidden" name="id" value="<?=$task['id']?>">
                    <button class="edit-btn">Update Task</button>
                </form>
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