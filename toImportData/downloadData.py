import pandas as pd
import mysql.connector
import os
from pathlib import Path

# Define root path
root_dir = Path(__file__).resolve().parent
data_directory = root_dir / "data"
federal_account_directory = root_dir / "data2"

# mySQL connection
db_connection = mysql.connector.connect(
    host="localhost",
    user="sujaya",
    password="Password0!",
    database="test5",
    ssl_disabled=True
)
cursor = db_connection.cursor()

# Function to insert
def insert_data(table, data):
    placeholders = ', '.join(['%s'] * len(data.columns))
    columns = ', '.join(data.columns)
    sql = f"INSERT IGNORE INTO {table} ({columns}) VALUES ({placeholders})"
    cursor.executemany(sql, data.values.tolist())
    db_connection.commit()

# Go through each csv file in data folder
for filename in os.listdir(federal_account_directory):
    if filename.endswith(".csv"):
        file_path_fa = os.path.join(federal_account_directory, filename)
        federal_account_df = pd.read_csv(file_path_fa, encoding="latin1", skipinitialspace=True, usecols=[
            "federal_account_symbol", "federal_account_name", "owning_agency_name"
        ])
        
        federal_account_data = federal_account_df.rename(columns={
            "federal_account_symbol": "Main_Account_Code",
            "federal_account_name": "Account_Title",
            "owning_agency_name": "Agency_Name"
        }).drop_duplicates().dropna(subset=['Main_Account_Code'])
        
        insert_data('Federal_Account', federal_account_data)

# Process each CSV file in the main data directory
for filename in os.listdir(data_directory):
    if filename.endswith(".csv"):
        file_path = os.path.join(data_directory, filename)
        
        df = pd.read_csv(file_path, encoding="latin1", skipinitialspace=True, usecols=[
            "object_classes_funding_this_award", "funding_agency_code", "funding_agency_name",
            "recipient_name", "recipient_uei", "federal_accounts_funding_this_award",
            "primary_place_of_performance_state_name", "total_outlayed_amount_for_overall_award",
            "total_dollars_obligated", "award_id_piid",
            "obligated_amount_from_COVID-19_supplementals_for_overall_award",
            "outlayed_amount_from_COVID-19_supplementals_for_overall_award","program_activities_funding_this_award"
        ])

        # Insert into Agency table
        agency_data = df[['funding_agency_code', 'funding_agency_name']].rename(columns={
            'funding_agency_code': 'Agency_Identifier',
            'funding_agency_name': 'Agency_Name'
        }).drop_duplicates().dropna(subset=['Agency_Identifier'])
        insert_data('Agency', agency_data)

        # Insert into Award table
        award_data = df[['total_dollars_obligated', 'total_outlayed_amount_for_overall_award',
                         'primary_place_of_performance_state_name', 'funding_agency_code', 'award_id_piid',
                         'object_classes_funding_this_award']].rename(columns={
            'total_dollars_obligated': 'Obligation_Amount',
            'total_outlayed_amount_for_overall_award': 'Outlayed_Amount',
            'primary_place_of_performance_state_name': 'Primary_Place',
            'funding_agency_code': 'Agency_Identifier',
            'award_id_piid': 'Prime_Award_ID',
            'object_classes_funding_this_award': 'Object_Class'
        }).drop_duplicates().dropna(subset=['Prime_Award_ID'])
        insert_data('Award', award_data)

        # Insert into Recipient table
        recipient_data = df[['recipient_name', 'recipient_uei', 'award_id_piid']].rename(columns={
            'recipient_name': 'Recipient_Name',
            'recipient_uei': 'Recipient_UEI',
            'award_id_piid': 'Prime_Award_ID'
        }).drop_duplicates().dropna(subset=['Recipient_UEI', 'Prime_Award_ID'])
        insert_data('Recipient', recipient_data)

        # Insert into Covid_Related table
        covid_related_data = df[['award_id_piid', 'obligated_amount_from_COVID-19_supplementals_for_overall_award',
                                 'outlayed_amount_from_COVID-19_supplementals_for_overall_award']].rename(columns={
            'award_id_piid': 'Prime_Award_ID',
            'obligated_amount_from_COVID-19_supplementals_for_overall_award': 'Covid_Obligated_Amount',
            'outlayed_amount_from_COVID-19_supplementals_for_overall_award': 'Covid_Outlayed_Amount'
        }).drop_duplicates().dropna(subset=['Prime_Award_ID'])
        insert_data('Covid_Related', covid_related_data)

        # Insert into Non_Covid_Related table
        non_covid_related_data = df[['award_id_piid']].rename(columns={
            'award_id_piid': 'Prime_Award_ID'
        }).drop_duplicates().dropna(subset=['Prime_Award_ID'])
        insert_data('Non_Covid_Related', non_covid_related_data)

        # Insert into Award_Uses table
        award_uses_data = df[['federal_accounts_funding_this_award', 'award_id_piid']].rename(columns={
            'federal_accounts_funding_this_award': 'Main_Account_Code',
            'award_id_piid': 'Prime_Award_ID',
        }).drop_duplicates().dropna(subset=['Main_Account_Code', 'Prime_Award_ID'])
        insert_data('Award_Uses', award_uses_data)

        program_activity_data = df['program_activities_funding_this_award'].str.split(':', expand=True)
        df['Program_Reporting_Key'] = program_activity_data[0].str.strip()
        df['Program_Name'] = program_activity_data[1].str.strip() if program_activity_data.shape[1] > 1 else ''

        # Insert data into Program_Activity table
        program_activity_data = df[['Program_Reporting_Key', 'Program_Name']].rename(columns={
            'Program_Reporting_Key': 'Program_Reporting_Key',
            'Program_Name': 'Program_Name'
        }).drop_duplicates().dropna(subset=['Program_Reporting_Key'])
        insert_data('Program_Activity', program_activity_data)

        # Insert into Provides table
        provides_data = df[['object_classes_funding_this_award', 'award_id_piid']].rename(columns={
            'object_classes_funding_this_award': 'Program_Reporting_Key',
            'award_id_piid': 'Prime_Award_ID'
        }).drop_duplicates().dropna(subset=['Program_Reporting_Key', 'Prime_Award_ID'])
        insert_data('Provides', provides_data)

cursor.close()
print("All data loaded successfully into the database.")
