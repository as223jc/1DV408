<?php
require_once('src/view/htmlPage.php');
require_once('src/controller/Login.php');
require_once('src/model/AccountDAL.php');
require_once('src/model/TestModels.php');
require_once('src/view/TestCases.php');

try{

}catch(Exception $ex){
    if($mysqli->connect_errno > 0)
        die('Unable to connect to database [' . $db->connect_error . ']');
    echo $ex;
}