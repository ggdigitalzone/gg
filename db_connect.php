<?php
// Database configuration
$servername = "localhost"; // Database server
$username = "root"; // Database username (change if needed)
$password = ""; // Database password (change if needed)
$dbname = "gaming_store"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set the character set to UTF-8 for better handling of special characters
$conn->set_charset("utf8");

// Use this connection in your scripts
?>
