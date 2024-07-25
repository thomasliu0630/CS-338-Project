import pandas as pd
import mysql.connector
import os
from pathlib import Path

# Define root path
root_dir = Path(__file__).resolve().parent
data_directory = root_dir / "data"
federal_account_directory = root_dir / "data2"

# Database configuration
db_host = "localhost"
db_user = "sujaya"
db_password = "Password0!"
new_db_name = "prod9"

# mySQL connection
db_connection = mysql.connector.connect(
    host=db_host,
    user=db_user,
    password=db_password,
    ssl_disabled=True
)
cursor = db_connection.cursor()

try:
    cursor.execute(f"CREATE DATABASE {new_db_name}")
    print(f"Database {new_db_name} created successfully.")
except mysql.connector.Error as err:
    print(f"Error creating database {new_db_name}: {err}")

# Use the new database
cursor.execute(f"GRANT ALL PRIVILEGES ON {new_db_name}.* TO 'sujaya'@'localhost';")
cursor.execute(f"USE {new_db_name}")

# Define schema (example schema)
schema = """
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
    Obligation_Amount DECIMAL(20,2) NOT NULL,
    Outlayed_Amount DECIMAL(20,2) NOT NULL,
    Primary_Place TEXT NOT NULL,
    Agency_Identifier VARCHAR(255),
    Prime_Award_ID VARCHAR(255) NOT NULL UNIQUE,
    Object_Class TEXT,
    PRIMARY KEY (Prime_Award_ID)
);

CREATE TABLE Recipient(
    Recipient_Name TEXT,
    Recipient_UEI VARCHAR(255) NOT NULL,
    Prime_Award_ID VARCHAR(255) NOT NULL,
    CONSTRAINT PK_Recipient PRIMARY KEY (Recipient_UEI, Prime_Award_ID)
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

ALTER TABLE Recipient
ADD FOREIGN KEY (Prime_Award_ID) REFERENCES Award(Prime_Award_ID);

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

"""

# Apply schema
try:
    for statement in schema.split(';'):
        if statement.strip():
            cursor.execute(statement)
    print("Schema applied successfully.")
except mysql.connector.Error as err:
    print(f"Error applying schema: {err}")


# Function to insert data with ON DUPLICATE KEY UPDATE
def insert_data(table, data, update_columns):
    placeholders = ', '.join(['%s'] * len(data.columns))
    columns = ', '.join(data.columns)
    update_placeholders = ', '.join([f"{col}=VALUES({col})" for col in update_columns])
    sql = f"INSERT INTO {table} ({columns}) VALUES ({placeholders}) ON DUPLICATE KEY UPDATE {update_placeholders}"
    print(f"Inserting into {table} with SQL: {sql}")  # Debug: Print SQL query
    print(f"Data:\n{data()}")  # Debug: Print first few rows of data
    cursor.executemany(sql, [tuple(row) for row in data.values])
    db_connection.commit()

# Function to process and insert main data files
def process_main_data_file(filename):
    file_path = os.path.join(data_directory, filename)
    
    df = pd.read_csv(file_path, encoding="latin1", skipinitialspace=True, usecols=[
        "object_classes_funding_this_award", "funding_agency_code", "funding_agency_name",
        "recipient_name", "recipient_uei", "federal_accounts_funding_this_award",
        "primary_place_of_performance_state_name", "total_outlayed_amount_for_overall_award",
        "total_dollars_obligated", "award_id_piid",
        "obligated_amount_from_COVID-19_supplementals_for_overall_award",
        "outlayed_amount_from_COVID-19_supplementals_for_overall_award", "program_activities_funding_this_award"
    ]).dropna()

    # Convert amount columns to decimal
    df['total_outlayed_amount_for_overall_award'] = df['total_outlayed_amount_for_overall_award'].astype(float)
    df['total_dollars_obligated'] = df['total_dollars_obligated'].astype(float)
    df['obligated_amount_from_COVID-19_supplementals_for_overall_award'] = df['obligated_amount_from_COVID-19_supplementals_for_overall_award'].astype(float)
    df['outlayed_amount_from_COVID-19_supplementals_for_overall_award'] = df['outlayed_amount_from_COVID-19_supplementals_for_overall_award'].astype(float)

    # Insert into Agency table
    agency_data = df[['funding_agency_code', 'funding_agency_name']].rename(columns={
        'funding_agency_code': 'Agency_Identifier',
        'funding_agency_name': 'Agency_Name'
    }).drop_duplicates()
    insert_data('Agency', agency_data, update_columns=['Agency_Name'])

    
# Function to process and insert federal account data
def process_federal_account_file(filename):
    file_path_fa = os.path.join(federal_account_directory, filename)
    federal_account_df = pd.read_csv(file_path_fa, encoding="latin1", skipinitialspace=True, usecols=[
        "federal_account_symbol", "federal_account_name", "owning_agency_name"
    ])
    
    federal_account_data = federal_account_df.rename(columns={
        "federal_account_symbol": "Main_Account_Code",
        "federal_account_name": "Account_Title",
        "owning_agency_name": "Agency_Name"
    }).drop_duplicates().dropna()
    insert_data('Federal_Account', federal_account_data, update_columns=['Account_Title', 'Agency_Name'])

def process_main_data_file2(filename):
    file_path = os.path.join(data_directory, filename)
    
    df = pd.read_csv(file_path, encoding="latin1", skipinitialspace=True, usecols=[
        'federal_accounts_funding_this_award',"award_id_piid",
    ]).dropna()

    # Insert into Award_Uses table
    award_uses_data = df[['federal_accounts_funding_this_award', 'award_id_piid']].rename(columns={
        'federal_accounts_funding_this_award': 'Main_Account_Code',
        'award_id_piid': 'Prime_Award_ID',
    }).drop_duplicates()
    insert_data('Award_Uses', award_uses_data, update_columns=['Prime_Award_ID'])

# Process each CSV file in the main data directory
'''
for filename in os.listdir(data_directory):
    if filename.endswith(".csv"):
        print(filename)
        process_main_data_file(filename)
'''
# Process each CSV file in the federal account directory
for filename in os.listdir(federal_account_directory):
    if filename.endswith(".csv"):
        print(filename)
        process_federal_account_file(filename)

# Process special award_uses
for filename in os.listdir(federal_account_directory):
    if filename.endswith(".csv"):
        print(filename)
        process_main_data_file2(filename)

cursor.close()
db_connection.close()
print("All data loaded successfully into the database.")
