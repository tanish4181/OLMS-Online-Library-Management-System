<?php
// Database connection settings
$servername = "sql100.infinityfree.com";
$username = "if0_39751217";
$password = "tanish147852";
$dbname = "if0_39751217_olms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>