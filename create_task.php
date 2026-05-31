<?php


session_start(); // Start the session to access session variables


if (isset($_SESSION['role']) && isset($_SESSION['id']) && in_array($_SESSION['role'], ['admin', 'manager'])) { // Check if the user is logged in by verifying session variables


    include "DB_connection.php"; // Include the database connection file
    include "app/Model/user.php"; // Include the user model file to access user-related functions


    // Role-based user list for task assignment
    if ($_SESSION['role'] === 'admin') {
        $users = get_all_assignable_users_admin($conn); // Admin sees all employees and managers
    } else {
        $users = get_assignable_users_for_manager($conn, $_SESSION['id']); // Manager sees only their own employees and other managers
    }



?>
    <!DOCTYPE html>
    <html lang="en">


    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Task</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/ai-panel.css">



    </head>


    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">


            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title">Create Task</h4>


                <form class="form-1"
                    action="app/add-task.php"
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
                        <input type="text" id="taskTitle" name="title" class="input-1" placeholder="Task Title"><br><br>
                    </div>
                    <div class="input-holder">
                        <textarea id="taskDescription" name="description" class="input-1" placeholder="Task Description"></textarea><br>
                    </div>
                    <div class="input-holder">
                        <label for="due_date">Due Date: </label>
                        <input type="date" name="due_date" class="input-1" placeholder="Due Date"><br>
                    </div>
                    <div class="input-holder">
                        <select name="assigned_to" class="input-1">
                            <option value="0">Select Employee</option>
                            <?php 
                            if ($users != 0)
                                foreach($users as $user) { ?>
                                    <option value="<?=$user['id']?>">
                                        <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?> (<?= $user['role'] ?>)
                                    </option>
                            <?php } ?>
                        </select><br><br>
                    </div>
                    
                    <button class="edit-btn">Create Task</button>
                </form>



                    <!-- AI Assistant Panel -->
                    <?php include "inc/ai-panel.php"; ?>
            </section>
        </div>
    


        <script type="text/javascript">
            var active = document.querySelector("#navList li:nth-child(3)"); // Select the third list item in the navigation list
            active.classList.add("active"); // Add the "active" class to the selected list item
        </script>
        <script src="js/ai-panel.js"></script>
    </body>


    </html>


<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>