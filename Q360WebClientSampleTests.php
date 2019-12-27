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


class Q360WebClientSampleTests
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
     * Verify the page title
     */
    public function _testPageTitle()
    {
        // Navigate to QA web server
        self::$driver->get('https://w360-qa-q360dev.solutions360.com');

        // Verify the title contains my name
        $this->assertContains("Q360", self::$driver->getTitle());
    }

    /**
     * Test taking a screenshot
     * @depends testPageTitle
     */
    public function _testScreenshot()
    {
        $driver = self::$driver;

        // Navigate to Q360
        $driver->get('https://w360-qa-q360dev.solutions360.com');

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

    public static function validInputsProvider() {

        $credentials = array(
            array(
                'userid' => 'ygonzalez',
                'password' => 'letmein',
            ),
            /*array(
                'userid' => 'bmudry',
                'password' => 'letmein',
            ),*/
        );
        return $credentials;
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
        $driver->findElement(WebDriverBy::id('password'))->sendKeys($pwd);
        //4. submit the form with the given values.
        $form->submit();
        //5. give the page time to load.
        sleep(2);
        //5. Then add some assertions
        $homePageTitle = $driver->getTitle();
        $this->assertEquals('Q360', $homePageTitle);

        sleep(3);
    }

    public function _testMenus() {
        $driver = self::$driver;

        //-----------testing the menus
        //$accountingMenuOptionXPath = '//*[@id="dhxId_7o8fOBoZwv2h_5265350"]';
        //$accountingMenuOptionXPath = '//*[@id="dhxId_2T7EjZ5M57Rk_5265350"]';
        $accountingMenuOptionXPath = '/html/body/div[51]/div[1]/div[3]/div[1]/div[5]';
        $accountingMenuOptionSelector = '#dhxId_7o8fOBoZwv2h_5265350';
        $xPathCustomerMenuOption = '//*[@id="dhxId_7o8fOBoZwv2h_5265351"]/td[2]/div';

        //switch to main window
        $driver->switchTo()->defaultContent();

        //$accountingMenu = $driver->findElement(WebDriverBy::cssSelector($accountingMenuOptionSelector));
        $text = $driver->findElement(WebDriverBy::xpath("//*[contains(@id,'5265350')]"))->getText();
        //$this->assertEquals('Accounting1', $text);

        $accountingMenu = $driver->findElement(WebDriverBy::xpath('//*[contains(@id,"5265350")]'));
        $actions = new WebDriverActions($driver);
        $actions->moveToElement($accountingMenu)->perform();
        $element = $driver->findElement(WebDriverBy::xpath('//*[contains(@id,"5265351")]'));

        fwrite(STDOUT, 'temp path '.print_r($element, TRUE));

        $tagName = $element->getTagName();
        $element->click();
        //get text
        $this->assertEquals('Accounting2', $tagName);
    }

    public function _testCreateServiceCall() {

        $driver = self::$driver;
        //find the menu options and click it

        //$xPathAccountingMenuOption = '//*[@id="dhxId_7o8fOBoZwv2h_5265350"]/div';
        //$xPathAccountingMenuOption = '//*[@id="dhxId_7o8fOBoZwv2h_5265350"]';
        $xPathAccountingMenuOption = '#dhxId_7o8fOBoZwv2h_5265350';
        $xPathCustomerMenuOption = '//*[@id="dhxId_7o8fOBoZwv2h_5265351"]/td[2]/div';

        $accountingMenu = $driver->findElement(WebDriverBy::cssSelector($xPathAccountingMenuOption));
        $actions = new WebDriverActions($driver);
        $actions->moveToElement($accountingMenu)->perform();
        $driver->findElement(WebDriverBy::xpath($xPathCustomerMenuOption))->click();
    }

    public function _testIFrames() {
        $driver = self::$driver;
        //#view-port > div > div.dhx_cell_wins > div > iframe
        $xPath = '#view-port > div > div.dhx_cell_wins > div > iframe';
        $iFrame = $driver->findElement(WebDriverBy::cssSelector($xPath));
        $driver->switchTo()->frame($iFrame);//WebDriverElement('the id or the name of the frame"some-frame" # name or id'));

        //switch to main window
        $driver->switchTo()->defaultContent();
    }
}