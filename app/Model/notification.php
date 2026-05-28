<?php 

// get all unread notifications for a specific user
function get_all_my_notifications($conn, $id){ 
    $sql = "SELECT * FROM notifications WHERE recipient = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0){
        $notifications = $stmt->fetchAll();

    }else{
        $notifications = 0;
    }
    return $notifications;
}

// get all unread notifications for a specific user
function get_my_unread_notifications( $conn, $id ){
    $sql = "SELECT * FROM notifications WHERE recipient = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0){
        $notifications = $stmt->fetchAll();

    }else{
        $notifications = 0;
    }
    return $notifications;
}


// count all notifications for a specific user
function count_my_notifications($conn, $id){
    $sql = "SELECT * FROM notifications WHERE recipient = ? AND is_read=0";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->rowCount();
}

// insert a new notification into the database
function insert_notifications($conn, $data){
    $sql = "INSERT INTO notifications (message, recipient, type) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);

}

// mark a notification as read
function notification_make_read($conn, $recipient, $notification_id){ 
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND recipient = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$notification_id, $recipient]);
}

?>

