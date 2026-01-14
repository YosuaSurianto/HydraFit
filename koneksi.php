<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "db_hydrafit"; // Sesuai screenshot kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Gagal Total: " . mysqli_connect_error());
}
?>