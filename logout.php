<?php 
    session_start(); // Start the session to access session variables

    session_unset(); // Unset all session variables to clear the user's session data
    session_destroy(); // Destroy the session to log the user out

    header('Location: login.php'); // Redirect the user to the login page after logging out
    exit();
?>