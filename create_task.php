<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/user.php"; // Include the user model file to access user-related functions

    $users = get_all_users($conn); // Retrieve all users from the database "employee"


?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Task</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


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
                        <input type="text" name="title" class="input-1" placeholder="Task Title"><br><br>
                    </div>
                    <div class="input-holder">
                        <textarea name="description" class="input-1" placeholder="Task Description"></textarea><br><br>
                    </div>
                    <div class="input-holder">
                        <select name="assigned_to" class="input-1">
                            <option value="0">Select Employee</option>
                            <?php 
                            if ($users != 0)
                                foreach($users as $user) { ?>
                                    <option value="<?=$user['id']?>"><?=$user['full_name']?></option>
                            <?php } ?>
                        </select><br><br>
                    </div>
                    
                    <button class="edit-btn">Create Task</button>
                </form>

            </section>
        </div>


        <script type="text/javascript">
            var active = document.querySelector("#navList li:nth-child(3)"); // Select the third list item in the navigation list
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