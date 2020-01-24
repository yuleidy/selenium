<?php

use Facebook\WebDriver\WebDriverBy;

class Q360SeleniumHelper
{
    private $configs;

    public function __construct() {
        $this->configs = include('../config/config.php');
    }

    #region Getters
    public function getConfigs() {
        return $this->configs;
    }

    public function getHost() {
        return $this->configs['selenium']['seleniumDriverHost'];
    }

    public function getBrowserUrl() {
        return $this->configs['credentials']['q360ServerUrl'];
    }

    public function getUserName() {
        return $this->configs['credentials']['q360Login'];
    }

    public function getUserPwd() {
        return $this->configs['credentials']['q360Pwd'];
    }

    public function getStringNames($optionName = '') {
        if (!empty($optionName) && array_key_exists($optionName, $this->configs['stringNames'])) {
            return $this->configs['stringNames'][$optionName];
        }
        return $this->configs['stringNames'];
    }

    public function getMenuNames($optionName = '') {
        if (!empty($optionName) && array_key_exists($optionName, $this->configs['menuNames'])) {
            return $this->configs['menuNames'][$optionName];
        }
        return $this->configs['menuNames'];
    }

    public function getDBValues($optionName = '') {
        if (!empty($optionName) && array_key_exists($optionName, $this->configs['DBValues'])) {
            return $this->configs['DBValues'][$optionName];
        }
        return $this->configs['DBValues'];
    }

    public function getExtraOptions($optionName = '') {
        if (!empty($optionName) && array_key_exists($optionName, $this->configs['extraOptions'])) {
            return $this->configs['extraOptions'][$optionName];
        }
        return $this->configs['extraOptions'];
    }

    public function getWaitingTimes($optionName = '') {
        if (!empty($optionName) && array_key_exists($optionName, $this->configs['waitingTimes'])) {
            return $this->configs['waitingTimes'][$optionName];
        }
        return $this->configs['waitingTimes'];
    }
    #endregion

    #region Login functions
    public function isLoggedIn($driver) {
        return ($driver->getTitle() == $this->getStringNames('loggedINPageTitle'));
    }

    public function login($driver) {
        if (!$this->isLoggedIn($driver)) { //means it's logged out so we need to login!
            //set the values to the text fields for user and pwd
            $driver->findElement(WebDriverBy::id('userid'))->sendKeys($this->getUserName());
            $driver->findElement(WebDriverBy::id('password'))->sendKeys($this->getUserPwd());
            //find the login form
            $driver->findElement(WebDriverBy::name('sign-in'))->submit();
            sleep($this->getWaitingTimes('s')); //give the page time to load.
            if ($this->getExtraOptions('closeWarningModal')) {
                //close up the modal with warnings that opens up after login!
                $driver->findElement(WebDriverBy::className("modal-close"))->click();
                //$driver->findElement(WebDriverBy::cssSelector("a.modal-close.fa-icon.fa-close"))->click();
            }
        }
    }

    public function logout($driver) {
        if ($this->isLoggedIn($driver)) { //means it's logged in, so we need to logout.
            //find the logout menu option
            $logoutMenu = $driver->findElement(WebDriverBy::xpath("//div[contains(text(), 'Sign Out') and @class='sub_item_text']"));
            //click the logout
            $driver->executeScript("arguments[0].click();", array($logoutMenu));
        }
    }
    #endregion
}