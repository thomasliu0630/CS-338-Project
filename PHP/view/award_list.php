<?php include('view/header.php') ?>


<?php

if (isset($_POST['submit'])) {
  try {
    require "common.php";

    $connection = new PDO($dsn, $username, $password);

    $sql = "SELECT *
    FROM award
    WHERE Prime_Award_ID = :award_ID";

    $award_ID = $_POST['award_ID'];

    $statement = $connection->prepare($sql);
    $statement->bindParam(':award_ID', $award_ID, PDO::PARAM_STR);
    $statement->execute();

    $result = $statement->fetchAll();
  } catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
  }
}
?>
<?php require "view/header.php"; ?>

<?php
if (isset($_POST['submit'])) {
  if ($result && $statement->rowCount() > 0) { ?>
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
<td><?php echo escape($row["Obligation_Amount"]); ?></td>
<td><?php echo escape($row["Outlayed_Amount"]); ?></td>
<td><?php echo escape($row["Primary_Place"]); ?></td>
<td><?php echo escape($row["Agency_Identifier"]); ?></td>
<td><?php echo escape($row["Prime_Award_ID"]); ?></td>
      </tr>
    <?php } ?>
      </tbody>
  </table>
  <?php } else { ?>
    > No results found for <?php echo escape($_POST['award_ID']); ?>.
  <?php }
} ?>

<h2>Find award based on ID</h2>

<form method="post">
  <label for="award_ID">Award ID</label>
  <input type="text" id="award_ID" name="award_ID">
  <input type="submit" name="submit" value="View Results">
</form>

<?php include('view/footer.php') ?>