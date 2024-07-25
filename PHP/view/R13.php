<?php
include('header.php');
require('../model/award_db.php');
require('../model/database2.php');
?>

<?php
try {
    $connection = new PDO($dsn, $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch distinct values for the dropdown
    $query = "SELECT DISTINCT a.Agency_Identifier, a.Agency_Name
FROM Agency a
WHERE a.Agency_Identifier IN (
    SELECT aw.Agency_Identifier
    FROM Award aw
    JOIN Covid_Related cr ON aw.Prime_Award_ID = cr.Prime_Award_ID
) AND a.Agency_Identifier IN (
    SELECT aw.Agency_Identifier
    FROM Award aw
    JOIN Non_Covid_Related ncr ON aw.Prime_Award_ID = ncr.Prime_Award_ID
);";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $agencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $error) {
    echo "Error fetching agencies: " . $error->getMessage();
}

if (isset($_POST['submit'])) {
    $agency1_ID = $_POST['Agency1'];
    $agency2_ID = $_POST['Agency2'];

    // Fetch Covid-related awards
    $queryCovid = "SELECT Agency.Agency_Name, Award.Outlayed_Amount 
                   FROM Award 
                   INNER JOIN Covid_Related ON Award.Prime_Award_ID = Covid_Related.Prime_Award_ID 
                   INNER JOIN Agency ON Award.Agency_Identifier = Agency.Agency_Identifier
                   WHERE Award.Agency_Identifier = :agency1_ID OR Award.Agency_Identifier = :agency2_ID";
    $stmtCovid = $connection->prepare($queryCovid);
    $stmtCovid->bindParam(':agency1_ID', $agency1_ID);
    $stmtCovid->bindParam(':agency2_ID', $agency2_ID);
    $stmtCovid->execute();
    $covidAwards = $stmtCovid->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Non-Covid-related awards
    $queryNonCovid = "SELECT Agency.Agency_Name, Award.Outlayed_Amount 
                      FROM Award 
                      INNER JOIN Non_Covid_Related ON Award.Prime_Award_ID = Non_Covid_Related.Prime_Award_ID 
                      INNER JOIN Agency ON Award.Agency_Identifier = Agency.Agency_Identifier
                      WHERE Award.Agency_Identifier = :agency1_ID OR Award.Agency_Identifier = :agency2_ID";
    $stmtNonCovid = $connection->prepare($queryNonCovid);
    $stmtNonCovid->bindParam(':agency1_ID', $agency1_ID);
    $stmtNonCovid->bindParam(':agency2_ID', $agency2_ID);
    $stmtNonCovid->execute();
    $nonCovidAwards = $stmtNonCovid->fetchAll(PDO::FETCH_ASSOC);

    // Define the directory for CSV files
    $csvDirectory = '../boxplot/csv/';
    if (!is_dir($csvDirectory)) {
        mkdir($csvDirectory, 0777, true);
    }

    // Export Covid-related awards to CSV
    $covidFileName = $csvDirectory . 'covid_awards.csv';
    $covidFile = fopen($covidFileName, 'w');
    fputcsv($covidFile, array('Agency_Name', 'Outlayed_Amount'));
    foreach ($covidAwards as $row) {
        fputcsv($covidFile, $row);
    }
    fclose($covidFile);

    // Export Non-Covid-related awards to CSV
    $nonCovidFileName = $csvDirectory . 'non_covid_awards.csv';
    $nonCovidFile = fopen($nonCovidFileName, 'w');
    fputcsv($nonCovidFile, array('Agency_Name', 'Outlayed_Amount'));
    foreach ($nonCovidAwards as $row) {
        fputcsv($nonCovidFile, $row);
    }
    fclose($nonCovidFile);

    // Open the HTML file
    $htmlFilePath = '../boxplot/boxplot.php';
    if (file_exists($htmlFilePath)) {
        header("Location: $htmlFilePath");
        exit;
    } else {
        echo "HTML file not found: $htmlFilePath\n";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            text-align: left;
        }
        select, input[type="submit"] {
            margin-top: 5px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Agencies to Compare</h2>
        <form method="post" action="">
            <label for="Agency1">First Agency Name:</label>
            <select id="Agency1" name="Agency1">
                <option value="">Select an agency</option>
                <?php foreach ($agencies as $agency) : ?>
                    <option value="<?php echo htmlspecialchars($agency['Agency_Identifier']); ?>">
                        <?php echo htmlspecialchars($agency['Agency_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="Agency2">Second Agency Name:</label>
            <select id="Agency2" name="Agency2">
                <option value="">Select a second agency</option>
                <?php foreach ($agencies as $agency) : ?>
                    <option value="<?php echo htmlspecialchars($agency['Agency_Identifier']); ?>">
                        <?php echo htmlspecialchars($agency['Agency_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="submit" name="submit" value="Submit">
        </form>
    </div>
</body>
</html>

<?php include('footer.php') ?>
