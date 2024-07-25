<?php
// Database connection
$host = 'localhost';
$username = 'sujaya';
$password = 'Password0!';
$database = 'afinaltest2';
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>