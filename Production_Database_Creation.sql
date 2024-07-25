-- @block
-- Creating the tables
CREATE TABLE Agency(
    Agency_Identifier VARCHAR(255) NOT NULL UNIQUE,
    Agency_Name VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (Agency_Identifier)
);

CREATE TABLE Federal_Account(
    Main_Account_Code VARCHAR(255) NOT NULL UNIQUE,
    Account_Title TEXT,
    Agency_Name VARCHAR(255) NOT NULL,
    PRIMARY KEY (Main_Account_Code)
);

CREATE TABLE Award(
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    Obligation_Amount DECIMAL(20,2) NOT NULL,
    Outlayed_Amount DECIMAL(20,2) NOT NULL,
    Primary_Place TEXT NOT NULL,
    Agency_Identifier VARCHAR(255),
    Object_Class TEXT,
    PRIMARY KEY (Prime_Award_ID)
);

CREATE TABLE Recipient(
    Recipient_UEI VARCHAR(255) NOT NULL,
    Recipient_Name TEXT,
    PRIMARY KEY (Recipient_UEI)
);

CREATE TABLE Covid_Related(
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    Covid_Obligated_Amount DECIMAL(20,2) NOT NULL,
    Covid_Outlayed_Amount DECIMAL(20,2) NOT NULL,
    PRIMARY KEY (Prime_Award_ID)
);

CREATE TABLE Non_Covid_Related(
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (Prime_Award_ID)
);

CREATE TABLE Award_Uses(
    Main_Account_Code VARCHAR(255) NOT NULL UNIQUE,
    Prime_Award_ID VARCHAR(255) NOT NULL,
    CONSTRAINT PK_Uses PRIMARY KEY (Main_Account_Code, Prime_Award_ID)
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

CREATE TABLE Receives(
    Recipient_UEI VARCHAR(255) NOT NULL,
    Prime_Award_ID VARCHAR(255) NOT NULL,
    CONSTRAINT PK_Receives PRIMARY KEY (Recipient_UEI, Prime_Award_ID)
);

-- @block
-- Adding the foreign keys

ALTER TABLE Federal_Account
ADD FOREIGN KEY (Agency_Name) REFERENCES Agency(Agency_Name);

ALTER TABLE Provides
ADD FOREIGN KEY (Program_Reporting_Key) REFERENCES Program_Activity(Program_Reporting_Key),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Award
ADD FOREIGN KEY (Agency_Identifier) REFERENCES Agency(Agency_Identifier);

ALTER TABLE Covid_Related
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Non_Covid_Related
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Award_Uses
ADD FOREIGN KEY (Main_Account_Code) REFERENCES Federal_Account(Main_Account_Code),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Receives
ADD FOREIGN KEY (Recipient_UEI) REFERENCES Recipient(Recipient_UEI),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

-- @block
-- Populating the tables

INSERT INTO Agency(Agency_Identifier, Agency_Name)
VALUES
('DOD', 'Department of Defence'),
('CIA', 'Central Intelligence Agency'),
('FBI', 'Federal Investigation Bureau');

INSERT INTO Award(Obligation_Amount, Outlayed_Amount, Primary_Place, Agency_Identifier, Prime_Award_ID, Object_Class)
VALUES
('15000', '10000', 'Washington', 'DOD', '2022DOD45B','25.3: Other goods and services from Federal sources'),
('2000', '500', 'Montana', 'CIA', '2021CIA19D','25.1: Advisory and assistance services'),
('6500', '3000', 'Illinois', 'CIA', '2019CIA06M','25.2: Other services from non-Federal sources'),
('3000', '200', 'California', 'FBI', '2015FBI03A','32.0: Land and structures;33.0: Investments and loans');

INSERT INTO Award_Uses(Main_Account_Code, Prime_Award_ID)
VALUES
(001, '2022DOD45B'),
(002, '2021CIA19D'),
(002, '2019CIA06M'),
(003, '2015FBI03A');

INSERT INTO Covid_Related(Prime_Award_ID, Covid_Obligated_Amount, Covid_Outlayed_Amount)
VALUES
('2021CIA19D', '1500', '500'),
('2015FBI03A', '2000', '150');

INSERT INTO Federal_Account(Main_Account_Code, Account_Title, Agency_Name)
VALUES
(001,'first','DOD'),
(002,'second','CIA'),
(003,'third','FBI');

INSERT INTO Non_Covid_Related(Prime_Award_ID)
VALUES
('2022DOD45B'),
('2019CIA06M');

INSERT INTO Program_Activity(Program_Reporting_Key,Program_Name) 
VALUES
('001','AUDIT'),
('0015','ENTERPRISE DATA COLLECTION AND DISSEMINATION SYSTEMS;0803');
('0017','DATA QUALITY INITIATIVE');

INSERT INTO Provides(Program_Reporting_Key, Prime_Award_ID)
VALUES
('0001', '2022DOD45B'),
('0017', '2021CIA19D'),
('0017', '2019CIA06M'),
('00001516', '2015FBI03A');


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





INSERT INTO Program_Activity(Program_Reporting_Key, Program_Name)
VALUES
('US1256', 'Stimulus'),
('US2270', 'Loan');


INSERT INTO Associated_With(Main_Account_Code, Object_Class, Program_Reporting_Key)
VALUES
(001, 'Temp', 'US1256'),
(003, 'Permanent', 'US2270');