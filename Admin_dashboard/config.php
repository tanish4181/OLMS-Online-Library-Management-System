<?php
$host = "localhost";       // usually localhost
$username = "root";        // default XAMPP/WAMP username
$password = "";            // default XAMPP/WAMP password is empty
$database = "olms";        // your database name

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
