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

    function lookup_covid($covid_state) {
        global $db;
        $query = 'CAll covid_case(:covid_state)';
        $statement = $db->prepare($query);
        $statement->bindValue(':covid_state', $covid_state);
        $statement->execute();
        $awards = $statement->fetchAll();
        $statement->closeCursor();
        return $awards;
    }

    function lookup_outlier() {
        global $db;
        $query = '(SELECT Primary_Place, Outlayed_Amount FROM Award ORDER BY Outlayed_Amount DESC LIMIT 5)
                    UNION
                    (SELECT Primary_Place, Outlayed_Amount FROM Award ORDER BY Outlayed_Amount ASC LIMIT 5)';
        $statement = $db->prepare($query);
        $statement->execute();
        $awards = $statement->fetchAll();
        $statement->closeCursor();
        return $awards;
    }
    