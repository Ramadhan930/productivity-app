<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $task_id = $_POST['task_id'];
    $new_status = $_POST['new_status'] ?? null;

    if ($new_status && in_array($new_status, ['pending', 'in-progress', 'done'])) {
        $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_status, $task_id, $user_id);
        $stmt->execute();
    }
}

header("Location: index.php");
exit();
