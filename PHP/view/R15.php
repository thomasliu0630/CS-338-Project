<?php include('header.php') ?>

<?php
require('../model/database.php');
require('../model/award_db.php');

try {
  require "common.php";

  $connection = new PDO($dsn, $username, $password);

  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $dropQueries = [
    "DROP VIEW IF EXISTS Place_List;",
    "DROP VIEW IF EXISTS Agency_List;",
    "DROP VIEW IF EXISTS Program_Activity_List;",
    "DROP VIEW IF EXISTS Recipient_List;",
    "DROP VIEW IF EXISTS Object_Class_List;"
  ];

  foreach ($dropQueries as $query) {
      $stmt = $connection->prepare($query);
      $stmt->execute();
  }

  // Create views
  $createQueries = [
      "CREATE VIEW Place_List AS
        SELECT DISTINCT Primary_Place
        FROM Award
        ORDER BY Primary_Place;;",
      
      "CREATE VIEW Agency_List AS
        SELECT DISTINCT Agency_Name, Agency_Identifier
        FROM Agency
        ORDER BY Agency_Name;",
      
      "CREATE VIEW Program_Activity_List AS
        SELECT DISTINCT Program_Name, Program_Reporting_Key
        FROM Program_Activity
        ORDER BY Program_Name;",
      
      "CREATE VIEW Recipient_List AS
        SELECT DISTINCT Recipient_Name, Recipient_UEI
        FROM Recipient
        ORDER BY Recipient_Name;",
      
      "CREATE VIEW Object_Class_List AS
        SELECT DISTINCT Object_Class
        FROM Award
        ORDER BY Object_Class;"
  ];

  foreach ($createQueries as $query) {
      $stmt = $connection->prepare($query);
      $stmt->execute();
  }

  // Fetching the results for each view
  $fetchQueries = [
      "SELECT * FROM Place_List;",
      "SELECT * FROM Agency_List;",
      "SELECT * FROM Program_Activity_List;",
      "SELECT * FROM Recipient_List;",
      "SELECT * FROM Object_Class_List;"
  ];

  $result = [];

  foreach ($fetchQueries as $query) {
      $stmt = $connection->prepare($query);
      $stmt->execute();
      $result[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  list($Place_List, $Agency_List, $Program_Activity_List, $Recipient_List, $Object_Class_List) = $result;

  // Fetch distinct values for the dropdown
  $query = "SELECT Primary_Place FROM Place_List;";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $places = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $query = "SELECT Agency_Name, Agency_Identifier FROM Agency_List;";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $agencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $query = "SELECT Program_Name, Program_Reporting_Key FROM Program_Activity_List;";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $query = "SELECT Recipient_Name, Recipient_UEI FROM Recipient_List;";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $query = "SELECT Object_Class FROM Object_Class_List;";
  $stmt = $connection->prepare($query);
  $stmt->execute();
  $objects = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  catch(PDOException $error) {
  echo "Error" . $error->getMessage();
  } 
  if (isset($_POST['submit'])) {
    $place_ID = $_POST['Primary_Place'];
    $agency_ID = $_POST['Agency'];
    $program_ID = $_POST['Program'];
    $recipient_ID = $_POST['Recipient'];
    $object_ID = $_POST['Object'];

    $query = "With award_place AS (
                SELECT a.Prime_Award_ID, a.Agency_Identifier
                FROM Award a
                Where (:place_ID = '' OR a.Primary_Place = :place_ID) AND (:object_ID = '' OR a.Object_Class = :object_ID)
              ), place_agency AS (
                SELECT ap.Prime_Award_ID
                FROM award_place ap
                Left Join Agency ag
                On ap.Agency_Identifier = ag.Agency_Identifier
                Where :agency_ID = '' OR ag.Agency_Identifier = :agency_ID
              ), agency_program AS (
                SELECT pa.Prime_Award_ID
                FROM place_agency pa
                Left Join Provides pr
                On pa.Prime_Award_ID = pr.Prime_Award_ID
                Where :program_ID = '' OR pr.Program_Reporting_Key = :program_ID
              ), program_recipient AS (
                SELECT ap1.Prime_Award_ID
                FROM agency_program ap1
                Left Join Receives r1
                On ap1.Prime_Award_ID = r1.Prime_Award_ID
                Where :recipient_ID = '' OR r1.Recipient_UEI = :recipient_ID
              )
              SELECT Award.*
              FROM Award
              Left Join program_recipient
              On Award.Prime_Award_ID = program_recipient.Prime_Award_ID
              Where Award.Prime_Award_ID = program_recipient.Prime_Award_ID;";
    $stmt = $connection->prepare($query);
    $stmt->bindvalue(":place_ID", $place_ID);
    $stmt->bindvalue(":object_ID", $object_ID);
    $stmt->bindvalue(":agency_ID", $agency_ID);
    $stmt->bindvalue(":program_ID", $program_ID);
    $stmt->bindvalue(":recipient_ID", $recipient_ID);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
?>

<style>
  form {
    display: flex;
    flex-direction: column;
    max-width: 600px;
    margin: 0 auto;
  }

  label {
    margin-top: 10px;
  }

  select {
    padding: 5px;
    margin-top: 5px;
  }

  input[type="submit"] {
    margin-top: 20px;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
  }

  input[type="submit"]:hover {
    background-color: #0056b3;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  table, th, td {
    border: 1px solid black;
  }

  th, td {
    padding: 10px;
    text-align: left;
  }
</style>

<h2>Browse Awards based on Pivot Table</h2>

<form method="post" action="">
    <label for="Primary_Place">Primary Place:</label>
    <select id="Primary_Place" name="Primary_Place">
        <option value="">Select a Primary Place</option>
        <?php foreach ($places as $place) : ?>
            <option value="<?php echo htmlspecialchars($place['Primary_Place']); ?>">
                <?php echo htmlspecialchars($place['Primary_Place']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="Agency">Agency:</label>
    <select id="Agency" name="Agency">
        <option value="">Select an Agency</option>
        <?php foreach ($agencies as $agency) : ?>
            <option value="<?php echo htmlspecialchars($agency['Agency_Identifier']); ?>">
                <?php echo htmlspecialchars($agency['Agency_Name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label for="Program">Program Name:</label>
    <select id="Program" name="Program">
        <option value="">Select a Program</option>
        <?php foreach ($programs as $program) : ?>
            <option value="<?php echo htmlspecialchars($program['Program_Reporting_Key']); ?>">
                <?php echo htmlspecialchars($program['Program_Name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label for="Recipient">Recipient Name:</label>
    <select id="Recipient" name="Recipient">
        <option value="">Select a Recipient</option>
        <?php foreach ($recipients as $recipient) : ?>
            <option value="<?php echo htmlspecialchars($recipient['Recipient_UEI']); ?>">
                <?php echo htmlspecialchars($recipient['Recipient_Name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="Object">Object Class:</label>
    <select id="Object" name="Object">
        <option value="">Select an Object Class</option>
        <?php foreach ($objects as $object) : ?>
            <option value="<?php echo htmlspecialchars($object['Object_Class']); ?>">
                <?php echo htmlspecialchars($object['Object_Class']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="submit" name="submit" value="Submit">
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
  <th>Primary_Place</th>
  <th>Agency_Identifier</th>
  <th>Prime Award ID</th>
  <th>Object Class</th>
</tr>
      </thead>
      <tbody>
  <?php foreach ($result as $row) { ?>
      <tr>
<td><?php echo escape($row["Obligation_Amount"]); ?></td>
<td><?php echo escape($row["Outlayed_Amount"]); ?></td>
<td><?php echo escape($row["Primary_Place"]); ?></td>
<td><?php echo escape($row["Agency_Identifier"]); ?></td>
<td><?php echo escape($row["Prime_Award_ID"]); ?></td>
<td><?php echo escape($row["Object_Class"]); ?></td>
      </tr>
    <?php } ?>
      </tbody>
  </table>
  <?php } else { ?>
    > No results found for selection.
  <?php }
} ?>

<?php include('footer.php') ?>
