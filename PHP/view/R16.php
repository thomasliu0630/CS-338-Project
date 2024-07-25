<?php
session_start();
include('header.php');

require('../model/database.php');
require('../model/award_db.php');
require "common.php";

$connection = new PDO($dsn, $username, $password);
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch distinct values for the dropdown
$query = "SELECT DISTINCT Agency_Name, Agency_Identifier FROM Agency;";
$stmt = $connection->prepare($query);
$stmt->execute();
$agencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
  $agency_ID = $_POST['Agency'];
  $_SESSION["agency_ID"] = $agency_ID;

  $query = "SELECT DISTINCT pa.Program_Name, pa.Program_Reporting_Key
            FROM Program_Activity pa
            INNER JOIN Provides p
            ON pa.Program_Reporting_Key = p.Program_Reporting_Key
            INNER JOIN Award a
            ON p.Prime_Award_ID = a.Prime_Award_ID
            INNER JOIN Agency ag
            ON a.Agency_Identifier = ag.Agency_Identifier
            WHERE ag.Agency_Identifier = :agency_ID;";
  
  $stmt = $connection->prepare($query);
  $stmt->bindvalue(":agency_ID", $agency_ID);
  $stmt->execute();
  $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['submit2'])) {
  $program_ID = $_POST['Program'];
  $completion_rate = $_POST['Completion_Rate'];
  $agency_ID = $_SESSION["agency_ID"];
  
  $query = "SELECT 
                a.Prime_Award_ID, 
                a.Obligation_Amount, 
                a.Outlayed_Amount,
                ROUND((a.Outlayed_Amount / a.Obligation_Amount) * 100) as Completion_Rate,
                ag.Agency_Name,
                pa.Program_Name
            FROM 
                Award a
            INNER JOIN 
                Agency ag ON a.Agency_Identifier = ag.Agency_Identifier
            INNER JOIN 
                Provides p ON a.Prime_Award_ID = p.Prime_Award_ID
            INNER JOIN 
                Program_Activity pa ON p.Program_Reporting_Key = pa.Program_Reporting_Key
            WHERE 
                ROUND((a.Outlayed_Amount / a.Obligation_Amount) * 100) <= :completion_rate
                AND ag.Agency_Identifier = :agency_ID
                AND pa.Program_Reporting_Key = :program_ID
            ORDER BY 
                Completion_Rate DESC;";
  
  $stmt = $connection->prepare($query);
  $stmt->bindvalue(":completion_rate", $completion_rate);
  $stmt->bindvalue(":agency_ID", $agency_ID);
  $stmt->bindvalue(":program_ID", $program_ID);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
      max-width: 600px;
      text-align: center;
    }
    form {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 15px;
      width: 100%;
    }
    .form-group label {
      margin-bottom: 5px;
      text-align: left;
      display: block;
    }
    select, input[type="text"], input[type="submit"] {
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
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ddd;
    }
    th {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Browse Awards based on Completion Rate</h2>

    <form method="post">
      <div class="form-group">
        <label for="Agency">Agency Name:</label>
        <select id="Agency" name="Agency" required>
          <option value="">Select an Agency</option>
          <?php foreach ($agencies as $agency) : ?>
            <option value="<?php echo htmlspecialchars($agency['Agency_Identifier']); ?>">
              <?php echo htmlspecialchars($agency['Agency_Name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <input type="submit" name="submit" value="Submit">
    </form>

    <?php if (isset($programs)) { ?>
    <form method="post" action="">
      <input type="hidden" name="Agency" value="<?php echo htmlspecialchars($agency_ID); ?>">
      <div class="form-group">
        <label for="Program">Program Name:</label>
        <select id="Program" name="Program" required>
          <option value="">Select a Program</option>
          <?php foreach ($programs as $program) : ?>
            <option value="<?php echo htmlspecialchars($program['Program_Reporting_Key']); ?>">
              <?php echo htmlspecialchars($program['Program_Name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="Completion_Rate">Completion Rate (%)</label>
        <input type="text" id="Completion_Rate" name="Completion_Rate" required>
      </div>
      <input type="submit" name="submit2" value="Submit">
    </form>
    <?php } ?>

    <?php
    if (isset($_POST['submit2'])) {
      if ($result) { ?>
        <h2>Results</h2>
        <table>
          <thead>
            <tr> 
              <th>Prime Award ID</th> 
              <th>Obligation Amount</th>
              <th>Outlayed Amount</th>
              <th>Completion Rate</th>
              <th>Agency Name</th>
              <th>Program Name</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($result as $row) { ?>
              <tr>
                <td><?php echo htmlspecialchars($row["Prime_Award_ID"]); ?></td>
                <td><?php echo htmlspecialchars($row["Obligation_Amount"]); ?></td>
                <td><?php echo htmlspecialchars($row["Outlayed_Amount"]); ?></td>
                <td><?php echo htmlspecialchars($row["Completion_Rate"]); ?></td>
                <td><?php echo htmlspecialchars($row["Agency_Name"]); ?></td>
                <td><?php echo htmlspecialchars($row["Program_Name"]); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } else { ?>
        <p>No results found for selection.</p>
      <?php }
    }
    ?>
    <ul>
      <a href="../index.php"><strong>Main Menu</strong></a> - Return to main menu
    </ul>
  </div>
</body>
</html>

<?php include('footer.php'); ?>
