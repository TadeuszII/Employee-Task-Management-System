<?php
session_start();
if (isset($_POST['user_name']) && isset($_POST['password'])) {


    include "../DB_connection.php"; // Include the database connection file to establish a connection to the database


    function validate_input($data) // Function to sanitize user input
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $user_name = validate_input($_POST['user_name']);
    $password = validate_input($_POST['password']);

    if (empty($user_name)) { // Check if the username field is empty
        $error_message = "Username is required";
        header('Location: ../login.php?error=' . $error_message);
        exit();
    } else if (empty($password)) { // Check if the password field is empty
        $error_message = "Password is required";
        header('Location: ../login.php?error=' . $error_message);
        exit();
    } else { // If both fields are filled, proceed with database verification
        $sql = "SELECT * FROM user WHERE username = ?"; // Prepare SQL statement to select user with the provided username
        $stmt = $conn->prepare($sql); // Prepare the SQL statement
        $stmt->execute([$user_name]); // Execute the statement with the username as a parameter to prevent SQL injection

        if ($stmt->rowCount() == 1) { // Check if a user with the provided username exists
            $user = $stmt->fetch(); // Fetch the user data from the database
            $usernameDB = $user['username']; // Get the username from the database
            $passwordDB = $user['password']; // Get the hashed password from the database
            $role = $user['role']; // Get the role of the user (admin or employee)
            $id = $user['id']; // Get the user ID from the database

            if ($user_name === $usernameDB) { // Check if the provided username matches the one in the database
                if (password_verify($password, $passwordDB)) {  // Verify the provided password against the hashed password in the database
                    if ($role === 'admin') {
                        $_SESSION['role'] = $role;
                        $_SESSION['id'] = $id;
                        $_SESSION['username'] = $usernameDB;
                        header('Location: ../index.php');

                    } else if ($role === 'employee') {
                        $_SESSION['role'] = $role;
                        $_SESSION['id'] = $id;
                        $_SESSION['username'] = $usernameDB;
                        header('Location: ../index.php');
                    }else if ($role === 'manager') {
                        $_SESSION['role'] = $role;
                        $_SESSION['id'] = $id;
                        $_SESSION['username'] = $usernameDB;
                        header('Location: ../index.php');
                    } else { // If the role is neither admin nor employee, redirect back to the login page with an error message
                        $error_message = "Unknown error occurred";
                        header('Location: ../login.php?error=' . $error_message);
                        exit();
                    }
                } else { // If the password does not match, redirect back to the login page with an error message
                    $error_message = "Incorrect username or password";
                    header('Location: ../login.php?error=' . $error_message);
                    exit();
                }
            } else { // If the provided username does not match the one in the database, redirect back to the login page with an error message
                $error_message = "Incorrect username or password"; 
                header('Location: ../login.php?error=' . $error_message);
                exit();
            }
        }
    }
} else { // If the username or password fields are not set, redirect back to the login page with an error message
    $error_message = "Unknown error occurred";
    header('Location: ../login.php?error=' . $error_message);
    exit();
}
