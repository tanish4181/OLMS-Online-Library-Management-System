<?php
// Hosted config
/*
$servername = "sql100.infinityfree.com";
$username = "if0_39751217";
$password = "tanish147852";
$dbname = "if0_39751217_olms";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
*/

// Localhost config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "olms";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>