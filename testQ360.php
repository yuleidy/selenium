<?php

namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once('vendor/autoload.php');

// start Chrome with 5 second timeout
$host = 'http://localhost:4444/wd/hub'; // this is the default
$capabilities = DesiredCapabilities::chrome();
$driver = RemoteWebDriver::create($host, $capabilities, 5000);

// navigate to 'http://www.seleniumhq.org/'
//$driver->get('https://www.seleniumhq.org/');
$driver->get('https://w360-qa-q360dev.solutions360.com');

// click the link 'About'
/*$link = $driver->findElement(
    WebDriverBy::id('menu_about')
);
$link->click();*/

// wait until the page is loaded
/*$driver->wait()->until(
    WebDriverExpectedCondition::titleContains('About')
);*/

// print the title of the current page
// <title>Sign in | Q360</title>
echo "The title is '" . $driver->getTitle() . "'\n";

// print the URI of the current page
echo "The current URI is '" . $driver->getCurrentURL() . "'\n";

// write 'php' in the search box
$driver->findElement(WebDriverBy::id('userid'))
    ->sendKeys('ygonzalez') // fill the search box
    ->submit(); // submit the whole form

// wait at most 10 seconds until at least one result is shown
/*$driver->wait(10)->until(
    WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
        WebDriverBy::className('gsc-result')
    )
);*/

// close the browser
$driver->quit();