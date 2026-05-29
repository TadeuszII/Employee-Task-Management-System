<?php

session_start();

if (isset($_SESSION['role']) && isset($_SESSION['id']) && in_array($_SESSION['role'], ['admin', 'employee', 'manager'])) {

    include "DB_connection.php";
    include "app/Model/user.php";

    $user = get_user_by_id($conn, $_SESSION['id']);

    $pic = (!empty($user['profile_picture']))
        ? 'images/profiles/' . $user['profile_picture']
        : 'images/User_PlaceHolder.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">

        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title">Edit Profile <a href="profile.php">Profile</a></h4>

            <form class="form-1" action="app/update-profile.php" method="POST" enctype="multipart/form-data">

                <!-- Error Message -->
                <?php if (isset($_GET['error'])) { ?>
                    <div class="warning" role="alert">
                        <?php echo stripcslashes($_GET['error']); ?>
                    </div>
                <?php } ?>

                <!-- Success Message -->
                <?php if (isset($_GET['success'])) { ?>
                    <div class="success" role="alert">
                        <?php echo stripcslashes($_GET['success']); ?>
                    </div>
                <?php } ?>

                <!-- Profile Picture Preview -->
                <div class="input-holder">
                    <img src="<?= htmlspecialchars($pic) ?>" alt="Profile Picture"
                        style="width:100px; height:100px; border-radius:50%; border:3px solid #cc0044; object-fit:cover; display:block; margin-bottom:10px;">
                    <label for="profile_picture">Profile Picture:</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg, image/png, image/webp" class="input-1">
                </div>

                <!-- Full Name -->
                <div class="input-holder">
                    <label for="full_name">Full Name:</label>
                    <input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" class="input-1" placeholder="Full Name">
                </div>

                <!-- Old Password -->
                <div class="input-holder">
                    <label for="password">Old Password:</label>
                    <input type="password" name="password" id="password" class="input-1" placeholder="Old Password">
                </div>

                <!-- New Password -->
                <div class="input-holder">
                    <label for="new_password">New Password:</label>
                    <input type="password" name="new_password" id="new_password" class="input-1" placeholder="New Password">
                </div>

                <!-- Confirm Password -->
                <div class="input-holder">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="input-1" placeholder="Confirm Password">
                </div>

                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                <br>
                <button class="edit-btn">Save Changes</button>
            </form>
        </section>

    </div>
</body>

</html>

<?php } else {
    $error_message = "Login at first";
    header('Location: login.php?error=' . $error_message);
    exit();
}
?>