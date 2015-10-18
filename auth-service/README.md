#Auth Service

Travis [![Build Status](https://magnum.travis-ci.com/eardish/user-service.svg?token=N3caz4AhgjzZiCyuyVzo&branch=dev)](https://magnum.travis-ci.com/eardish/user-service)
Coveralls [unavailable]
Scrutinizer [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eardish/ephect-auth-service/badges/quality-score.png?b=dev&s=d8b7dab1a3c4fed001f7802ad9f1d2ef93d2ee2c)](https://scrutinizer-ci.com/g/eardish/ephect-auth-service/?branch=dev) [![Build Status](https://scrutinizer-ci.com/g/eardish/ephect-auth-service/badges/build.png?b=dev&s=9f099413f8826b4be1b2c5505b6e18d17ebdbbab)](https://scrutinizer-ci.com/g/eardish/ephect-auth-service/build-status/dev)

===================

### Purpose

This is the basic authentication piece of the architecture. It checks to see whether user x or y is allowed to do whatever operation it is they are trying to carry out. It communicates with the database directly, bypassing the service layer controller piece altogether.

### Use Cases

1. Verify user is saved in database. If so, authenticate them
2. Retrieve ID of authenticated user and use ID to keep track of the user
3. Updates password to most current hashing algorithm on authentication

### Current State

1. Can connect to database service
2. Can update password to current hashing standard
3. Can retrieve ID to be used for tracking user
