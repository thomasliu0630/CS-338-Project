<?php include('header.php') ?>


<?php
require('../model/database.php');
require('../model/award_db.php');
require "header.php";
?>
<h2>Find locations with outlying outlayed amounts</h2>

<?php

  try {
    require "common.php";

    $connection = new PDO($dsn, $username, $password);

    $result = lookup_outlier();
  } catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
  }
  if ($result) { ?>
    <h2>Results</h2>

    <table>
      <thead>
<tr>
  <th>Primary Place</th>
  <th>Outlayed Amount</th>
</tr>
      </thead>
      <tbody>
  <?php foreach ($result as $row) { ?>
      <tr>
<td><?php echo escape($row["Primary_Place"]); ?></td>
<td><?php echo escape($row["Outlayed_Amount"]); ?></td>
      </tr>
    <?php } ?>
      </tbody>
  </table>
  <?php }
?>

<?php include('footer.php') ?>

<ul>
    <a href="../index.php"><strong>Main Menu</strong></a> - Return to main menu
</ul>