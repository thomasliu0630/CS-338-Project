-- @block
-- Creating the tables

CREATE TABLE Federal_Account(
    Main_Account_Code VARCHAR(255) NOT NULL UNIQUE,
    Account_Title TEXT,
    Agency_Identifier VARCHAR(255),
    PRIMARY KEY (Main_Account_Code)
);
CREATE TABLE Agency(
    Agency_Identifier VARCHAR(255) NOT NULL UNIQUE,
    Agency_Name TEXT,
    PRIMARY KEY (Agency_Identifier)
);
CREATE TABLE Treasury(
    Main_Account_Code VARCHAR(255) NOT NULL UNIQUE,
    Account_Code VARCHAR(255) NOT NULL UNIQUE,
    Account_Name TEXT,
    Obligations_Incurred TEXT,
    Unobligated_Balance TEXT,
    CONSTRAINT PK_Treasury PRIMARY KEY (Main_Account_Code, Account_Code)
);
CREATE TABLE Award(
    Obligation_Amount TEXT,
    Outlayed_Amount TEXT,
    Primary_Place TEXT,
    Agency_Identifier VARCHAR(255),
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (Prime_Award_ID)
);
CREATE TABLE Recipient(
    Recipient_Name TEXT,
    Business_Type TEXT,
    Recipient_UEI VARCHAR(255) NOT NULL UNIQUE,
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    CONSTRAINT PK_Recipient PRIMARY KEY (Recipient_UEI, Prime_Award_ID)
);
CREATE TABLE Child_Recipient(
    Child_Recipient VARCHAR(255) NOT NULL UNIQUE,
    Recipient_UEI VARCHAR(255) NOT NULL UNIQUE,
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    CONSTRAINT PK_Child_Recipient PRIMARY KEY (Child_Recipient, Recipient_UEI, Prime_Award_ID)
);
CREATE TABLE Domestic_Recipient(
    D_State TEXT,
    Congressional_District TEXT,
    County TEXT,
    Recipient_UEI VARCHAR(255) NOT NULL UNIQUE,
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    CONSTRAINT PK_Domestic PRIMARY KEY (Recipient_UEI, Prime_Award_ID)
);      
CREATE TABLE Foreign_Recipient(
    Recipient_UEI VARCHAR(255) NOT NULL UNIQUE,
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    Country TEXT,
    CONSTRAINT PK_Foreign PRIMARY KEY (Recipient_UEI, Prime_Award_ID)
);
CREATE TABLE Covid_Related(
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    Covid_Obligated_Amount TEXT,
    Covid_Outlayed_Amount TEXT,
    PRIMARY KEY (Prime_Award_ID)
);
CREATE TABLE Non_Covid_Related(
    Prime_Award_ID VARCHAR(255) PRIMARY KEY NOT NULL UNIQUE
);
CREATE TABLE Award_Uses(
    Main_Account_Code VARCHAR(255) NOT NULL,
    Account_Code VARCHAR(255) NOT NULL,
    Treasury_Account_Funding_Award TEXT,
    Prime_Award_ID VARCHAR(255) NOT NULL,
    CONSTRAINT PK_Uses PRIMARY KEY (Main_Account_Code, Account_Code, Prime_Award_ID)
);  
CREATE TABLE Associated_With(
    Main_Account_Code VARCHAR(255) NOT NULL UNIQUE,
    Object_Class TEXT,
    Program_Reporting_Key VARCHAR(255) NOT NULL UNIQUE,
    CONSTRAINT PK_Associated PRIMARY KEY (Main_Account_Code, Program_Reporting_Key)
);
CREATE TABLE Program_Activity(
    Program_Reporting_Key VARCHAR(255) NOT NULL UNIQUE,
    Program_Name TEXT,
    PRIMARY KEY (Program_Reporting_Key)
);
CREATE TABLE Provides(
    Program_Reporting_Key VARCHAR(255) NOT NULL,
    Prime_Award_ID VARCHAR(255) NOT NULL,
    CONSTRAINT PK_Provides PRIMARY KEY (Program_Reporting_Key, Prime_Award_ID)
);

-- @block
--Adding the foreign keys

ALTER TABLE Federal_Account
ADD FOREIGN KEY (Agency_Identifier) REFERENCES Agency(Agency_Identifier);

ALTER TABLE Provides
ADD FOREIGN KEY (Program_Reporting_Key) REFERENCES Program_Activity(Program_Reporting_Key),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Treasury
ADD FOREIGN KEY (Main_Account_Code) REFERENCES Federal_Account(Main_Account_Code);

ALTER TABLE Award
ADD FOREIGN KEY (Agency_Identifier) REFERENCES Agency(Agency_Identifier);

ALTER TABLE Recipient
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Child_Recipient
ADD FOREIGN KEY (Recipient_UEI) REFERENCES Recipient(Recipient_UEI),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Recipient(Prime_Award_ID);

ALTER TABLE Domestic_Recipient
ADD FOREIGN KEY (Recipient_UEI) REFERENCES Recipient(Recipient_UEI),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Recipient(Prime_Award_ID);

ALTER TABLE Foreign_Recipient
ADD FOREIGN KEY (Recipient_UEI) REFERENCES Recipient(Recipient_UEI),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Recipient(Prime_Award_ID);

ALTER TABLE Covid_Related
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Non_Covid_Related
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Award_Uses
ADD FOREIGN KEY (Main_Account_Code) REFERENCES Treasury(Main_Account_Code),
ADD FOREIGN KEY (Account_Code) REFERENCES Treasury(Account_Code);

ALTER TABLE Associated_With
ADD FOREIGN KEY (Main_Account_Code) REFERENCES Federal_Account(Main_Account_Code),
ADD FOREIGN KEY (Program_Reporting_Key) REFERENCES Program_Activity(Program_Reporting_Key);

-- @block
--Populating the tables

INSERT INTO Agency(Agency_Identifier, Agency_Name)
VALUES
('DOD', 'Department of Defence'),
('CIA', 'Central Intelligence Agency'),
('FBI', 'Federal Investigation Bureau');

INSERT INTO Federal_Account(Main_Account_Code, Account_Title, Agency_Identifier)
VALUES
(001,'first','DOD'),
(002,'second','CIA'),
(003,'third','FBI');

INSERT INTO Treasury(Main_Account_Code, Account_Code, Account_Name, Obligations_Incurred, Unobligated_Balance)
VALUES
(001, 00101, 'first', '10000', '15000'),
(002, 00201, 'second', '20000', '5000'),
(003, 00202, 'third', '30000', '2000');

INSERT INTO Award(Obligation_Amount, Outlayed_Amount, Primary_Place, Agency_Identifier, Prime_Award_ID)
VALUES
('15000', '10000', 'Washington', 'DOD', '2022DOD45B'),
('2000', '500', 'Montana', 'CIA', '2021CIA19D'),
('6500', '3000', 'Illinois', 'CIA', '2019CIA06M'),
('3000', '200', 'California', 'FBI', '2015FBI03A');

INSERT INTO Recipient(Recipient_Name, Business_Type, Recipient_UEI, Prime_Award_ID)
VALUES
('Tom', 'Farmer', 'T234567FW', '2022DOD45B'),
('Jerry', 'Hunter', 'J785901HM', '2021CIA19D'),
('Susan', 'Baker', 'S906278BI', '2019CIA06M'),
('Martha', 'Banker', 'M461738BC', '2015FBI03A');

INSERT INTO Child_Recipient(Child_Recipient, Recipient_UEI, Prime_Award_ID)
VALUES
('Thomas', 'T234567FW', '2022DOD45B'),
('Jenny', 'J785901HM', '2021CIA19D'),
('Susie', 'S906278BI', '2019CIA06M'),
('Mary', 'M461738BC', '2015FBI03A');

INSERT INTO Domestic_Recipient(D_State, Congressional_District, County, Recipient_UEI, Prime_Award_ID)
VALUES
('Washington', '3A', 'Maryland', 'T234567FW', '2022DOD45B'),
('California', '2B', 'Woodstock', 'M461738BC', '2015FBI03A');

INSERT INTO Foreign_Recipient(Recipient_UEI, Prime_Award_ID, Country)
VALUES
('J785901HM', '2021CIA19D', 'China'),
('S906278BI', '2019CIA06M', 'Mexico');

INSERT INTO Covid_Related(Prime_Award_ID, Covid_Obligated_Amount, Covid_Outlayed_Amount)
VALUES
('2021CIA19D', '1500', '500'),
('2015FBI03A', '2000', '150');

INSERT INTO Non_Covid_Related(Prime_Award_ID)
VALUES
('2022DOD45B'),
('2019CIA06M');

INSERT INTO Award_Uses(Main_Account_Code, Account_Code, Treasury_Account_Funding_Award, Prime_Award_ID)
VALUES
(001, 00101, '150', '2022DOD45B'),
(002, 00201, '200', '2021CIA19D'),
(002, 00201, '100', '2019CIA06M'),
(003, 00202, '300', '2015FBI03A');

INSERT INTO Program_Activity(Program_Reporting_Key, Program_Name)
VALUES
('US1256', 'Stimulus'),
('US2270', 'Loan');

INSERT INTO Provides(Program_Reporting_Key, Prime_Award_ID)
VALUES
('US1256', '2022DOD45B'),
('US1256', '2021CIA19D'),
('US1256', '2019CIA06M'),
('US2270', '2015FBI03A');

INSERT INTO Associated_With(Main_Account_Code, Object_Class, Program_Reporting_Key)
VALUES
(001, 'Temp', 'US1256'),
(003, 'Permanent', 'US2270');