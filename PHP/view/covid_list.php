<?php include('header.php') ?>


<?php
require('../model/database.php');
require('../model/award_db.php');

if (isset($_POST['submit'])) {
  try {
    require "common.php";

    $connection = new PDO($dsn, $username, $password);

    $covid_status = $_POST['covid_status'];

    $result = lookup_covid($covid_status);
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
  <th>Prime Award ID</th>
</tr>
      </thead>
      <tbody>
  <?php foreach ($result as $row) { ?>
      <tr>
<td><?php echo escape($row["Obligation_Amount"]); ?></td>
<td><?php echo escape($row["Outlayed_Amount"]); ?></td>
<td><?php echo escape($row["Prime_Award_ID"]); ?></td>
      </tr>
    <?php } ?>
      </tbody>
  </table>
  <?php } else { ?>
    > No results found for <?php echo escape($_POST['covid_status']); ?>.
  <?php }
} ?>

<h2>Browse awards based on Covid-status</h2>

<form method="post" id="list__header_select" class="list__header_select">
    <input type="hidden" name="action" value="list_assignments">
    <select name="covid_status" required>
        <option value="0">View All</option>
        <option value="1">Covid-related</option>
        <option value="2">Non Covid-related</option>
        <option value="3">Both</option>
    </select>
    <input type="submit" name="submit" value="View Results">
</form>


<?php include('footer.php') ?>

<ul>
    <a href="../index.php"><strong>Main Menu</strong></a> - Return to main menu
</ul>