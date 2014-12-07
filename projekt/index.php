<?php
require_once('src/controller/LoginController.php');

try{
    $mysqli = new mysqli("localhost", "admin", "", "projekt");
    $ctrl = new Login($mysqli);

    $ctrl->loginHandler();

}catch(Exception $ex){
    if($mysqli->connect_errno > 0)
        die('Unable to connect to database [' . $mysqli->connect_error . ']');
    echo $ex;
}
?>