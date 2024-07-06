<!DOCTYPE html>
<html>
<body>
<?php
$dsn = 'mysql:host=localhost;dbname=test';
$username = 'sujaya';
$password = 'Password0!';

try {
    $db = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    $error = "Database Error: ";
    $error .= $e->getMessage();
    include('view/error.php');
    exit();
}
