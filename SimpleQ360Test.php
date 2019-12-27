<?php

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverPoint;

use PHPUnit\Framework\TestCase;

require_once'vendor/autoload.php';

class SimpleQ360Test extends TestCase
{
    /** @var RemoteWebDriver */
    private static $driver;

    /**
     * Open the Chrome browser just once to be reused on each test.
     */
    public static function setupBeforeClass()
    {
        //fwrite(STDOUT, 'server var '.print_r($_SERVER, TRUE));
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
    public function setUp() {}

    /**
     * After all tests have finished, quit the browser,
     * even if an exception was thrown and/or tests fail
     */
    public static function tearDownAfterClass()
    {
        // Close the browser
        self::$driver->quit();
    }

    /**
     * Test taking a screenshot
     * @depends testPageTitle
     */
    public function _testTakeScreenshot()
    {
        $driver = self::$driver;

        // Navigate to Q360
        $url = 'https://w360-qa-q360dev.solutions360.com';
        $driver->get($url);

        // Take a screenshot
        //fwrite(STDOUT, 'temp path '.print_r(sys_get_temp_dir(), TRUE));
        //$path = sys_get_temp_dir() . "/testScreenshot-ericdraken.png";
        $path = "tests/testScreenshot-login.png";
        $driver->takeScreenshot($path);

        // Verify the screenshot was taken
        $this->assertFileExists($path);

        // Verify the file length is greater than 0
        $this->assertTrue( filesize($path) > 0 );

        // Cleanup
        unlink($path);
    }

    public function testLoginPage() {

        //these values here might be coming from a config file or some source other than the code.
        $userid = 'ygonzalez';
        $pwd = 'letmein';

        $url = 'https://w360-qa-q360dev.solutions360.com';

        $driver = self::$driver;
        //1. get the driver to navigate to the login page
        $driver->get($url);
        //1.1 assert about the page title (just cause...).
        $loginPageTitle = $driver->getTitle();
        $this->assertEquals('Sign in | Q360', $loginPageTitle);
        //2. find the login form
        $form = $driver->findElement(WebDriverBy::name('sign-in'));
        //3. set the values to the text fields for user and pwd
        $driver->findElement(WebDriverBy::id('userid'))->sendKeys($userid);
        $driver->findElement(WebDriverBy::id('password123'))->sendKeys($pwd);
        //4. submit the form with the given values.
        $form->submit();
        //5. give the page time to load.
        sleep(2);
        //5. Then add some assertions
        $homePageTitle = $driver->getTitle();
        $this->assertEquals('Q360123', $homePageTitle);

        sleep(3);
    }
}