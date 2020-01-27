<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverPoint;

use PHPUnit\Framework\TestCase;

require_once '../vendor/autoload.php';

class SimpleSeleniumTest extends TestCase
{
    /** @var RemoteWebDriver */
    private static $driver;

    /**
     * Open the Chrome browser just once to be reused on each test.
     */
    public static function setupBeforeClass() : void
    {
        //$host = 'http://'.$_SERVER['REMOTE_ADDR'].':4444/wd/hub'; // this is the default port
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
    public function setUp() : void  {}

    /**
     * After all tests have finished, quit the browser,
     * even if an exception was thrown and/or tests fail
     */
    public static function tearDownAfterClass() : void
    {
        // Close the browser
        self::$driver->quit();
    }

    /**
     * Verify the page title
     */
    public function testPageTitle()
    {
        // Navigate to ED
        self::$driver->get('https://ericdraken.com');

        // Verify the title contains my name
        $this->assertContains("Eric Draken", self::$driver->getTitle());
    }

    /**
     * Test clicking "About Eric" and taking a screenshot
     * @depends testPageTitle
     */
    public function testScreenshot()
    {
        $driver = self::$driver;

        // Navigate to ED
        $driver->get('https://ericdraken.com');

        // Click the link 'About Eric'
        $link = $driver->findElement(
            WebDriverBy::linkText('About Eric')
        );
        $link->click();

        // Wait until the page is loaded
        $driver->wait(15)->until(
            WebDriverExpectedCondition::titleContains('Eric Draken')
        );

        // Take a screenshot
        fwrite(STDOUT, 'temp path '.print_r(sys_get_temp_dir(), TRUE));
        $path = sys_get_temp_dir() . "/testScreenshot-ericdraken.png";
        $driver->takeScreenshot($path);

        // Verify the screenshot was taken
        $this->assertFileExists($path);

        // Verify the file length is greater than 0
        $this->assertTrue( filesize($path) > 0 );

        // Cleanup
        unlink($path);
    }
}