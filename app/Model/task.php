<?php

// Wstawienia zadania w baza danych
function insert_task($conn, $data){
    $sql = "INSERT INTO tasks (title, description, assigned_to, due_date, created_by) VALUES (?,?,?,?,?)";
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

// Pobranie wszystkich zadań z bazy danych stworzonych przez konkretnego użytkownika
function get_all_tasks_by_creator($conn, $creator_id){
    $sql = "SELECT * FROM tasks WHERE created_by = ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$creator_id]);

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

// aktualizacja statusu zadania w bazie danych
function update_task_status($conn, $data) {
    $sql = "UPDATE tasks SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

// pobranie wszystkich zadań z bazy danych, które są przypisane do użytkownika i mają termin dzisiaj
function get_all_tasks_due_today($conn){
    $sql = "SELECT * FROM tasks WHERE due_date = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->execute([]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;
}

// pobranie wszystkich zadań z bazy danych, które są przypisane do użytkownika i nie mają terminu
function get_all_tasks_overdue($conn){
    $sql = "SELECT * FROM tasks WHERE due_date < CURDATE() AND NOT (due_date IS NULL OR due_date = '0000-00-00') AND STATUS != 'completed'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;
}

// pobranie zadan bez dealinu
function get_all_tasks_no_deadline($conn){
    $sql = "SELECT * FROM tasks WHERE due_date IS NULL OR due_date = '0000-00-00'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;
}

// pobranie zadan bez dealinu
function get_tasks_by_status($conn, $status){
    $sql = "SELECT * FROM tasks WHERE status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$status]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;
}

// Personal dashboard - pobranie zadan przypisanych do uzytkownika

// pobranie dziseijszych zadan przypisanych do uzytkownika
function get_all_tasks_by_id_due_today( $conn, $id ){
    $sql = "SELECT * FROM tasks WHERE assigned_to = ? AND due_date = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;
}

// pobranie zadan przypisanych do uzytkownika, ktore są przeterminowane
function get_all_tasks_by_id_overdue( $conn, $id ){
    $sql = "SELECT * FROM tasks WHERE assigned_to = ? AND due_date < CURDATE() AND NOT (due_date IS NULL OR due_date = '0000-00-00') AND STATUS != 'completed'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;
}

// pobranie zadan przypisanych do uzytkownika, ktore nie mają terminu
function get_all_tasks_by_id_no_deadline( $conn, $id ){
    $sql = "SELECT * FROM tasks WHERE assigned_to = ? AND (due_date IS NULL OR due_date = '0000-00-00')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;
}

// pobranie zadan przypisanych do uzytkownika, ktore mają określony status
function get_tasks_by_id_status($conn, $id, $status){
    $sql = "SELECT * FROM tasks WHERE assigned_to = ? AND status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id, $status]);

    if ($stmt->rowCount() > 0){
        $tasks = $stmt->fetchAll();

    }else{
        $tasks = 0;
    }
    return $tasks;    

}

// Pobranie zadań z bazy danych z zakresem widoczności w zależności od roli użytkownika i dodatkowym filtrem
function get_tasks_scoped($conn, $role, $id, $filter = null, $extra = null){

    // Build the WHERE scope based on role
    if ($role === 'admin') {
        $scope = "1=1";
        $params = [];
    } elseif ($role === 'manager') {
        $scope = "(assigned_to = ? OR assigned_to IN (SELECT id FROM user WHERE manager_id = ?))";
        $params = [$id, $id];
    } else {
        $scope = "assigned_to = ?";
        $params = [$id];
    }

    // Build the WHERE filter
    switch ($filter) {
        case 'due_today':
            $condition = "due_date = CURDATE()";
            break;
        case 'overdue':
            $condition = "due_date < CURDATE() AND NOT (due_date IS NULL OR due_date = '0000-00-00') AND STATUS != 'completed'";
            break;
        case 'no_deadline':
            $condition = "(due_date IS NULL OR due_date = '0000-00-00')";
            break;
        case 'status':
            $condition = "status = ?";
            $params[] = $extra;
            break;
        default:
            $condition = "1=1";
    }

    $sql = "SELECT * FROM tasks WHERE $scope AND $condition ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount() > 0 ? $stmt->fetchAll() : 0;
}



