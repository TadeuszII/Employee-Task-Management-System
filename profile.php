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
                <h4 class="title">Profile <a href="edit_profile.php">Edit Profile</a></h4>
                <table class="main-table" style="max-width: 300px; margin-left: 0;">
                    <tr>
                        <td>Full Name</td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                    </tr>
                    <tr>
                        <td>Team joining date</td>
                        <!-- Wyświetlanie poprawnej daty i liczby dni od dołączenia -->
                        <td>
                            <?php 
                            $joined_date = strtotime($user['created_at']);
                            $diff_seconds = time() - $joined_date;
                            $diff_days = floor($diff_seconds / (60 * 60 * 24));
                            
                            $days_text = "";
                            if ($diff_days <= 0) {
                                $days_text = "today";
                            } else if ($diff_days == 1) {
                                $days_text = "1 day ago";
                            } else {
                                $days_text = $diff_days . " days ago";
                            }
                            ?>
                            <?= htmlspecialchars($user['created_at']) ?> (<?= $days_text ?>)
                        </td>
                    </tr>
                </table>
            </section>

        </div>



    </body>

    </html>

<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>