<?php



// ---- Sprawdź czy username już istnieje w bazie ----
function username_exists($conn, $username, $exclude_id = null)
{
    if ($exclude_id) {
        // Używane przy edycji usera - ignoruje jego własne ID
        $sql = "SELECT id FROM user WHERE username = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $exclude_id]);
    } else {
        // Używane przy dodawaniu nowego usera
        $sql = "SELECT id FROM user WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);
    }
    return $stmt->rowCount() > 0;
}



# ---- To show all users employee in the table ----
function get_all_users($conn){ 
    $sql = "SELECT * FROM user WHERE role =? ";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["employee"]);


    if ($stmt->rowCount() > 0){
        $users = $stmt->fetchAll();


    }else{
        $users = 0;
    }
    return $users;
}


// ---- get ALL users for manage_users.php ----
function get_all_users_admin($conn){
    // Changed: alias manager_name → manager_username, use u.username for ordering
    $sql = "SELECT u.*, m.username AS manager_username, m.role AS manager_role 
            FROM user u
            LEFT JOIN user m ON u.manager_id = m.id
            ORDER BY u.role, u.username";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0){
        $users = $stmt->fetchAll();
    }else{
        $users = 0;
    }
    return $users;
}

// ---- To show all users employee and manager in the table for assign task ----
function get_all_assignable_users($conn){
    $sql = "SELECT * FROM user WHERE role IN ('employee', 'manager') ORDER BY role, username";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0){
        $users = $stmt->fetchAll();
    }else{
        $users = 0;
    }
    return $users;
}

// ---- Admin task assignment: all employees + all managers (NO admins) ----
function get_all_assignable_users_admin($conn){
    $sql = "SELECT * FROM user WHERE role IN ('employee', 'manager') ORDER BY role, username";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0){
        $users = $stmt->fetchAll();
    }else{
        $users = 0;
    }
    return $users;
}

// ---- Manager task assignment: only their own employees + all other managers (not self) ----
function get_assignable_users_for_manager($conn, $manager_id){
    $sql = "SELECT * FROM user 
            WHERE (role = 'employee' AND manager_id = ?)
               OR (role = 'manager' AND id != ?)
            ORDER BY role, username";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$manager_id, $manager_id]);

    if ($stmt->rowCount() > 0){
        $users = $stmt->fetchAll();
    }else{
        $users = 0;
    }
    return $users;
}

// ---- For add-user/edit-user: populate the manager_id dropdown (managers + admins) ----
function get_all_managers_and_admins($conn, $exclude_id = null){
    if ($exclude_id) {
        $sql = "SELECT * FROM user WHERE role IN ('manager', 'admin') AND id != ? ORDER BY role, username";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$exclude_id]);
    } else {
        $sql = "SELECT * FROM user WHERE role IN ('manager', 'admin') ORDER BY role, username";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    if ($stmt->rowCount() > 0){
        $users = $stmt->fetchAll();
    }else{
        $users = 0;
    }
    return $users;
}

# ---- To insert user to the database ----
function insert_user($conn, $data){
    $sql = "INSERT INTO user (full_name, username, password, role, manager_id) VALUES (?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}


# ---- To update user data in the database ----
function update_user($conn, $data){
    $sql = "UPDATE user SET full_name = ?, username = ?, password = ?, role = ?, manager_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data); // data = [full_name, username, password, role, manager_id, id]
    
}



# ---- To delete user from the database ----

function delete_user($conn, $data){
    # ============================================ CHANGED: removed AND role = ? so any role can be deleted ============================================
    $sql = "DELETE FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
    # ============================================ END CHANGE ============================================
}



# ---- To get user data by id from database in edit ----

function get_user_by_id($conn, $id){
    $sql = "SELECT * FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);


    if ($stmt->rowCount() > 0){
        $user = $stmt->fetch();


    }else{
        $user = 0;
    }
    return $user;
}

// ---- To update user profile in the database ----
function update_profile($conn, $data){
    $sql = "UPDATE user SET full_name = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);


}

// ---- To get direct reports of a manager (employees + managers with this manager as their manager_id) ----
function get_direct_reports($conn, $admin_id) {
    $sql = "SELECT * FROM user WHERE manager_id = ? AND role IN ('employee', 'manager') ORDER BY role, full_name";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$admin_id]);

    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll();
    }
    return [];
}
