<?php

session_start(); // Start the session to access session variables

if (isset($_SESSION['role']) && isset($_SESSION['id'])) { // Check if the user is logged in by verifying session variables

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">


    </head>

    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">

            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">

            </section>
        </div>




    </html>

<?php } else {
    $error_message = "Login at first";
    header('Location: ../login.php?error=' . $error_message);
    exit();
}
?>