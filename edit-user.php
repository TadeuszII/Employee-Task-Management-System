<?php



session_start(); // Start the session to access session variables



if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') { // Check if the user is logged in by verifying session variables



    include "DB_connection.php"; // Include the database connection file
    include "app/Model/user.php"; // Include the user model file to access user-related functions



    if (!isset($_GET['id'])) {
        $error_message = "User id is required";
        header('Location: manage_users.php?error=' . $error_message);
        exit();
    }
    $id = $_GET['id'];
    
    $user = get_user_by_id($conn, $id); // Retrieve all users from the database "employee"
    
    if ($user == 0) {
         $error_message = "User not found";
        header('Location: manage_users.php?error=' . $error_message);
        exit();
    }


    // Load managers and admins for the manager assignment dropdown — exclude the user being edited so they cannot be assigned to themselves
    $supervisors = get_all_managers_and_admins($conn, $user['id']);



?>  
    <!DOCTYPE html>
    <html lang="en">



    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit User</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">




    </head>



    <body>
        <input type="checkbox" id="checkbox">
        <?php include "inc/header.php"; ?> <!-- Include the header -->
        <div class="body">



            <?php include "inc/nav.php"; ?> <!-- Include the navigation -->
            <section class="section-1">
                <h4 class="title">Edit User <a href="manage_users.php">Users</a></h4>



                <form class="form-1"
                    action="app/update-user.php"
                    method="POST">



                    <!-- Error Message -->
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="warning" role="alert">
                            <?php echo htmlspecialchars(stripcslashes($_GET['error']), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php } ?>
                    <!-- Success Message -->
                    <?php
                    if (isset($_GET['success'])) { ?>
                        <div class="success" role="alert">
                            <?php echo htmlspecialchars(stripcslashes($_GET['success']), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php }?>



                    <div class="input-holder">
                        <input type="text" name="full_name" value="<?=$user["full_name"] ?>" class="input-1" placeholder="Full Name"><br><br>
                    </div>



                    <div class="form-1">
                        <input type="text" name="user_name" value="<?=$user["username"] ?>" class="input-1" placeholder="Username"><br><br>
                    </div>
                    <div class="form-1">
                        <input type="text" name="password"  class="input-1" placeholder="Password"><br><br>
                    </div>
                     <div class="input-holder">
                        <select name="role" id="role-select" class="input-1"> <!-- Added id for JS -->
                            <option value="employee" <?= $user['role'] == 'employee' ? 'selected' : '' ?>>Employee</option>
                            <option value="manager"  <?= $user['role'] == 'manager'  ? 'selected' : '' ?>>Manager</option>
                            <option value="admin"    <?= $user['role'] == 'admin'    ? 'selected' : '' ?>>Admin</option>
                        </select><br><br>
                    </div>


                    <!-- Manager assignment dropdown - shown for employee and manager roles, pre-selects current manager, excludes self -->
                    <div class="input-holder" id="manager-field">
                        <select name="manager_id" class="input-1">
                        <option value="">-- No Manager Assigned --</option>
                        <?php if ($supervisors != 0) foreach($supervisors as $s) { ?>
                            <option value="<?= $s['id'] ?>" <?= $user['manager_id'] == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['username'], ENT_QUOTES, 'UTF-8') ?> (<?= $s['role'] ?>)
                            </option>
                        <?php } ?>
                    </select><br><br>
                    </div>


                    <input type="text" name="id" value="<?=$user["id"]?>" hidden> 
                    <button class="edit-btn">Update</button>
                </form>
            </section>



        </div>




        <script type="text/javascript">
            var active = document.querySelector("#navList li:nth-child(2)"); // Select the second list item in the navigation list
            active.classList.add("active"); // Add the "active" class to the selected list item


            // Show manager field only when role is employee or manager
            const roleSelect = document.getElementById('role-select');
            const managerField = document.getElementById('manager-field');


            function toggleManagerField() {
                const showFor = ['employee', 'manager'];
                managerField.style.display = showFor.includes(roleSelect.value) ? 'block' : 'none';
            }


            roleSelect.addEventListener('change', toggleManagerField);
            toggleManagerField(); // run on page load to reflect current role
        </script>
    </body>



    </html>



<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>