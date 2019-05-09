Create REST APIs in Laravel v5.8

You'll need the following tools to run this APIs.
-	XAMP/LAMP/MAMP
-	PHP7.2 or above
-	mysql 5.7 or above
-	composer

Step 1: Create the project
	You need to create a podcast folder and move all the codes on it. then open the folder in commond propmt and run the below commond
	-	composer install

Step 2: Configure database and make models
	You need to create a database (db_podcast) in your database and run the below command.
	-	php artisan migrate

	If you get an error saying cannot connect to database, even after you updated your .env to not point to database anymore clear the config and try again.

	To clear config in Laravel:
	-	php artisan config:clear

After that, please review the below mentioned link (inlcuded all the mandatory details regarding the podcast functionality) for run all the APIs

https://docs.google.com/spreadsheets/d/1ckxapsNglCJaxz9rzRb6meNc985FVP-vRCLuQ4ZKnYs/edit#gid=0
