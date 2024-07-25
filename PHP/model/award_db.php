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

    function lookup_covid($covid_state, $amount_range) {
        global $db;
        $query = 'Select * from (        
                WITH Variables AS (
                    SELECT :covid_state AS is_covid_related, :amount_range AS outlaid_amount_range
                )
                SELECT 
                        CASE
                                            WHEN V.is_covid_related = 1 THEN C.Prime_Award_ID
                                            WHEN V.is_covid_related = 2 THEN NC.Prime_Award_ID
                                            ELSE A.Prime_Award_ID
                                        END AS AwardID
                                    FROM 
                                        Award A
                                    LEFT JOIN 
                                        Covid_related C ON A.Prime_Award_ID = C.Prime_Award_ID
                                    LEFT JOIN 
                                        Non_Covid_related NC ON A.Prime_Award_ID = NC.Prime_Award_ID
                                    JOIN
                                        Variables V
                WHERE 
                    (
                        (V.is_covid_related != 0 AND (
                            (V.outlaid_amount_range = \'0-5000000\' AND A.Outlayed_Amount BETWEEN 0 AND 5000000) OR
                            (V.outlaid_amount_range = \'5000001-10000000\' AND A.Outlayed_Amount BETWEEN 5000001 AND 10000000) OR
                            (V.outlaid_amount_range = \'10000001-15000000\' AND A.Outlayed_Amount BETWEEN 10000001 AND 15000000) OR
                            (V.outlaid_amount_range = \'15000001-20000000\' AND A.Outlayed_Amount BETWEEN 15000001 AND 20000000) OR
                            (V.outlaid_amount_range = \'20000001-25000000\' AND A.Outlayed_Amount BETWEEN 20000001 AND 25000000) OR
                            (V.outlaid_amount_range = \'25000001+\' AND A.Outlayed_Amount > 25000000)
                        )) OR
                        (V.is_covid_related = 1 AND (
                            (V.outlaid_amount_range = \'0-5000000\' AND C.Covid_Outlayed_Amount BETWEEN 0 AND 5000000) OR
                            (V.outlaid_amount_range = \'5000001-10000000\' AND C.Covid_Outlayed_Amount BETWEEN 5000001 AND 10000000) OR
                            (V.outlaid_amount_range = \'10000001-15000000\' AND C.Covid_Outlayed_Amount BETWEEN 10000001 AND 15000000) OR
                            (V.outlaid_amount_range = \'15000001-20000000\' AND C.Covid_Outlayed_Amount BETWEEN 15000001 AND 20000000) OR
                            (V.outlaid_amount_range = \'20000001-25000000\' AND C.Covid_Outlayed_Amount BETWEEN 20000001 AND 25000000) OR
                            (V.outlaid_amount_range = \'25000001+\' AND C.Covid_Outlayed_Amount > 25000000)
                        ))
                    )) as AwardView where AwardID is not null;';
        $statement = $db->prepare($query);
        $statement->bindValue(':covid_state', $covid_state);
        $statement->bindValue(':amount_range', $amount_range);
        $statement->execute();  
        $awards = $statement->fetchAll();
        $statement->closeCursor();
        return $awards;
    }

    function lookup_outlier() {
        global $db;
        $query = '(SELECT Primary_Place, Outlayed_Amount FROM Award ORDER BY Outlayed_Amount DESC LIMIT 5)
                    UNION
                    (SELECT Primary_Place, Outlayed_Amount FROM Award ORDER BY Outlayed_Amount ASC LIMIT 5);';
        $statement = $db->prepare($query);
        $statement->execute();
        $awards = $statement->fetchAll();
        $statement->closeCursor();
        return $awards;
    }
    
    function compareoo() {
        global $db;
        $query = 'SELECT 
            primary_place, 
            SUM(Obligation_Amount) as Total_Obligation, 
            SUM(Outlayed_Amount) as Total_Outlayed 
        FROM 
            Award 
        GROUP BY 
            primary_place
        ORDER BY 
            primary_place;';
        $statement = $db->prepare($query);
        $statement->execute();
        $awards = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $awards;
    }

    function agency_charts() {
        global $db;
        $query = 'SELECT 
            ag.Agency_Name, 
            SUM(a.Obligation_Amount) as Total_Obligation 
        FROM 
            Award a
        INNER JOIN 
            Agency ag ON a.Agency_Identifier = ag.Agency_Identifier
        GROUP BY 
            ag.Agency_Name
        ORDER BY 
            Total_Obligation DESC
        LIMIT 10;';
        $statement = $db->prepare($query);
        $statement->execute();
        $awards = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $awards;
    }

    function recipient_charts() {
        global $db;
        $query = 'SELECT 
            r.Recipient_Name, 
            SUM(a.Obligation_Amount) as Total_Obligation 
        FROM 
            Award a
        INNER JOIN 
            Receives rec ON a.Prime_Award_ID = rec.Prime_Award_ID
        INNER JOIN 
            Recipient r ON rec.Recipient_UEI = r.Recipient_UEI
        GROUP BY 
            r.Recipient_Name
        ORDER BY 
            Total_Obligation DESC
        LIMIT 12;';
        $statement = $db->prepare($query);
        $statement->execute();
        $awards = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $awards;
    }

    function lookup_account($account_code) {
        global $db;
        $query = '
                SELECT 
                    Average_Obligation_Amount,
                    Average_Outlayed_Amount
                FROM 
                    Average_Funding
                WHERE 
                    Main_Account_Code = :account_code;';
        $statement = $db->prepare($query);
        $statement->bindValue(':account_code', $account_code);
        $statement->execute();
        $result = $statement->fetchAll();
        $statement->closeCursor();
        return $result;
    }

    function agency_performance($agency_ID) {
        global $db;
        $query = '
                SELECT
                    ag.Agency_Name,
                    COUNT(aw.Prime_Award_ID) AS Total_Number_of_Awards,
                    SUM(aw.Obligation_Amount) AS Total_Obligated_Amount,
                    SUM(aw.Outlayed_Amount) AS Total_Outlayed_Amount
                FROM
                    Agency ag
                JOIN
                    Award aw ON ag.Agency_Identifier = aw.Agency_Identifier
                WHERE
                    ag.Agency_Identifier = :agency_ID
                GROUP BY
                    ag.Agency_Name;
                ';
        $statement = $db->prepare($query);
        $statement->bindValue(':agency_ID', $agency_ID);
        $statement->execute();
        $result = $statement->fetchAll();
        $statement->closeCursor();
        return $result;
    }

