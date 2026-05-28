<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/user.php"; // Include the user model file to access user-related functions

    if (!isset($_GET['id'])) {
        $error_message = "User id is required";
        header('Location: manage_users.php?error=' . $error_message);
        exit();
    }
    $id = $_GET['id'];
    
    $user = get_user_by_id($conn, $id); // Retrieve all users from the database "employee"
    
    if ($user == 0) {
         $error_message = "User not found";
        header('Location: manage_users.php?error=' . $error_message);
        exit();
    }

?>  
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit User</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title">Edit User <a href="manage_users.php">Users</a></h4>

                <form class="form-1"
                    action="app/update-user.php"
                    method="POST">

                    <!-- Error Message -->
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="warning" role="alert">
                            <?php echo htmlspecialchars(stripcslashes($_GET['error']), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php } ?>
                    <!-- Success Message -->
                    <?php
                    if (isset($_GET['success'])) { ?>
                        <div class="success" role="alert">
                            <?php echo htmlspecialchars(stripcslashes($_GET['success']), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php }?>

                    <div class="input-holder">
                        <input type="text" name="full_name" value="<?=$user["full_name"] ?>" class="input-1" placeholder="Full Name"><br><br>
                    </div>

                    <div class="form-1">
                        <input type="text" name="user_name" value="<?=$user["username"] ?>" class="input-1" placeholder="Username"><br><br>
                    </div>
                    <div class="form-1">
                        <input type="text" name="password"  class="input-1" placeholder="Password"><br><br>
                    </div>
                     <div class="input-holder">
                        <select name="role" class="input-1">
                            <option value="employee" <?= $user['role'] == 'employee' ? 'selected' : '' ?>>Employee</option>
                            <option value="manager"  <?= $user['role'] == 'manager'  ? 'selected' : '' ?>>Manager</option>
                            <option value="admin"    <?= $user['role'] == 'admin'    ? 'selected' : '' ?>>Admin</option>
                        </select><br><br>
                    </div>
                    <input type="text" name="id" value="<?=$user["id"]?>" hidden> 
                    <button class="edit-btn">Update</button>
                </form>
            </section>

        </div>


        <script type="text/javascript">
            var active = document.querySelector("#navList li:nth-child(2)"); // Select the second list item in the navigation list
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