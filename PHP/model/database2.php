<!DOCTYPE html>
<html>
<body>
<?php
$dsn = 'mysql:host=localhost;dbname=finaldropna2';
$username = 'sujaya';
$password = 'Password0!';

try {
    $db = new PDO($dsn, $username, $password);
    ini_set('memory_limit', '256M');
    ini_set('max_execution_time', '180');
} catch (PDOException $e) {
    $error = "Database Error: ";
    $error .= $e->getMessage();
    include('../view/error.php');
    exit();
}
