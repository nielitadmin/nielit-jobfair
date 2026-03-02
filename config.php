<?php
session_start();

$host = "localhost";
$user = "u664913565_nielit_jobfair";
$pass = "Nielitbbsr@2026";
$db   = "u664913565_nielit_jobfair";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Security Function
function clean_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}
?>