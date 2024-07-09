<?php include('header.php') ?>


<?php
require('../model/database.php');
require('../model/award_db.php');

if (isset($_POST['submit'])) {
  try {
    require "common.php";

    $connection = new PDO($dsn, $username, $password);

    $award_ID = $_POST['award_ID'];

    $result = lookup_award($award_ID);
  } catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
  }
}
?>
<?php require "header.php"; ?>

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

<?php include('footer.php') ?>

<ul>
    <a href="../index.php"><strong>Main Menu</strong></a> - Return to main menu
</ul>