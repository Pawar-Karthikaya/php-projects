<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "ci4tutorial";

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
