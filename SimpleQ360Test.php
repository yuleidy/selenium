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
    public static function setupBeforeClass():void
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

    public function testGetReady() {
        //these values here might be coming from a config file or some source other than the code.
        $userid = 'ygonzalez';
        //$pwd = 'letmein';
        $pwd = '1234';

        //$url = 'https://w360-qa-q360dev.solutions360.com';
        $url = 'http://q360webclient.test';

        $driver = self::$driver;
        //1. get the driver to navigate to the login page
        $driver->get($url);
        //1.1 assert about the page title (just cause...).
        $loginPageTitle = $driver->getTitle();
        $this->assertEquals('Sign in | Q360', $loginPageTitle);
    }

    private function login() {
        $driver = self::$driver;
        $pageTitle = $driver->getTitle();
        if ($pageTitle == 'Sign in | Q360') {
            //login
            $userid = 'ygonzalez';
            $pwd = '1234';
            //find the login form
            $form = $driver->findElement(WebDriverBy::name('sign-in'));
            //set the values to the text fields for user and pwd
            $driver->findElement(WebDriverBy::id('userid'))->sendKeys($userid);
            $driver->findElement(WebDriverBy::id('password'))->sendKeys($pwd);
            //submit the form with the given values.
            $form->submit();
            //give the page time to load.
            sleep(3);
            //# set the timeout to 20 seconds, and the time in interval to 1000 ms
            //$driver->wait(10, 1000)->until(WebDriverExpectedCondition::titleIs('Q360'));
        }
        $homePageTitle = $driver->getTitle();
        return ('Q360' == $homePageTitle);
    }

    private function logout() {
        $driver = self::$driver;
        $pageTitle = $driver->getTitle();
        if ($pageTitle == 'Q360') { //means it's logged in, so it's safe to logout.
            $logoutMenu = $driver->findElement(WebDriverBy::xpath("//div[contains(text(), 'Sign Out') and @class='sub_item_text']"));
            $driver->executeScript("arguments[0].click();", array($logoutMenu));
        }
        $homePageTitle = $driver->getTitle();
        return ('Sign in | Q360' == $homePageTitle);
    }

    public function testGetDriverBySamples() {
        $driver = self::$driver;
        //examples live here: https://gist.github.com/aczietlow/7c4834f79a7afd920d8f

        //1. by class name:
        $s1 = $driver->findElement(WebDriverBy::className('legend'))->getText();
        fwrite(STDOUT, 'by class name: '.print_r($s1, TRUE));
        //2. by id:
        $s2 = $driver->findElement(WebDriverBy::id('footer'))->getText();
        fwrite(STDOUT, 'by id: '.print_r($s2, TRUE));
        //3. by tag name:
        $s3 = $driver->findElement(WebDriverBy::tagName('div'))->getText();
        fwrite(STDOUT, 'by tag name: '.print_r($s3, TRUE));
        //4. by css selector
        $s4 = $driver->findElement(WebDriverBy::cssSelector('p.legend'))->getText();
        fwrite(STDOUT, 'by css selector: '.print_r($s4, TRUE));
        //5. by link text
        $s5 = $driver->findElement(WebDriverBy::linkText("I forgot my password"))->getText();
        fwrite(STDOUT, 'by link text: '.print_r($s5, TRUE));
        $this->assertEquals('I forgot my password', $s5);
        //6. by partial link text
        $s6 = $driver->findElement(WebDriverBy::partialLinkText("password"))->getText();
        fwrite(STDOUT, 'by partial link text: '.print_r($s6, TRUE));
        $this->assertEquals('I forgot my password', $s6);
        //7. by name
        $s7 = $driver->findElement(WebDriverBy::name("sign-in"))->getText();
        fwrite(STDOUT, 'by name: '.print_r($s7, TRUE));
        //8. by xpath
        $s8 = $driver->findElement(WebDriverBy::xpath("//*[@id=\"mainContent\"]/form/div/p[2]/button"))->getText();
        fwrite(STDOUT, 'by xpath: '.print_r($s8, TRUE));
        $this->assertEquals('Submit', $s8);

        //$driver->switchTo()->defaultContent();
        //$driver->manage()->window()->maximize();

        if ($this->login()) {
            if ($this->logout()) {
                fwrite(STDOUT, '---------LOGGED OUT OK!--------------');
            }
        }
        else {
            fwrite(STDOUT, '-------------NOT LOGGED IN!-------------');
        }
        sleep(5);
    }

    public function _testMenus() {
        $driver = self::$driver;

        //-----------testing the menus

        //$(document).find(".sub_item_text:contains('General Codes')").parents('.sub_item,.sub_item_selected').trigger('click')
        //Footer.componentGet('main_edit').click()
        //to get the current window: s3.screens.windowsGet()[s3.screens.activeWindowIDGet()]
        //$(s3.screens.windowsGet()[s3.screens.activeWindowIDGet()].dhtmlxWindowCellGet().getFrame().contentWindow.document).find('body').css('background-color', 'red')

        //switch to main window
        $driver->switchTo()->defaultContent();

        //$accountingMenu = $driver->findElement(WebDriverBy::cssSelector($accountingMenuOptionSelector));
        //$text = $driver->findElement(WebDriverBy::cssSelector(".sub_item_text:contains('General Codes')"))->getText();
        //$this->assertEquals('Accounting1', $text);

        //$accountingMenu = $driver->findElement(WebDriverBy::cssSelector(".sub_item_text:contains('Accounting')"));
        $accountingMenu = $driver->findElement(WebDriverBy::cssSelector(".sub_item .sub_item_text:contains('Customer')"));
        $actions = new WebDriverActions($driver);
        $actions->moveToElement($accountingMenu)->perform();
        $element = $driver->findElement(WebDriverBy::cssSelector(".sub_item_text:contains('General Codes')"));

        //fwrite(STDOUT, 'temp path '.print_r($element, TRUE));

        $tagName = $element->getTagName();
        fwrite(STDOUT, 'customer menu tag name: '.print_r($tagName, TRUE));
        $element->click();
        //get text
        $this->assertEquals('Customer', $tagName);
    }
}