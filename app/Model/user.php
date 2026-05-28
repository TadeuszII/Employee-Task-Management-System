<?php


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
    $sql = "SELECT * FROM user ORDER BY role, full_name";
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
    $sql = "SELECT * FROM user WHERE role IN ('employee', 'manager') ORDER BY role, full_name";
    $stmt = $conn->prepare($sql);
    $stmt->execute();


    if ($stmt->rowCount() > 0){
        $users = $stmt->fetchAll();
    }else{
        $users = 0;
    }
    return $users;
}


# ---- To insert user to the database ----
function insert_user($conn, $data){
    $sql = "INSERT INTO user (full_name, username, password, role) VALUES (?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);


}


# ---- To update user data in the database ----
function update_user($conn, $data){
    $sql = "UPDATE user SET full_name = ?, username = ?, password = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);


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