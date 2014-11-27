<?php
require_once('src/model/TestModels.php');
class RunTests{
    private $runTests;
    function __construct(){
        $this->runTests = new Tests();
    }
    function execTests(){
        $htmlText = "";
        $testResults = $this->runTests->getTestResults();

        $htmlText .= "<h2>Testing</h2>";
        $htmlText .= "<h3>Mysqli testcases</h3>";
        $htmlText .= "<b>Create Account 1: </b>";
        $htmlText .= @(assert($testResults["Create Account 1"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Create Account 2: </b>";
        $htmlText .= @(assert($testResults["Create Account 2"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Delete Account: </b>";
        $htmlText .= @(assert($testResults["Delete Account"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Delete Account 2: </b>";
        $htmlText .= @(assert($testResults["Delete Account 2"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Create Account: </b>";
        $htmlText .= @(assert($testResults["Create Character"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Check If Account Exists Existing Username: </b>";
        $htmlText .= @(assert($testResults["Check If Account Exists"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Check If Account Exists Non-existing Username: </b>";
        $htmlText .= @(assert($testResults["Check If Account Exists 2"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Check If Account With Password Exists With Fake: </b>";
        $htmlText .= @(!assert($testResults["Check If AccountPassword Exists"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");
        $htmlText .= "<br><b>Check If Account With Password Exists With Existing: </b>";

        $htmlText .= @(assert($testResults["Check If AccountPassword Exists 2"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Check If Character Exists: </b>";
        $htmlText .= @(assert($testResults["Check If Character Exists"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Check If Character Exists with wrong name: </b>";
        $htmlText .= @(assert(!$testResults["Check If Character Exists with wrong name"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Delete existing player: </b>";
        $htmlText .= @(assert($testResults["Delete Existing Player"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test good cookie authentication: </b>";
        $htmlText .= @(assert($testResults["Test Good Cookie Auth"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test bad cookie authentication: </b>";
        $htmlText .= @(!assert($testResults["Test Bad Cookie Auth"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><h3>Code validation</h3>";
        $htmlText .= "<b>Test Bad Username:</b>";
        $htmlText .= @(!assert($testResults["Test Bad Username"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Bad Username 2:</b>";
        $htmlText .= @(!assert($testResults["Test Bad Username 2"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Good Username:</b>";
        $htmlText .= @(assert($testResults["Test Good Username"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Bad Password:</b>";
        $htmlText .= @(assert($testResults["Test Bad Password"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Bad Password 2:</b>";
        $htmlText .= @(assert($testResults["Test Bad Password 2"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Good Password:</b>";
        $htmlText .= @(!assert($testResults["Test Good Password"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Bad Matching Password:</b>";
        $htmlText .= @(assert($testResults["Test Bad Matching Password"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Good Matching Password:</b>";
        $htmlText .= @(!assert($testResults["Test Good Matching Password"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Bad Email:</b>";
        $htmlText .= @(assert($testResults["Test Bad Email"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><b>Test Good Email:</b>";
        $htmlText .= @(!assert($testResults["Test Good Email"])==1 ? "<span class='green'>As intended</span>" : "<span class='red'>ERROR! Not working!</span>");

        $htmlText .= "<br><br><b>Get Account Data</b><br>";

        foreach($testResults["getaccount"]  as $key => $res) {
            $htmlText .= "$key : $res";
            $htmlText .= "<br>";
        }

        $htmlText .= "<br><br><b>Get Character Data</b><br>";

        foreach($testResults["getcharacter"]  as $key => $res) {
            $htmlText .= "$key : $res";
            $htmlText .= "<br>";
        }

        $htmlText .= "<br><br><b>Get All Characters From One Account</b><br>";

        foreach($testResults["getallchars"] as $allChars) {
            foreach ($allChars as $key => $res) {
                $htmlText .= "$key : $res";
                $htmlText .= "<br>";
            }
            $htmlText .= "<br>";
        }
        return $htmlText;
    }
}