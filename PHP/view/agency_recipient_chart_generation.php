<?php
include('header.php');
require('../model/database.php');
require('../model/award_db.php');

$agency_charts = agency_charts($db);

function EscapeForCSV($value)
{
  return '"' . str_replace('"', '""', $value) . '"';
}

// Check if there is any data to export
if (count($agency_charts) > 0) {
    // File path to save the CSV
    $filePath = '../AGCharts/files/agency.csv';

    // Open the file in write mode
    $file = fopen($filePath, 'w');

    // Write column headers
    fputcsv($file, ['Agency_Name', 'Total_Obligation']);

    // Write rows to the CSV file
    foreach ($agency_charts as $row) {
        if (is_string($row['Agency_Name'])) {
            // Capitalize the first letter of the state and remove surrounding quotes if any
            $row['Agency_Name'] = ucwords(strtolower(trim($row['Agency_Name'], '\'"')));
        }
        fputcsv($file, $row);
    }

    // Close the file
    fclose($file);

    echo "Data has been exported to $filePath\n";
} else {
    echo "No results found.\n";
}

$recipient_charts = recipient_charts($db);

// Check if there is any data to export
if (count($recipient_charts) > 0) {
    // File path to save the CSV
    $filePath = '../AGCharts/files/receipient.csv';

    // Open the file in write mode
    $file = fopen($filePath, 'w');


    // Write column headers
    fputcsv($file, ['Recipient_Name', 'Total_Obligation']);

    // Write rows to the CSV file
    foreach ($recipient_charts as $row) {
        if (is_string($row['Recipient_Name'])) {
            // Capitalize the first letter of the state and remove surrounding quotes if any
            $row['Recipient_Name'] = ucwords(strtolower(trim($row['Recipient_Name'], '\'"')));
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
$htmlFilePath = '../AGCharts/agchart.php';
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