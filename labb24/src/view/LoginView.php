<?php

session_start();

class LoginView{
	private $model;
	public function __construct(LoginModel $loginmodel){
		$this->model = $loginmodel;
	}	

	public function isOnline(){
		if(isset($_COOKIE['Username']) && isset($_COOKIE['Password'])) {
			return true;
		}
		return false;
	}

	public function showForm(){		
		$ret = "
	<form action='?login' method='post' enctype='multipart/form-data' id='loginForm'>
	<h2>Existing user</h2><br>";

	if(isset($_COOKIE["logError"])){
		$ret .= "<label class='errMsg'>".$_COOKIE["logError"]."</label><br>";
	}

	$ret .= "Username:
	<input type='text' name='Username' class='inputBox'><br>
	Password:
	<input type='password' name='Password' class='inputBox'><br>
	Remember me:
	<input type='checkbox' name='rememberMe' value='1' id='checkBox'><br>
	<input type='submit' value='Login' name='submit1' class='button'>
	<br><br>
	</form>
	<hr>
	<form action='?register' method='post' enctype='multipart/form-data' id='loginForm'>
	<h2>New user</h2><br>";

	if(isset($_COOKIE["RegErr"])){
		$ret .= "<label class='errMsg'>".$_COOKIE["RegErr"]."</label><br>";
	}

	$ret .= "Username:
	<input type='text' name='newuser' class='inputBox'><br>
	Password:
	<input type='password' name='newpass1' class='inputBox'><br>
	Repeat Password:
	<input type='password' name='newpass2' class='inputBox'><br>
	<input type='submit' value='Create' name='submit2' class='button'>
	</form>";
	return $ret;
	}

	public function memberPage()
	{
		// var_dump($_GET);
		// die();
		return "Welcome back, " . $_COOKIE["Username"] . "! <a href='?logout'>Logout</a>";
	}

	// public function tryLogin(){
	// 	if (isset($_POST["$Username"])) {
	// 		$_SESSION["username"] = $_POST["$Username"];
	// 		echo "login user";
	// 		if (isset($_POST["$Password"])) {
	// 			echo " with password";			
	// 		}else{
	// 			echo " WITHOUT password";
	// 		}
	// 	}	
	// }

	// public function tryCreateUser(){		
	// 	echo "create new user";
	// 	if (isset($_POST["$newuser"])) {
	// 		echo "create new user";
	// 		if (isset($_POST["$newpass"])) {
	// 			echo " with password";			
	// 		}else{
	// 			echo " WITHOUT password";
	// 		}
	// 	}
	// }
}