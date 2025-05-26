<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $habit_id = $_POST['habit_id'];
    $today = date('Y-m-d');

    $check_sql = "SELECT * FROM habit_logs WHERE habit_id = ? AND log_date = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("is", $habit_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $insert_sql = "INSERT INTO habit_logs (habit_id, log_date, status) VALUES (?, ?, TRUE)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("is", $habit_id, $today);
        $insert_stmt->execute();
    }
}

header("Location: index.php");
exit();
