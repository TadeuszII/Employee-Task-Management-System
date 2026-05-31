<?php


session_start(); // Start the session to access session variables


if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') { // Check if the user is logged in by verifying session variables


    include "DB_connection.php";
    include "app/Model/user.php";


    // load managers and admins for dropdown 
    $supervisors = get_all_managers_and_admins($conn);
    


?>
    <!DOCTYPE html>
    <html lang="en">


    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add User</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">



    </head>


    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">


            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title">Add User <a href="manage_users.php">Users</a></h4>


                <form class="form-1"
                    action="app/add-user.php"
                    method="POST">


                    <!-- Error Message -->
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="warning" role="alert">
                            <?php echo stripcslashes($_GET['error']); ?>
                        </div>
                    <?php } ?>
                    <!-- Success Message -->
                    <?php
                    if (isset($_GET['success'])) { ?>
                        <div class="success" role="alert">
                            <?php echo stripcslashes($_GET['success']); ?>
                        </div>
                    <?php }?>


                    <div class="input-holder">
                        <input type="text" name="full_name" class="input-1" placeholder="Full Name"><br><br>
                    </div>


                    <div class="form-1">
                        <input type="text" name="user_name" class="input-1" placeholder="Username"><br><br>
                    </div>
                    <div class="form-1">
                        <input type="text" name="password" class="input-1" placeholder="Password"><br><br>
                    </div>
                    <div class="input-holder">
                        <select name="role" id="role-select" class="input-1"> <!-- Added id for JS -->
                            <option value="employee" selected>Employee</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select><br><br>
                    </div>

                    <!-- Manager assignment dropdown - only shown for employee role -->
                    <div class="input-holder" id="manager-field">
                        <select name="manager_id" class="input-1">
                            <option value="">-- No Manager Assigned --</option>
                            <?php if ($supervisors != 0) foreach($supervisors as $s) { ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= htmlspecialchars($s['username'], ENT_QUOTES, 'UTF-8') ?> (<?= $s['role'] ?>)
                                </option>
                            <?php } ?>
                        </select><br><br>
                    </div>

                    <button class="edit-btn">Add User</button>
                </form>
            </section>


        </div>



        <script type="text/javascript">
            var active = document.querySelector("#navList li:nth-child(2)"); // Select the second list item in the navigation list
            active.classList.add("active"); // Add the "active" class to the selected list item

            // Show manager field only when role is employee
            const roleSelect = document.getElementById('role-select');
            const managerField = document.getElementById('manager-field');

        function toggleManagerField() {
            const showFor = ['employee', 'manager'];
            managerField.style.display = showFor.includes(roleSelect.value) ? 'block' : 'none';
        }

            roleSelect.addEventListener('change', toggleManagerField);
            toggleManagerField(); // run on page load to set correct initial state
        </script>
    </body>


    </html>


<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>