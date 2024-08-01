# CS-338-Project
Repo for CS 338 group 15 project

Sample_Database_Creation.sql contains all the code required to generate the database schema and a sample dataset, to be run while working in an empty database.
test-sample.sql contains the sample queries to be run on the generated sample dataset, with test-sample.txt containing the outputs for the sample queries. The Covid-19 award filter calls a predefined database procedure that is also included in the file. Outlier locations was modified to only show the first and last records as the sample dataset was too small otherwise.

The database application can be run using php, by running a localhost server on the php folder and accessing it using any web browser http://localhost:8000.
Currently there are 6 basic features: 1. Award ID Finder (searching based on amount range and covid-relatedness). 2. Award search by ID. 3. Look up program activities by recipient. 4. Update recipient information. 5. Look up average outlayed and obligated amounts by federal account. 6. Look up agency performance (# of awards, total outlayed and total obligated amounts.
There are also 5 fancy features: 1. Visual data chart of top performing agencies. 2. Boxplots comparing two agencies by their covid and non-covid related expenditures. 3. Thematic map showing the distrbution of federal funds across states. 4. Multi-attribute filter search. 5. Look up award based on completion rate, agency, and program.

Production_Database_Creation.sql contains all the code to to set up a production database in preperation to populate it with production data.

Here is procedure to load the data into production database:
You first need to download the Award Data Archive from their official website, which contains the bulk of the data we need. 
https://www.usaspending.gov/download_center/award_data_archive

We decided on the 2022 data, you can use other data as you please. So the first step would be to download the “Full” zip. You would then unzip it, which should consider a handful of very large csv files. Place these csv files inside the “data” folder inside the "toImportData" folder.

Then, you will also need the Custom Account Data, which is on the same website.
https://www.usaspending.gov/download_center/custom_account_data

Select “all” for Budget Functions, select the same fiscal year you selected earlier and click download. This should generate a link for you to download the data pretty quickly. Place the csv file inside the “data2” inside the "toImportData" folder.

Then, install python on your computer and then run the script in “downloadData.py” inside the "toImportData" folder . If the files were placed in the correct folders, the script should add all the needed production data needed into the mysql database that was set up using the given schema. Make sure to edit the database connections as needed.

The script is a simple script written in python and mostly utilizes pandas to add the csv data you just downloaded into mysql. Due to the size of the dataset, this script takes a little bit to go through all the csvs, expected runtime is about an hour. The script just defines the directory of where the csv files are, then establishes a connection to the mysql database. We then define a function to insert data into the database and have a for loop to loop through each csv, working through each mysql table, then working through each given csv file.
