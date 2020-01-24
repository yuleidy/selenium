<?php

return array(
    //----selenium driver config
    'selenium' => array(
        'seleniumDriverHost' => 'http://localhost:4444/wd/hub', // this is the default port
    ),
    //----waiting times
    'waitingTimes' => array(
        'xs'  => 1, //extra small
        's'  => 3, //small
        'm' => 5, //medium
        'l'  => 10, //large
        'xl' => 20, //extra large
        'v' => 30, //venti
    ),
    //----config options related to the server to be tested (login info)
    'credentials' => array(
        'q360ServerUrl' => 'http://q360webclient.test',
        'q360Login' => 'ygonzalez',
        'q360Pwd' => '1234',
    ),
    //----config options related to the tests
    'stringNames' => array(
        'loggedINPageTitle' => 'Q360',
        'loggedOUTPageTitle' => 'Sign in | Q360',
    ),
    //----menu options names
    'menuNames' => array(
        'financeOptions' => 'Get Finance Options',
    ),
    //----fixed values from the DB, like ids, names, etc.
    'DBValues' => array(
        'customerNo' => 'YTI001',
        'quoteNo' => '201012',
    ),
    //----extra options, free to add here at will
    'extraOptions' => array(
        'closeWarningModal' => true,
    ),
);