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


# ---- To insert user to the database ----
function insert_user($conn, $data){
    $sql = "INSERT INTO user (full_name, username, password, role) VALUES (?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);

}

# ---- To update user data in the database ----
function update_user($conn, $data){
    $sql = "UPDATE user SET full_name = ?, username = ?, password = ?, role = ? WHERE id = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);

}


# ---- To delete user from the database ----

function delete_user($conn, $data){
    $sql = "DELETE FROM user WHERE id =? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
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

function update_profile($conn, $data){
    $sql = "UPDATE user SET full_name = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);

}