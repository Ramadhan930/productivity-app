<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        header("Location: login.php?success=1");
        exit();
    } else {
        $error = "Username sudah digunakan.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen "
    style="background-image: url(assets/img/wow.gif); background-size: cover; background-repeat: no-repeat;">
    <form method="POST" class="p-6 rounded shadow w-80">
        <h2 class="text-xl font-bold mb-4">Daftar Akun</h2>
        <?php if (isset($error))
            echo "<p class='text-red-500'>$error</p>"; ?>
        <input name="username" placeholder="Username" required class="w-full mb-3 p-2 border rounded" />
        <input name="password" type="password" placeholder="Password" required class="w-full mb-3 p-2 border rounded" />
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Register</button>
        <p class="text-sm mt-2 text-white">Sudah punya akun? <a href="login.php" class="text-blue-500">Login</a></p>
    </form>
</body>

</html>