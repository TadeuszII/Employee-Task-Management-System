<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "DB_connection.php"; // Include the database connection file
    include "app/Model/task.php"; // Include the task model file to access task-related functions
    include "app/Model/user.php"; // Include the user model file to access user-related functions
    include "app/Model/notification.php"; // Include the notification model file to access notification-related functions

    $notifications = get_all_my_notifications($conn, $_SESSION['id']); // Retrieve all notifications from the database for the current user

    // obliczenie liczby powiadomień
    $num_notifications = $notifications ? count($notifications) : 0;

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Notifications</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title-2">All Notifications (<?= $num_notifications ?>)</h4>

                 <?php
                    if (isset($_GET['success'])) { ?>
                        <div class="success" role="alert">
                            <?php echo htmlspecialchars(stripcslashes($_GET['success']), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php }?>

                <?php if ($notifications != 0) { ?>

                    <table class="main-table">
                        <tr>
                            <th>#</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>

                        <?php $i=0; foreach($notifications as $notification) { ?>

                        <tr>
                            <td><?=++$i?></td>
                            <td><?=$notification['message'] ?></td>
                            <td><?=$notification['type'] ?></td>
                            <td><?=$notification['date'] ?></td>
                            <!-- if is read == 1 should be read else not read -->
                            <td><?= $notification['is_read'] == 1 ? 'Read' : 'Unread' ?></td>
                            <td>
                                <a href="delete-notification.php?id=<?=$notification['id']?>" class="delete-btn">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>

                <?php } else { ?>
                    <h3>You don't have any notifications</h3>
                <?php } ?>

            </section>
        </div>
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