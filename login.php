<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Login gagal. Cek kembali username/password.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-opacity-40"
    style="background-image: url(assets/img/wow.gif); background-size: cover; background-repeat: no-repeat;">
    <form method="POST" class="p-6 rounded shadow w-80 justify-center">
        <h2 class="text-xl font-bold mb-4 item-center ">Login</h2>
        <?php if (isset($error))
            echo "<p class='text-red-500'>$error</p>"; ?>
        <input name="username" placeholder="Username" required class="w-full mb-3 p-2 border rounded" />
        <input name="password" type="password" placeholder="Password" required class="w-full mb-3 p-2 border rounded" />
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
        <p class="text-sm mt-2 text-white">Belum punya akun? <a href="register.php" class="text-blue-500">Daftar</a></p>
    </form>
</body>

</html>