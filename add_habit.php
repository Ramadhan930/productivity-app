<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['habit_name'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO habits (user_id, name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $name);
    $stmt->execute();
}

header("Location: index.php");
exit();
