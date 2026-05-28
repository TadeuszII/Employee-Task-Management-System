<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

    include "../DB_connection.php"; // Include the database connection file
    include "Model/task.php"; // Include the task model file to access task-related functions
    include "Model/user.php";
    include "Model/notification.php";
    $notifications = get_my_unread_notifications($conn, $_SESSION['id']); // Retrieve all notifications from the database for the current user

    if ($notifications == 0){ ?>

        <li> 
        <a href="#">
            You don't have any notifications
        </a>
        </li>

    <?php }else{
        
    

    foreach ( $notifications as $notification ) {

?>
    <li> 
        <a href="app/notification-read.php?notification_id=<?= $notification['id'] ?>">
            
            <?php if($notification['is_read'] == 0){
                echo '<mark>' . $notification['type'] . '</mark>:';
            }else echo $notification['type'] . ":" ?>
            ,,<?= $notification['message'] ?>"
            &nbsp;&nbsp;<small><?= (is_null($notification['date']) || $notification['date'] === '0000-00-00') ? 'No Due Date' : $notification['date'] ?></small>
        </a>
    </li>
<?php
    }
}
} else {
    echo 0;
}
?>