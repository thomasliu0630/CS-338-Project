<?php include('header.php'); ?>

<?php
require('../model/database.php');
require('../model/award_db.php');

try {
  require "common.php";

  $connection = new PDO($dsn, $username, $password);
  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch distinct values for the dropdown
  $query = "SELECT DISTINCT Agency_Identifier, Agency_Name FROM Agency";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $agencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $error) {
  echo $sql . "<br>" . $error->getMessage();
}

if (isset($_POST['submit'])) {
  $agency_ID = $_POST['Agency'];
  $result = agency_performance($agency_ID);
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
    <h2>Look up Agency Performance</h2>

    <form method="post" action="">
      <div class="form-group">
        <label for="Agency">Agency:</label>
        <select id="Agency" name="Agency" required>
          <option value="">Select an agency</option>
          <?php foreach ($agencies as $agency) : ?>
            <option value="<?php echo htmlspecialchars($agency['Agency_Identifier']); ?>">
              <?php echo htmlspecialchars($agency['Agency_Name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <input type="submit" name="submit" value="Submit">
    </form>

    <?php
    if (isset($_POST['submit'])) {
      if ($result) { ?>
        <h2>Results</h2>
        <table>
          <thead>
            <tr>  
              <th>Number of Awards</th>
              <th>Obligated Amount Sum</th>
              <th>Outlayed Amount Sum</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($result as $row) { ?>
              <tr>
                <td><?php echo htmlspecialchars($row["Total_Number_of_Awards"]); ?></td>
                <td><?php echo htmlspecialchars($row["Total_Obligated_Amount"]); ?></td>
                <td><?php echo htmlspecialchars($row["Total_Outlayed_Amount"]); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } else { ?>
        <p>No results found for <?php echo htmlspecialchars($_POST['agency_ID']); ?>.</p>
      <?php }
    }
    ?>
  </div>
</body>
</html>

<?php include('footer.php'); ?>
