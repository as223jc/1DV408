<?php

require_once("src/model/LoginModel.php");
require_once("src/view/LoginView.php");

class LoginController {
	private $view;
	private $model;
	public function __construct(){
		$this->model = new LoginModel();
		$this->view = new LoginView($this->model);
	}
	public function doLogin(){		
		if(!$this->view->isOnline()){
			echo $this->view->showForm();
		}
	}

	public function currState()	{

		$regErr ="";
        $logErr = "";
		if (isset($_GET["login"])) {
			if(isset($_POST["Username"])&& isset($_POST["Password"])){
                if($_POST["Username"] !== ""){
                    if($_POST["Password"] !== ""){
                        $logErr = $this->model->userExists($_POST["Username"], $_POST["Password"]);
                        if($logErr===""){
                            $hashPass = password_hash($_POST["Password"], PASSWORD_BCRYPT);
                            if(isset($_POST["rememberMe"])){
                                //user checked "remember me"
                                //set expiration of cookies to today's time PLUS 365 days
                                setcookie('Username', $_POST["Username"], time()+60*60*24*365);
                                setcookie('Password', $hashPass, time()+60*60*24*365);
                            }else {
                                //save user for session only then remove cookies
                                setcookie('Username', $_POST["Username"]);
                                setcookie('Password', $hashPass);
                            }
                        }else{
                            if($logErr !== "Incorrect password") {
                                $logErr = "User not found";
                            }
                        }
                    }else{
                        $logErr = "Enter a password";
                    }
                }else{
                    $logErr = "Enter a username";
                }
                setcookie('logError', $logErr, time()+1);
                header('Location:index.php');
            }
		}
		if (isset($_GET["register"])) {
			if(isset($_POST["newuser"])){
                if($_POST["newuser"] !== "") {
                    if (isset($_POST["newpass1"]) && isset($_POST["newpass2"])) {
                        if($_POST["newpass1"] !== "") {
                            if($_POST["newpass2"] !== "") {
                                $errorMessage = $this->model->addUser($_POST["newuser"], $_POST["newpass1"], $_POST["newpass2"]);
                                if (!$errorMessage) {
                                    setcookie('Username', $_POST["newuser"]);
                                    $hashPass = password_hash($_POST["newpass1"], PASSWORD_BCRYPT);
                                    setcookie('Password', $hashPass);
                                    header('Location: http://dadel.me/labb2/index.php');
                                } else {
                                    $regErr = $errorMessage;
                                }
                            }else{
                                $regErr = "You must enter matching password";
                            }
                        }else{
                            $regErr = "You must enter a password";
                        }
                    } else {
                        $regErr = "You must enter a password";
                    }
                }else{
                    $regErr = "You must enter a username";
                }
            }
			setcookie('RegErr', $regErr, time()+1);
			header('Location:index.php');
		} else if (isset($_GET["logout"])) {
			setcookie("Username", "", time()-3600);
			setcookie("Password", "", time()-3600);
			header('Location:index.php');
		} else if ($this->view->isOnline()) {					
		 	echo $this->view->memberPage();        
		}  else{
			$this->doLogin(); 
		}		
	}
}