<?php

// Wstawienia zadania w baza danych
function insert_task($conn, $data){
    $sql = "INSERT INTO tasks (title, description, assigned_to, due_date) VALUES (?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);

}

// Pobranie wszystkich zadań z bazy danych
function get_all_tasks($conn){
    $sql = "SELECT * FROM tasks ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;
}

// pobranie zadania z bazy danych po id
function get_task_by_id($conn, $id) {
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0){
        $task = $stmt->fetch();

    }else{
        $task = 0;
    }
    return $task;

}

// usuniece zadania z bazy dannych
function delete_task($conn, $data) {
    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

// aktualizacja zadania w bazie danych

function update_task($conn, $data) {
    $sql = "UPDATE tasks SET title = ?, description = ?, assigned_to = ?, due_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

// aktualizacja statusu zadania w bazie danych
function get_all_tasks_by_id($conn, $id){

    $sql = "SELECT * FROM tasks WHERE assigned_to = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;

}


function update_task_status($conn, $data) {
    $sql = "UPDATE tasks SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

