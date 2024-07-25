<?php include('view/header.php') ?>
<?php
    require('model/database.php');
    require('model/award_db.php');
/*
    $award_id = filter_input(INPUT_POST, 'assignment_id', FILTER_DEFAULT);
    $description = filter_input(INPUT_POST, 'description', FILTER_DEFAULT);

    $award_id = filter_input(INPUT_POST, 'course_id', FILTER_DEFAULT);
    if (!$award_id) {
        $award_id = filter_input(INPUT_GET, 'course_id', FILTER_DEFAULT);
        // an id of NULL or FALSE is ok here
    }

    $action = filter_input(INPUT_POST, 'action', FILTER_DEFAULT);
    if (!$action) {
        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        if (!$action) {
            $action = 'award_id'; // assigning default value if NULL or FALSE
        }
    }

    switch($action) {
        default:
            $award = lookup_award($award_id);
            include ('view/award_list.php');
    }*/
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
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td a {
            color: #007bff;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CS 338 Group 15 PHP Web Application</h1>
        <table>
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><a href="view/award_list.php"><strong>R6 - Awards List</strong></a></td>
                    <td>Find a list of Award IDs by their covid status and outlayed amounts</td>
                </tr>
                <tr>
                    <td><a href="view/award_search.php"><strong>R7 - Award Search</strong></a></td>
                    <td>Search for an award using ID</td>
                </tr>
                <tr>
                    <td><a href="view/program_activities.php"><strong>R8 - Program Activities</strong></a></td>
                    <td>Filter awards by whether they were Covid-19 related</td>
                </tr>
                <tr>
                    <td><a href="view/replace_recipient.php"><strong>R9 - Replace Recipient</strong></a></td>
                    <td>Replace a Recipient of an Existing Award</td>
                </tr>
                <tr>
                    <td><a href="view/average_funding.php"><strong>R10 - Average Funding</strong></a></td>
                    <td>Find average amounts by agency</td>
                </tr>
                <tr>
                    <td><a href="view/agency_performance.php"><strong>R11 - Agency Performance</strong></a></td>
                    <td>Look up performance of Agencies</td>
                </tr>
                <tr>
                    <td><a href="view/agency_recipient_chart_generation.php"><strong>R12 - Compare Outlayed Obligated</strong></a></td>
                    <td>Compare Outlayed Obligated</td>
                </tr>
                <tr>
                    <td><a href="view/R13.php"><strong>R13 - Per Agency Bar Charts</strong></a></td>
                    <td>Compare Agency boxplots for Covid/non Covid</td>
                </tr>
                <tr>
                    <td><a href="view/compare_outlayed_obligated.php"><strong>R14 - Visually Comparing US States</strong></a></td>
                    <td>Compare Outlayed and Obligated amounts per state visually</td>
                </tr>
                <tr>
                    <td><a href="view/R15.php"><strong>R15 - Pivot Tables</strong></a></td>
                    <td>Select Primary Place, Agency, Program Name, Recipient and Object Class</td>
                </tr>
                <tr>
                    <td><a href="view/R16.php"><strong>R16 - Award Completion Search</strong></a></td>
                    <td>Identify Awards with a Specific Completion Rate</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
