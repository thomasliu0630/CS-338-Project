<?php 

    function lookup_award($award_id) {
        global $db;
        $query = 'SELECT * FROM award WHERE award.Prime_Award_ID = :award_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':award_id', $award_id);
        $statement->execute();
        $awards = $statement->fetchAll();
        $statement->closeCursor();
        return $awards;
    }

    