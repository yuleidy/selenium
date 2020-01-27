<?php

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverPoint;

use PHPUnit\Framework\TestCase;

require_once '../vendor/autoload.php';

class SimpleQ360Test extends TestCase
{
    /** @var RemoteWebDriver */
    private static $driver;

    /**
     * Open the Chrome browser just once to be reused on each test.
     */
    public static function setupBeforeClass():void
    {
        $host = 'http://localhost:4444/wd/hub'; // this is the default port
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

    public function testGetReady() {
        //$url = 'https://w360-qa-q360dev.solutions360.com';
        $url = 'http://q360webclient.test';

        $driver = self::$driver;
        //1. get the driver to navigate to the login page
        $driver->get($url);
        //1.1 assert about the page title (just cause...)
        $loginPageTitle = $driver->getTitle();
        $this->assertEquals('Sign in | Q360', $loginPageTitle);
    }

    public function testGetDriverBySamples() {
        $driver = self::$driver;
        //examples live here: https://gist.github.com/aczietlow/7c4834f79a7afd920d8f

        $helper = new Q360SeleniumHelper();
        $printOut = false;

        //this happens in the login page, so we need to logout first
        if ($helper->isLoggedIn($driver)) $helper->logout($driver);

        //1. by class name:
        $s1 = $driver->findElement(WebDriverBy::className('legend'))->getText();
        if ($printOut) fwrite(STDOUT, '---by class name: ' . print_r($s1, TRUE));

        //2. by id:
        $s2 = $driver->findElement(WebDriverBy::id('footer'))->getText();
        if ($printOut) fwrite(STDOUT, '---by id: ' . print_r($s2, TRUE));

        //3. by tag name:
        $s3 = $driver->findElement(WebDriverBy::tagName('div'))->getText();
        if ($printOut) fwrite(STDOUT, '---by tag name: ' . print_r($s3, TRUE));

        //4. by css selector
        $s4 = $driver->findElement(WebDriverBy::cssSelector('p.legend'))->getText();
        if ($printOut) fwrite(STDOUT, '---by css selector: ' . print_r($s4, TRUE));

        //5. by link text
        $s5 = $driver->findElement(WebDriverBy::linkText("I forgot my password"))->getText();
        if ($printOut) fwrite(STDOUT, '---by link text: ' . print_r($s5, TRUE));
        $this->assertEquals('I forgot my password', $s5);

        //6. by partial link text
        $s6 = $driver->findElement(WebDriverBy::partialLinkText("password"))->getText();
        if ($printOut) fwrite(STDOUT, '---by partial link text: ' . print_r($s6, TRUE));
        $this->assertEquals('I forgot my password', $s6);

        //7. by name
        $s7 = $driver->findElement(WebDriverBy::name("sign-in"))->getText();
        if ($printOut) fwrite(STDOUT, '---by name: ' . print_r($s7, TRUE));

        //8. by xpath
        $s8 = $driver->findElement(WebDriverBy::xpath("//*[@id=\"mainContent\"]/form/div/p[2]/button"))->getText();
        if ($printOut) fwrite(STDOUT, '---by xpath: ' . print_r($s8, TRUE));
        $this->assertEquals('Submit', $s8);

        //$driver->switchTo()->defaultContent();
        //$driver->manage()->window()->maximize();
    }

    public function _testDealingWithFrames() {
        $driver = self::$driver;

        $my_frame = $driver->findElement(WebDriverBy::id('my_frame'));
        $driver->switchTo()->frame($my_frame);

        $driver->switchTo()->defaultContent();
    }

    public function _testMenus() {
        $driver = self::$driver;

        //-----------testing the menus

        //switch to main window
        $driver->switchTo()->defaultContent();

        $accountingMenu = $driver->findElement(WebDriverBy::cssSelector(".sub_item .sub_item_text:contains('Customer')"));
        $actions = new WebDriverActions($driver);
        $actions->moveToElement($accountingMenu)->perform();
        $element = $driver->findElement(WebDriverBy::cssSelector(".sub_item_text:contains('General Codes')"));

        $tagName = $element->getTagName();
        fwrite(STDOUT, 'customer menu tag name: '.print_r($tagName, TRUE));
        $element->click();
        //get text
        $this->assertEquals('Customer', $tagName);
    }
}