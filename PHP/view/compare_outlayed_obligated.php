<?php
include('header.php');
require('../model/database.php');
require('../model/award_db.php');

$award_data = compareoo($db);

// Check if there is any data to export
if (count($award_data) > 0) {
    // File path to save the CSV
    $filePath = '..\OOGraphs\files\138ff3103e06f8b7001560842903392710c7950a7a02ecee26ba056d256d9f127a8bd80abd83e897c9f68ff625f134501424fff4d8ef8c235638d0f0d287766b.csv';

    // Open the file in write mode
    $file = fopen($filePath, 'w');

    // Write column headers
    fputcsv($file, ['State', 'Total_Obligation', 'Total_Outlayed']);

    // Write rows to the CSV file
    foreach ($award_data as $row) {
        // Check if the first element exists
        if (is_string($row['primary_place'])) {
            // Capitalize the first letter of the state and remove surrounding quotes if any
            $row['primary_place'] = ucwords(strtolower(trim($row['primary_place'], "\"'")));
        }
        fputcsv($file, $row);
    }

    // Close the file
    fclose($file);

    echo "Data has been exported to $filePath\n";
} else {
    echo "No results found.\n";
}

// Open the HTML file
$htmlFilePath = '../OOGraphs/oograph.php';
if (file_exists($htmlFilePath)) {
    header("Location: $htmlFilePath");
    exit;
} else {
    echo "HTML file not found: $htmlFilePath\n";
}

?>

<?php include('footer.php') ?>

<ul>
    <a href="./index.php"><strong>Main Menu</strong></a> - Return to main menu
</ul>