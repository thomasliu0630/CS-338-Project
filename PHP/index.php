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
<h1>CS 338 Group 15 PHP Web Application</h1>
<h2>Main Menu</h2>
    <ul>
    <li>
        <a href="view/award_list.php"><strong>Award Search</strong></a> - Search for an award using ID
    </li>
    <li>
        <a href="view/outlier_list.php"><strong>Outlier Locations</strong></a> - finding the locations with the most and least amount of funding
    </li>
    <li>
        <a href="view/covid_list.php"><strong>Covid-19 Awards</strong></a> - filter awards by whether they were Covid-19 related
    </li>
    </ul>
    