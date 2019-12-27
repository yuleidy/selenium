# selenium
 Selenium tests with php
 
 Steps:
 1. Install Java (follow steps here: http://java.boot.by/scbcd5-guide/apas02.html)
 2. Then follow up the steps from here: https://ericdraken.com/automated-web-testing-php-phpunit-selenium/
 3. Start the selenium server: java -Dwebdriver.chrome.driver=chromedriver.exe -jar selenium-server-standalone-3.141.59.jar
 4. It's important to also have PHP 7.2 or greater running. 
 This will help with all the composer dependencies we are trying to install. 
 Most of them need PHP >= 7.1
 
 Yuleidy's notes: 
 When starting the selenium server with the chrome driver,
 it is important to set the driver name to be the .exe file (in Windows at least).
 
 You might need to install pear at some point (https://pear.php.net/manual/en/installation.getting.php)

