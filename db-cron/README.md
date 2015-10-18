Database Cron Service
====================
Database Cron propagates data from PostgreSQL into Neo4j and ElasticSearch. PostgreSQL is the only database kept up to date in real time.
Any writes to the db are directed to postgres. The cron then applies the changes to the other databases.

Set Up
====================
Create the db in pg admin "eardish-cron" with user "eardish".  
To set up the db table, run `php CreateCronSchema.php` from the root folder of the db-cron repo.

How it works
====================
Any data that gets inserted, updated, or deleted from PostgreSQL are added to a database (cron_queue) after the transactions are completed in the db-orm.
Items inserted in the database are given a status of 0.
The CronManager runs the scrip that takes first entry from the db with a status of 0 and sets the status to -1 (locked or in progress). 

Once it successfully propagates that entry's changes into Neo4j and Elastic search, it sets the status to 1 (completed) and then takes the next item from the db.
If the propagation fails, the status is reset to 0 to try again (we may have to have some other status for this to avoid infinite loops).
