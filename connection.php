<?php
// Change these values when uploading to your web host.
$host = "localhost";
$user = "root";
$password = "";
$database = "inventory_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed. Please check connection.php.");
}
$conn->set_charset("utf8mb4");
?>