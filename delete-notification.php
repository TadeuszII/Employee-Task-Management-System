<?php
session_start();

if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    if (isset($_GET['id'])) {
        include "DB_connection.php";
        
        $id = (int)$_GET['id'];
        $recipient = $_SESSION['id'];
        
        // usuwanie powiadomienia przypisanego do zalogowanego użytkownika
        $sql = "DELETE FROM notifications WHERE id = ? AND recipient = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id, $recipient]);
        
        header('Location: notifications.php?success=Notification deleted successfully');
        exit();
    } else {
        header('Location: notifications.php');
        exit();
    }
} else {
    header('Location: login.php?error=Login at first');
    exit();
}
?>
