<?php
header('Content-Type: application/json');

require('../model/database.php');

// Database configuration
$username = 'sujaya';
$password = 'Password0!';
$host = 'localhost';
$database = 'afinlatest2';

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Define the SQL query
    $query = "
        SELECT * FROM Award_Statistics WHERE (Agency_Name = 'CIA' OR Agency_Name = 'FBI') AND Award_Type = 'non-covid';
    ";

    // Execute the query
    $stmt = $pdo->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>