<?php

require_once("../common/htmlPage.php");
require_once("src/controller/LoginController.php");

$login = new LoginController();
$body = $login->currState();

$view = new htmlPage();
$view->showHtml($body);
