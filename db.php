<?php

$host = 'localhost';
$user = 'root';
$pass = ''; // Ganti jika ada password
$dbname = 'productivity_app';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>