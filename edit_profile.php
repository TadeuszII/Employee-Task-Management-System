<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id']) && in_array($_SESSION['role'], ['admin', 'employee', 'manager'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/user.php"; // Include the user model file to access user-related functions

    $user = get_user_by_id($conn, $_SESSION['id']); // Retrieve the task from the database "employee"
    
?>  
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title">Edit Profile <a href="profile.php">Profile</a></h4>
                
                <form class="form-1"
                    action="app/update-profile.php"
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
                        <label for="full_name">Full Name:</label>
                        <input type="text" name="full_name" value="<?=$user["full_name"] ?>" class="input-1" placeholder="Full Name"><br>
                    </div>
                    <div class="form-1">
                        <label for="password">Old Password: </label><br>
                        <input type="text" name="password" value="*************"   class="input-1" placeholder="Old Password"><br>
                    </div>
                    <div class="form-1">
                        <label for="new_password">New Password: </label><br>
                        <input type="text" name="new_password"  class="input-1" placeholder="New Password"><br>
                    </div>
                    <div class="form-1">
                        <label for="confirm_password">Confirm Password: </label><br>
                        <input type="text" name="confirm_password"  class="input-1" placeholder="Confirm Password"><br><br>
                    </div>

                    <input type="text" name="id" value="<?=$user["id"]?>" hidden> 
                    <button class="edit-btn">Change</button>
                </form>
            </section>

        </div>


        <script type="text/javascript">
            var active = document.querySelector("#navList li:nth-child(3)"); // Select the second list item in the navigation list
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