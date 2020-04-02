<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverPoint;

use PHPUnit\Framework\TestCase;

require_once'../vendor/autoload.php';
require_once '../classes/q360seleniumhelper.php';

class QuoteFinanceTest extends TestCase
{
    /** @var RemoteWebDriver */
    private static $driver;
    /** @var Q360SeleniumHelper */
    private static $helper;

    /**
     * Open the Chrome browser just once to be reused on each test.
     */
    public static function setupBeforeClass():void
    {
        self::$helper = new Q360SeleniumHelper();
        $host = self::$helper->getHost();
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

        // Set size
        $driver->manage()->window()->setPosition(new WebDriverPoint(30,30));
        $driver->manage()->window()->setSize(new WebDriverDimension(1280,800));

        self::$driver = $driver;
    }

    /**
     * Before each test the same browser instance will be reused,
     * so clear cookies, local storage, etc., or open a new incognito tab
     */
    public function setUp():void {}

    /**
     * After all tests have finished, quit the browser,
     * even if an exception was thrown and/or tests fail
     */
    public static function tearDownAfterClass():void
    {
        // Close the browser
        self::$driver->quit();
    }

    public function testInit() {
        $url = self::$helper->getBrowserUrl();
        $driver = self::$driver;
        //1. get the driver to navigate to the login page
        $driver->get($url);
        //2. assert about the page title (just cause...).
        $loginPageTitle = $driver->getTitle();
        $this->assertEquals(self::$helper->getStringNames('loggedOUTPageTitle'), $loginPageTitle);
        //3. login
        self::$helper->login($driver);
        $loginPageTitle2 = $driver->getTitle();
        $this->assertEquals(self::$helper->getStringNames('loggedINPageTitle'), $loginPageTitle2);
    }

    public function testQuoteFinanceOptions() {
        $driver = self::$driver;
        $quoteNo = self::$helper->getDBValues('quoteNo');
        if (!self::$helper->isLoggedIn($driver)) self::$helper->login($driver);

        $driver->switchTo()->defaultContent(); //just in case it went to another frame or so

        //type the QuoteNo on the quick search 201012
        $driver->findElement(WebDriverBy::id("viewport-quicksearch"))->sendKeys($quoteNo);
        $driver->findElement(WebDriverBy::id("viewport-quicksearch-btn"))->click();
        sleep(self::$helper->getWaitingTimes('m'));

        //first I have to select frame #3
        $my_frame = $driver->findElement(WebDriverBy::xpath('//*[@id="view-port"]/div[2]/div[3]/div/iframe'));
        $driver->switchTo()->frame($my_frame);

        //extended menu option -> Get Finance Options
        $extMenu = $driver->findElement(WebDriverBy::cssSelector("div[data-componentid='main_extendedmenu']"));
        $extMenu->click();
        $driver->executeScript("arguments[0].click();", array($extMenu));
        sleep(self::$helper->getWaitingTimes('l'));

        $financeOptionMenu = $driver->findElement(WebDriverBy::xpath("//span[contains(text(), 'Get Finance Options')]"));
        $financeOptionMenu->click();
        sleep(self::$helper->getWaitingTimes('s'));

        //the modal opens up!
        $getPaymentButton = $driver->findElement(WebDriverBy::className("getLeasePaymentsButton"));
        $getPaymentButton->click();

        //wait for the payments to load! and check there are results!
        sleep(self::$helper->getWaitingTimes('m'));
        $optionOne = $driver->findElement(WebDriverBy::cssSelector(".s360W360QuoteFinancePaymentOptionsListItem:nth-child(1)"));
        $optionOne->click();

        //save the selected option
        $savePaymentButton = $driver->findElement(WebDriverBy::className("addSelectedPaymentOption"));
        $savePaymentButton->click();
        sleep(self::$helper->getWaitingTimes('m'));

        //check success message
        $message = $driver->findElement(WebDriverBy::className("successMessage"))->getText();
        fwrite(STDOUT, '---message after saving: ' . print_r($message, TRUE) . '-------');
        $this->assertContains("success", $message);

        //final wait for me to see results
        sleep(self::$helper->getWaitingTimes('s'));
    }
}