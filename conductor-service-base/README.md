Ephect Service Base
===================
A base service to extend into more complex services. Designed to communicate with the Ephect-SLC.

## To extend
* cd into base directory for all Eardish repos
* `git clone git@github.com:eardish/ephect-service-base.git <name of new service>`
* `cd <name of new service>`
* remove old reference to git and initialize a new git repo
* `rm -rf .git`
* `git init`
* `git remote add origin git@github.com:eardish/<name of new service>.git`
* `composer install`
* open repo in PHPStorm

##### update lib
* change directory name from `lib/Eardish` from EchoService to <NameService>
* change all name spaces from `Eardish\EchoService` to `Eardish\<NameService>` _including use statements_
* change Class name and file name from EchoService.php to NameService.php (inside <name of service> directory)
* make sure to look in server.php and update the use statement at the top
* change ServiceName that gets injected as parameter in server.php when making the ServiceAPI 
* in ServiceAPI.php change class in parameter EchoService to <NameService> and update doc block

##### update tests
* in the `tests/` folder, change all namespaces, use statements and reference to EchoService to <NameService>
* change Directory and Fil name too
* change class path for mock
* update README.md to display correct name of service and a brief description

##### push to Github
* `git add .`
* `git commit -m "first commit"`
* `git push origin master`

========
