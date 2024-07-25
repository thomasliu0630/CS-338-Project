<?php 
session_start();
include('header.php');
require('../model/database.php');
require('../model/award_db.php');

$connection = new PDO($dsn, $username, $password);
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['submit'])) {
  try {
    require "common.php";

    // Fetch distinct values for the dropdown
    $award_ID = $_POST['award_ID'];
    $_SESSION["award_ID"] = $award_ID;

    $query = "SELECT r.Recipient_UEI
              FROM Receives r
              WHERE r.Prime_Award_ID = :award_id;";
    $stmt = $connection->prepare($query);
    $stmt->bindValue(':award_id', $award_ID);
    $stmt->execute();
    $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  catch(PDOException $error) {
    echo "Error fetching recipients: " . $error->getMessage();
  }
} 

if (isset($_POST['submit2'])) {
  $award_ID = $_SESSION["award_ID"];
  $recipient_uei = $_POST['Recipient'];
  $recipient_uei2 = $_POST['Recipient_UEI2'];
  $query = "UPDATE Receives	
            SET Recipient_UEI = :recipient_uei2
            WHERE Recipient_UEI = :recipient_uei AND Prime_Award_ID = :award_id;";
  $stmt = $connection->prepare($query); 
  $stmt->bindValue(':recipient_uei2', $recipient_uei2);
  $stmt->bindValue(':recipient_uei', $recipient_uei);
  $stmt->bindValue(':award_id', $award_ID);
  $stmt->execute();
  echo "<h2>Update Successful</h2>";
}

try {
  // Fetch all Award IDs for the dropdown
  $query = "SELECT DISTINCT Prime_Award_ID FROM Award";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $awards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $error) {
  echo "Error fetching award IDs: " . $error->getMessage();
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
  </style>
</head>
<body>
  <div class="container">
    <h2>Look up Award ID</h2>
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
      <input type="submit" name="submit" value="View Recipients">
    </form>

    <?php if (isset($recipients)) : ?>
      <form method="post" action="">
        <label for="Recipient">Recipient:</label>
        <select id="Recipient" name="Recipient">
          <option value="">Select a recipient</option>
          <?php foreach ($recipients as $recipient) : ?>
            <option value="<?php echo htmlspecialchars($recipient['Recipient_UEI']); ?>">
              <?php echo htmlspecialchars($recipient['Recipient_UEI']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <label for="Recipient_UEI2">Recipient UEI</label>
        <input type="text" id="Recipient_UEI2" name="Recipient_UEI2">
        <input type="submit" name="submit2" value="Update Recipient">
      </form>
    <?php endif; ?>
  </div>
</body>
</html>

<?php include('footer.php') ?>
