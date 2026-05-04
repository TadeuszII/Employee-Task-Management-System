<nav class="side-bar">
                <div class="user-p">
                    <img src="images/User_PlaceHolder.png" alt="User Place holder">
                    <h4><?php echo $_SESSION['username']; ?></h4>
                </div>

                <?php
                //$user = 'admin';
                if ($_SESSION['role'] == 'employee') { // Check if the user is an employee
                ?>

                    <!--- Employe Navigation Bar-->
                    <ul>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-computer" aria-hidden="true"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-tasks"></i>
                                <span>My Task</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-bell"></i>
                                <span>Notification</span>
                            </a>
                        </li>
                        <li>
                            <a href="logout.php">
                                <i class="fa-solid fa-sign-out" aria-hidden="true"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
           

        <?php } else { // If the user is not an employee, assume they are an admin
        ?>
            <!--- Admin Navigation Bar-->
            <ul>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-computer" aria-hidden="true"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="active">
                    <a href="manage_users.php">
                        <i class="fa-solid fa-users"></i>
                        <span>Manage Users</span>
                    </a>
                </li>
                <li>
                    <a href="create_task.php">
                        <i class="fa-solid fa-plus"></i>
                        <span>Create Task</span>
                    </a>
                </li>
                <li>
                    <a href="all_tasks.php">
                        <i class="fa-solid fa-bell"></i>
                        <span>All Tasks</span>
                    </a>
                </li>
                <li>
                    <a href="notifications.php">
                        <i class="fa-solid fa-bell"></i>
                        <span>Notification</span>
                    </a>
                </li>

                <li>
                    <a href="logout.php">
                        <i class="fa-solid fa-sign-out" aria-hidden="true"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
            <?php } ?>
            </nav>