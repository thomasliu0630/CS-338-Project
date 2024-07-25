<?php include('header.php'); ?>

<?php
require('../model/database.php');
require('../model/award_db.php');

$connection = new PDO($dsn, $username, $password);
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
  // Drop and create the view
  $dropViewSql = "DROP VIEW IF EXISTS Average_Funding;";
  $createViewSql = "
    CREATE VIEW Average_Funding AS
    SELECT 
        Federal_Account.Main_Account_Code,
        AVG(Award.Obligation_Amount) AS Average_Obligation_Amount,
        AVG(Award.Outlayed_Amount) AS Average_Outlayed_Amount
    FROM 
        Federal_Account
    JOIN 
        Award_Uses ON Federal_Account.Main_Account_Code = Award_Uses.Main_Account_Code
    JOIN 
        Award ON Award_Uses.Prime_Award_ID = Award.Prime_Award_ID
    GROUP BY 
        Federal_Account.Main_Account_Code;
  ";
  
  // Execute the SQL statements
  $connection->exec($dropViewSql);
  $connection->exec($createViewSql);

  // Fetch distinct Federal Account Codes for the dropdown
  $query = "SELECT DISTINCT Main_Account_Code FROM Average_Funding";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $account_codes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $error) {
  echo "Error: " . $error->getMessage();
}

if (isset($_POST['submit'])) {
  try {
    require "common.php";

    $account_code = $_POST['account_code'];

    // Query to get the results from the view
    $sql = "
      SELECT 
        Average_Obligation_Amount, 
        Average_Outlayed_Amount 
      FROM 
        Average_Funding 
      WHERE 
        Main_Account_Code = :account_code
    ";
    
    $statement = $connection->prepare($sql);
    $statement->bindParam(':account_code', $account_code, PDO::PARAM_STR);
    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  } catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
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
    select, input[type="submit"] {
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
    <h2>Find average amounts based on Federal Account Code</h2>

    <form method="post">
      <div class="form-group">
        <label for="account_code">Federal Account Code</label>
        <select id="account_code" name="account_code" required>
          <option value="">Select an Account Code</option>
          <?php foreach ($account_codes as $code) : ?>
            <option value="<?php echo htmlspecialchars($code['Main_Account_Code']); ?>">
              <?php echo htmlspecialchars($code['Main_Account_Code']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <input type="submit" name="submit" value="View Results">
    </form>

    <?php
    if (isset($_POST['submit'])) {
      if ($result) { ?>
        <h2>Results</h2>
        <table>
          <thead>
            <tr>
              <th>Average Obligation Amount</th>
              <th>Average Outlayed Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($result as $row) { ?>
              <tr>
                <td><?php echo htmlspecialchars($row["Average_Obligation_Amount"]); ?></td>
                <td><?php echo htmlspecialchars($row["Average_Outlayed_Amount"]); ?></td>
              </tr> 
            <?php } ?>
          </tbody>
        </table>
      <?php } else { ?>
        <p>No results found for <?php echo htmlspecialchars($_POST['account_code']); ?>.</p>
      <?php }
    }
    ?>
  </div>
</body>
</html>

<?php include('footer.php'); ?>
