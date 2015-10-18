[![Build Status](https://magnum.travis-ci.com/eardish/ephect-api.svg?token=QpoLNHjVKRsjoExsauxP&branch=dev)](https://magnum.travis-ci.com/eardish/ephect-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eardish/db-orm-service/badges/quality-score.png?b=dev&s=9f3ea07c284fe3affb1eb28f9ffd8b302e33fd63)](https://scrutinizer-ci.com/g/eardish/db-orm-service/?branch=dev)
[![Code Coverage](https://scrutinizer-ci.com/g/eardish/db-orm-service/badges/coverage.png?b=dev&s=86e62c9ccb81eeb003e8ba3d345c3368b6841de4)](https://scrutinizer-ci.com/g/eardish/db-orm-service/?branch=dev)
[![Build Status](https://scrutinizer-ci.com/g/eardish/db-orm-service/badges/build.png?b=dev&s=80caa852c010c3c5e1c8e7dd4638ac35914f3c84)](https://scrutinizer-ci.com/g/eardish/db-orm-service/build-status/dev)

Database ORM Service
====================
The db-orm-service builds and sends queries to the appropriate database for data requested from the services. Currently three different databases are being utilized, postgreSQL, neo4j and elasticsearch. Which of the three databases is being used when depends on the status of the request as well as the type of data being requested.

All writing requests (create, update, destroy) will autmatically be sent to postgreSQL. Where a decision will be made to use which server is on read requests.

Requests coming in from the services will have priority as a field set in the DTO. Any request that comes in needing real-time data will have a priority level that the db-orm-service will automatically send to postgreSQL. Other requests will be routed to their "default" database--for instance social requests will go to neo4j and heavy text requests will got to elasticsearch.

The Doctrine 2.0 library is currently being used to populate the tables from model files into postgreSQL.


Set Up
====================
Follow Install Guide in Wiki to install databases and db libraries  
https://github.com/eardish/db-orm-service/wiki/DB-install-guide-(Ubuntu-14.04)
