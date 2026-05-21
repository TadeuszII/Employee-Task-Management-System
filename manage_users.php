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
        <title>Manage Users</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title">Manage Users <a href="add-user.php">Add User</a></h4>

                 <?php
                    if (isset($_GET['success'])) { ?>
                        <div class="success" role="alert">
                            <?php echo stripcslashes($_GET['success']); ?>
                        </div>
                    <?php }?>

                <?php if ($users != 0) { ?>

                    <table class="main-table">
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>User Name</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>

                        <?php $i=0; foreach($users as $user) { ?>

                        <tr>
                            <td><?=++$i?></td>
                            <td><?=$user['full_name'] ?></td>
                            <td><?=$user['username'] ?></td>
                            <td><?=$user['role'] ?></td>
                            <td>
                                <a href="edit-user.php?id=<?=$user['id']?>" class="edit-btn">Edit</a> <!-- Link to edit user with id user -->
                                <a href="delete-user.php?id=<?=$user['id']?>" class="delete-btn">Delete</a> <!-- Link to delete user with id user -->
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