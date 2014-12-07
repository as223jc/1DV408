<?php
date_default_timezone_set('Europe/Stockholm');

require_once("../common/htmlPage.php");
require_once("src/controller/LoginController.php");

$login = new LoginController();
$body = $login->doLogin();

$view = new htmlPage();
$view->showHtml($body);
