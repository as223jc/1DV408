<?php
//TESTING OF ACCOUNT SYSTEM
class Tests
{
    private static $testResults;
    function __construct(){
        //CONNECT TO DATABASE
        $mysqli = new mysqli("localhost", "admin", "", "projekt");
        $db = new AccountDAL($mysqli);
        $usr = new Users($mysqli);

        //CREATE THE ACCOUNT TABLE if not exists
        self::$testResults["Create Account Table"] = $db->createAccountTable();

        //CREATE THE CHARACTER TABLE if not exists
        self::$testResults["Create Character Table"] = $db->createCharacterTable();

        //CREATE USER with correct info
       self::$testResults["Create Account 1"] = $db->createAccount([
           "username" => "axel",
            "password" => "testar",
            "email" => "test@abc.com"
       ]);

        //DELETE ACCOUNT 1 with correct info
        self::$testResults["Delete Account"] = $db->deleteAccount([
            "username" => "axel"
        ]);

        //CREATE NEW ACCOUNT with correct info
        self::$testResults["Create Account 2"] = $db->createAccount([
            "username" => "lusse",
            "password" => "osten",
            "email" => "email@asd.com"
        ]);

        //DELETE ACCOUNT 2 with error in username
        self::$testResults["Delete Account 2"] = $db->deleteAccount([
            "username" => "lusdse"
        ]);

        //CREATE CHARACTER ON ACCOUNT 2 with correct info
        self::$testResults["Create Character"] = $db->createCharacter([
            "accountId" => "2",
            "playerName" => "Axel",
            "sex" => "0",
            "position" => "1233,233"
        ]);

        //CHECK ACCOUNT EXISTS with existing username
        self::$testResults["Check If Account Exists"] = $db->accountExists([
            "username" => "lusse"
        ]);

        //CHECK ACCOUNT EXISTS with non-existing username
        self::$testResults["Check If Account Exists 2"] = $db->accountExists([
            "username" => "peter"
        ]);

        //CHECK ACCOUNT EXISTS with fake username AND password
        self::$testResults["Check If AccountPassword Exists"] = $db->accountExists([
            "username" => "lusse"
        ], "snor");
        //CHECK ACCOUNT EXISTS with existing username AND password
        self::$testResults["Check If AccountPassword Exists 2"] = $db->accountExists([
            "username" => "johnas"
        ], 'rabarber');

        //CHECK CHARACTER EXISTS with existing character
        self::$testResults["Check If Character Exists"] = $db->characterExists([
            "playername"=>"Axel"
        ]);

        //CHECK CHARACTER EXISTS with existing character
        self::$testResults["Check If Character Exists with wrong name"] = $db->characterExists([
            "playername"=>"asdsdfsdf"
        ]);

        //DELETE EXISTING PLAYER
        self::$testResults["Delete Existing Player"] = $db->deleteCharacter([
            "playername"=>"Axel"
        ]);

        //TEST GOOD COOKIE AUTHENTICATION
        self::$testResults["Test Good Cookie Auth"] = $db->authLogin(["username"=>"pelle", "loginid"=>"1239523"]);

        //TEST BAD COOKIE AUTHENTICATION
        self::$testResults["Test Bad Cookie Auth"] = $db->authLogin(["username"=>"doesntexist", "loginid"=>"1234567"]);

        ///////////////////////////
        /////////Code validation//
        /////////////////////////

        //CHECK BAD USERNAME VALIDATION
        self::$testResults["Test Bad Username"] = $usr->checkUserName("22345");

        //CHECK BAD USERNAME VALIDATION 2
        self::$testResults["Test Bad Username 2"] = $usr->checkUserName("Hejsan&");

        //CHECK GOOD USERNAME VALIDATION
        self::$testResults["Test Good Username"] = $usr->checkUserName("Apan");

        //TEST BAD PASSWORD
        self::$testResults["Test Bad Password"] = $usr->checkPassword("");

        //TEST BAD PASSWORD 2
        self::$testResults["Test Bad Password 2"] = $usr->checkPassword("asdasd");

        //TEST BAD MATCHING PASSWORD
        self::$testResults["Test Bad Matching Password"] = $usr->checkPassword("apelsinmarmelad", "asd");

        //TEST GOOD MATCHING PASSWORD
        self::$testResults["Test Good Matching Password"] = $usr->checkPassword("apskaftet", "apskaftet");

        //TEST GOOD PASSWORD
        self::$testResults["Test Good Password"] = $usr->checkPassword("testartestar");

        //TEST BAD EMAIL
        self::$testResults["Test Bad Email"] = $usr->checkEmail("aasd@asdw");

        //TEST GOOD EMAIL
        self::$testResults["Test Good Email"] = $usr->checkEmail("asdsad@asdasd.com");






        self::$testResults["getaccount"] = $db->getAccount(["username"=>"pelle"]);
        self::$testResults["getcharacter"] = $db->getCharacterWithName(["playername"=>"aladdin"]);
        self::$testResults["getallchars"] = $db->getCharactersWithAccountId(["account_id"=>"516"]);


    }

    function getTestResults(){
        return self::$testResults;
    }

    function runTests(){

    }

}
