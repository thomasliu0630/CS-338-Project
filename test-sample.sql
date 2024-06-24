DELIMITER //
CREATE PROCEDURE covid_case(IN covid_state INT)
BEGIN
 CASE covid_state
	WHEN 1 THEN 
	SELECT
		Award.Obligation_Amount,
		Award.Outlayed_Amount,
		Award.Prime_Award_ID
	FROM Award 
    INNER JOIN Covid_Related ON Covid_Related.Prime_Award_ID = Award.Prime_Award_ID;
	WHEN 2 THEN
    SELECT
		Award.Obligation_Amount,
		Award.Outlayed_Amount,
		Award.Prime_Award_ID
	FROM Award 
    INNER JOIN Non_Covid_Related ON Non-Covid_Related.Prime_Award_ID = Award.Prime_Award_ID;
    END CASE;
END //
DELIMITER ;

-- @block
CAll covid_case(1);

-- @block
SELECT * FROM Award WHERE Prime_Award_ID = '2021CIA19D';

-- @block
SELECT count(Prime_Award_ID) AS Number_of_Awards, sum(Obligation_Amount), sum(Outlayed_Amount)
    FROM Award WHERE Agency_Identifier = 'CIA';

-- @block
UPDATE Award
	SET Primary_Place = 'Montana'
	WHERE Prime_Award_ID = '2021CIA19D';