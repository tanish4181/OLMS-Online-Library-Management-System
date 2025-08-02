<?php
// Database connection settings
// These are the basic settings to connect to MySQL database
$host = "localhost";       // The server where MySQL is running
$username = "root";        // The username for MySQL (default for XAMPP)
$password = "";            // The password (empty for default XAMPP setup)
$database = "olms";        // The name of our database

// Create connection to the database
$conn = mysqli_connect($host, $username, $password, $database);

// Check if connection worked
if (!$conn) {
    // If connection failed, show error and stop
    die("Connection failed: " . mysqli_connect_error());
}

// If we get here, connection is successful
?>
