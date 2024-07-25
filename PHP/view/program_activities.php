<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Program Activities</title>
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
            margin-top: 10px;
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
        <?php
        require('../model/mysqli_connection.php');
        require "header.php";

        $selectedPrimaryPlace = isset($_POST['Primary_Place']) ? $_POST['Primary_Place'] : '';
        $recipientOptions = '';

        // Handle first submit for fetching recipients
        if (isset($_POST['fetch_recipients'])) {
            $selectedPrimaryPlace = $_POST['Primary_Place'];
            $sql = "SELECT DISTINCT r.Recipient_Name, r.Recipient_UEI
                    FROM Recipient r
                    JOIN Receives rc ON r.Recipient_UEI = rc.Recipient_UEI
                    JOIN Award a ON rc.Prime_Award_ID = a.Prime_Award_ID
                    WHERE a.Primary_Place = ?
                    ORDER BY r.Recipient_Name";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $selectedPrimaryPlace);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $recipientOptions .= '<option value="'.$row['Recipient_UEI'].'">'.$row['Recipient_Name'].'</option>';
                }
            } else {
                $recipientOptions = '<option value="">No recipients found</option>';
            }

        }

        // Process final form submission for selected PrimaryPlace and Recipient
        if (isset($_POST['view_results'])) {
            $selectedRecipient = isset($_POST['recipientName']) ? $_POST['recipientName'] : '';
            $sql = "SELECT DISTINCT pa.Program_Name
                    FROM Program_Activity pa
                    JOIN Provides pr ON pa.Program_Reporting_Key = pr.Program_Reporting_Key
                    JOIN Award a ON pr.Prime_Award_ID = a.Prime_Award_ID
                    JOIN Receives rc ON a.Prime_Award_ID = rc.Prime_Award_ID
                    JOIN Recipient r ON rc.Recipient_UEI = r.Recipient_UEI
                    WHERE a.Primary_Place = ?
                    AND r.Recipient_UEI = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $selectedPrimaryPlace, $selectedRecipient);
            $stmt->execute();
            $result1 = $stmt->get_result();
            $stmt->close();
        }
        ?>

        <h2>Program Activities</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="Primary_Place">Select Primary Place:</label>
                <select id="Primary_Place" name="Primary_Place" required>
                    <option value="">Select Primary Place</option>
                    <?php
                    // Fetch distinct PrimaryPlace
                    $sql = "SELECT DISTINCT Primary_Place FROM Award;";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $selected = ($row['Primary_Place'] == $selectedPrimaryPlace) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($row['Primary_Place']) . '"' . $selected . '>' . htmlspecialchars($row['Primary_Place']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <input type="submit" name="fetch_recipients" value="Fetch Recipients">
        </form>

        <form method="post" action="">
            <input type="hidden" name="Primary_Place" value="<?php echo htmlspecialchars($selectedPrimaryPlace); ?>">
            <div class="form-group">
                <label for="recipientName">Select Recipient Name:</label>
                <select id="recipientName" name="recipientName" required>
                    <option value="">Select Recipient</option>
                    <?php echo $recipientOptions; ?>
                </select>
            </div>
            <input type="submit" name="view_results" value="View Results">
        </form>

        <?php
        if (isset($result1)) {
            echo '<h2>Results</h2>';
            echo '<table>';
            echo '<thead><tr><th>Program Activities</th></tr></thead>';
            echo '<tbody>';
            while ($row = $result1->fetch_assoc()) {
                echo '<tr><td>' . htmlspecialchars($row["Program_Name"]) . '</td></tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No results found for the selected criteria.</p>';
        }
        ?>
        <?php include('footer.php') ?>
    </div>
</body>
</html>
