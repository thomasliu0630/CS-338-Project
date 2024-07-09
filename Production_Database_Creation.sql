-- @block
-- Creating the tables

CREATE TABLE Federal_Account(
    Main_Account_Code VARCHAR(255) NOT NULL UNIQUE,
    Account_Title TEXT,
    Agency_Name VARCHAR(255) NOT NULL,
    PRIMARY KEY (Main_Account_Code)
);
CREATE TABLE Agency(
    Agency_Identifier VARCHAR(255) NOT NULL UNIQUE,
    Agency_Name VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (Agency_Identifier)
);
CREATE TABLE Award(
    Obligation_Amount TEXT,
    Outlayed_Amount TEXT,
    Primary_Place TEXT,
    Agency_Identifier VARCHAR(255),
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    Object_Class TEXT,
    PRIMARY KEY (Prime_Award_ID)
);
CREATE TABLE Recipient(
    Recipient_Name TEXT,
    Recipient_UEI VARCHAR(255) NOT NULL UNIQUE,
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    CONSTRAINT PK_Recipient PRIMARY KEY (Recipient_UEI, Prime_Award_ID)
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
    Prime_Award_ID VARCHAR(255) NOT NULL,
    Federal_Account_Funding_Award TEXT,
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

-- @block
-- Adding the foreign keys

ALTER TABLE Federal_Account
ADD FOREIGN KEY (Agency_Name) REFERENCES Agency(Agency_Name);

ALTER TABLE Provides
ADD FOREIGN KEY (Program_Reporting_Key) REFERENCES Program_Activity(Program_Reporting_Key),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Award
ADD FOREIGN KEY (Agency_Identifier) REFERENCES Agency(Agency_Identifier);

ALTER TABLE Recipient
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Covid_Related
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Non_Covid_Related
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

ALTER TABLE Award_Uses
ADD FOREIGN KEY (Main_Account_Code) REFERENCES Federal_Account(Main_Account_Code),
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);