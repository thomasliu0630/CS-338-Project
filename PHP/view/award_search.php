<?php include('header.php'); ?>

<?php
require('../model/database.php');
require('../model/award_db.php');

$connection = new PDO($dsn, $username, $password);
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
  // Fetch all Award IDs for the dropdown
  $query = "SELECT DISTINCT Prime_Award_ID FROM Award";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $awards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $error) {
  echo "Error fetching award IDs: " . $error->getMessage();
}

if (isset($_POST['submit'])) {
  try {
    require "common.php";

    $award_ID = $_POST['award_ID'];
    $result = lookup_award($award_ID);
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
    }
    label {
      margin-top: 10px;
      text-align: left;
      width: 100%;
    }
    select, input[type="text"], input[type="submit"] {
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
    <h2>Find award based on ID</h2>

    <form method="post">
      <label for="award_ID">Award ID</label>
      <select id="award_ID" name="award_ID">
        <option value="">Select an award ID</option>
        <?php foreach ($awards as $award) : ?>
          <option value="<?php echo htmlspecialchars($award['Prime_Award_ID']); ?>">
            <?php echo htmlspecialchars($award['Prime_Award_ID']); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <input type="submit" name="submit" value="View Results">
    </form>

    <?php
    if (isset($_POST['submit'])) {
      if ($result) { ?>
        <h2>Results</h2>
        <table>
          <thead>
            <tr>
              <th>Obligation Amount</th>
              <th>Outlayed Amount</th>
              <th>Primary Place</th>
              <th>Agency Identifier</th>
              <th>Prime Award ID</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($result as $row) { ?>
              <tr>
                <td><?php echo htmlspecialchars($row["Obligation_Amount"]); ?></td>
                <td><?php echo htmlspecialchars($row["Outlayed_Amount"]); ?></td>
                <td><?php echo htmlspecialchars($row["Primary_Place"]); ?></td>
                <td><?php echo htmlspecialchars($row["Agency_Identifier"]); ?></td>
                <td><?php echo htmlspecialchars($row["Prime_Award_ID"]); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } else { ?>
        <p>No results found for <?php echo htmlspecialchars($_POST['award_ID']); ?>.</p>
      <?php }
    }
    ?>
  </div>
</body>
</html>

<?php include('footer.php'); ?>
