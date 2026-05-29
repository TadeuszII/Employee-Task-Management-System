<?php
$nav_user = get_user_by_id($conn, $_SESSION['id']);
$pic = (!empty($nav_user['profile_picture']))
    ? 'images/profiles/' . $nav_user['profile_picture']
    : 'images/User_PlaceHolder.png';
?>

<nav class="side-bar">
    <div class="user-p">
        <img src="<?= htmlspecialchars($pic) ?>" alt="Profile Picture">
        <h4><?php echo $_SESSION['username']; ?></h4>
    </div>

    <?php if ($_SESSION['role'] == 'employee') { ?>
    <ul id="navList">
        <li data-page="index.php"><a href="index.php"><i class="fa-solid fa-computer"></i><span>Dashboard</span></a></li>
        <li data-page="my_tasks.php"><a href="my_tasks.php"><i class="fa-solid fa-tasks"></i><span>My Task</span></a></li>
        <li data-page="notifications.php"><a href="notifications.php"><i class="fa-solid fa-bell"></i><span>Notification</span></a></li>
        <li data-page="profile.php"><a href="profile.php"><i class="fa-solid fa-user"></i><span>Profile</span></a></li>
        <li data-page="logout.php"><a href="logout.php"><i class="fa-solid fa-sign-out"></i><span>Logout</span></a></li>
    </ul>

    <?php } elseif ($_SESSION['role'] == 'manager') { ?>
    <ul id="navList">
        <li data-page="index.php"><a href="index.php"><i class="fa-solid fa-computer"></i><span>Dashboard</span></a></li>
        <li data-page="my_tasks.php"><a href="my_tasks.php"><i class="fa-solid fa-tasks"></i><span>My Tasks</span></a></li>
        <li data-page="create_task.php"><a href="create_task.php"><i class="fa-solid fa-plus"></i><span>Create Task</span></a></li>
        <li data-page="all_tasks.php"><a href="all_tasks.php"><i class="fa-solid fa-list"></i><span>All Tasks</span></a></li>
        <li data-page="notifications.php"><a href="notifications.php"><i class="fa-solid fa-bell"></i><span>Notification</span></a></li>
        <li data-page="profile.php"><a href="profile.php"><i class="fa-solid fa-user"></i><span>Profile</span></a></li>
        <li data-page="logout.php"><a href="logout.php"><i class="fa-solid fa-sign-out"></i><span>Logout</span></a></li>
    </ul>

    <?php } else { ?>
    <ul id="navList">
        <li data-page="index.php"><a href="index.php"><i class="fa-solid fa-computer"></i><span>Dashboard</span></a></li>
        <li data-page="manage_users.php"><a href="manage_users.php"><i class="fa-solid fa-users"></i><span>Manage Users</span></a></li>
        <li data-page="create_task.php"><a href="create_task.php"><i class="fa-solid fa-plus"></i><span>Create Task</span></a></li>
        <li data-page="all_tasks.php"><a href="all_tasks.php"><i class="fa-solid fa-tasks"></i><span>All Tasks</span></a></li>
        <li data-page="notifications.php"><a href="notifications.php"><i class="fa-solid fa-bell"></i><span>Notification</span></a></li>
        <li data-page="profile.php"><a href="profile.php"><i class="fa-solid fa-user"></i><span>Profile</span></a></li>
        <li data-page="logout.php"><a href="logout.php"><i class="fa-solid fa-sign-out"></i><span>Logout</span></a></li>
    </ul>
    <?php } ?>
</nav>

<script>
    var currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('#navList li').forEach(function(li) {
        if (li.getAttribute('data-page') === currentPage) {
            li.classList.add('active');
        }
    });
</script>