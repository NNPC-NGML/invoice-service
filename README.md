# docker-boilerplate

**This is a quick setup for containerising your project. It helps your install Mysql 5.7.22, phpmyadmin.**

# Docker Compose Config

1. **If you would use Mysql, update the Mysql Configuration;
   MYSQL_DATABASE: your_db
   MYSQL_USER: your_username
   MYSQL_PASSWORD: your_password
   MYSQL_ROOT_PASSWORD: your_root_password**
2. **Change the service_name and the service_name_queue to your preferred name in the docker-compose.yaml file;**
3. **All docker compose service should have different ports so ensure to change the port in the docker-compose file, specifically on line (13 & 38)**

**To use the Make command , go to the make file and make change to CONTAINER_PHP="your primary container" on line 4, the name should be the name you chose in step 2 above**

# Make Commands

**1. To Start App
make start**

**2. to stop App
make stop**

**3. To rebuild and start the App
make fresh**

**4. To Destroy All contaner
make destroy**

**5. To restart container
make restart**

**7. to ssh into the parent container
make ssh**

**8. To run Migration on the parent container
make migrate**

**9. To run a fresh migration
make migrate-fresh**

**10. To run test
make tests**

**11. To run lint check
make lint**

**12. To fix lint issues
make lint-fix**

**Cheers!!!!**

# Test Case and Microservice Middleware

A sample test case MiddlewareSampleTest has be added, NOTE: the authentication mock call should always be imported at the top of the test case. The Skillz Packageshas already been installed and configured .**NOTE we used httponly cookie**

### Installation of the package (package is already installed)

-   to install the package run **_composer require skillz-systems/user-service dev-main_**
-   then add the package to your autoload. ie **composer.json** under **"autoload"** then **psr-4** \***_"Skillz\\": "vendor/skillz-systems/user-service/src/"_**
-   then run **_composer dump_**
-   then **TESTING PURPOSE** go to **phpunit.xml** and add `<env name="USERS_MS" value="your ip address:docker port"/> `
-   Also, add **USER_MS** to your env file

### Ckecklist to run microservice

1. Add your current IP address to the ENV_MS
2. Add your current IP address to **phpunit.xml** for all test cases requiring auth to run
3. Use the **"scope.user"** middleware

### To Review Skillz package

To review the skillz package go the following files

-   Go to namespace **\*App\Http\Middleware** \*and open **UsersMiddleware.php**
-   In config folder open **skillz.php**
-   Go to **\*project-boilerplate\vendor\skillz-systems\user-service\src\*\*** see the package
-   Go to **composer.json** under **"autoload"** then **psr-4\***"Skillz\\": "vendor/skillz-systems/user-service/src/"\*

### Github Action Modification

its important for the github action to be modified to point to the correct service for deployment .

-   below are things to be changed or modified

-   search for the keyword "service_name_placeholder" and replace it with the correct service name refer to service name list below .

### Service Name List

-   automator
-   communication
-   customer
-   document
-   formbuilder
-   notification
-   processflow
-   users

### Queue Name List

-   automator_queue
-   communication_queue
-   customer_queue
-   document_queue
-   formbuilder_queue
-   notification_queue
-   processflow_queue
-   users_queue
