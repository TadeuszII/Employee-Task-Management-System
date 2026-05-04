<?php 
    $sName = "localhost"; // Server name or IP address of the database server
    $uName = "root"; // Username for the database connection
    $pass = ""; // Password for the database 
    $db_name = "employee_task_management_system"; // Name of the database to connect to

    try { // Try to establish a connection to the database using PDO (PHP Data Objects)
        $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass); // Create a new PDO instance with the specified connection parameters
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set the error mode to exception to handle any connection errors
    }catch(PDOException $error){ // Catch any PDO exceptions that occur during the connection attempt
        echo "Connection to database failed: " . $error->getMessage() . "<br>"; // Output an error message if the connection fails
        exit();
    }

?>