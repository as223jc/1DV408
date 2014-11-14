<?php

class LoginModel {
	private $pwd;
	private $uname;
	private $userList;
	private $fileLocation = "../../users.txt";
	public function __construct(){
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
	}

	public function verifyInput($otheruser, $password1, $password2)
	{
        $err = "";

		if (strlen($otheruser)<4) {
			$err = "Username must be longer than 3 characters";
		} else if (!preg_match("/^[a-z0-9_]+$/i", $otheruser)) {			
			$err = "Username must be A-Z and no symbols";
		} else if ($this->userExists($otheruser)) {
			$err = "Username already exists";
		} else if ((strlen($password1)<4 || strlen($password2)<4) || ($password1 == NULL || $password2 == NULL)) {
			$err = "Password must be longer than 3 characters";
		} else if($password1 !== $password2) {
			$err = "Passwords do not match";
		}

		return $err;
	}

	public function addUser($otheruser, $password1, $password2) {
		$errMessage = $this->verifyInput($otheruser, $password1, $password2);
		if ($errMessage === "") {
            $hashedPass = password_hash($password1, PASSWORD_BCRYPT);
			$fh = fopen($this->fileLocation, "a+");
			fwrite($fh, $otheruser.':'."$hashedPass\n");
			fclose($fh);
			return false;
		}
		return $errMessage;		
	}

	public function userExists($usr, $pass = ""){
		$fh = fopen($this->fileLocation, "r");
		while (!feof($fh)) {
		     $user = explode(":", fgets($fh));
//		    if (strcasecmp($usr, $user[0]) === 0) {
            if(preg_match("/\b$usr\b/i", $user[0])){
                //BUG::space is added on reading and matching not possible, this
                //trims all trailing spaces which is UNWANTED
                $user[1] = rtrim($user[1]);
//                $ok = preg_match("/\b$user[1]\b/", $pass);
//                echo rtrim($user[1]);
//                echo "found pass:$user[1]. entered pass:$pass. $ok";
//                die();
            //if user specified he wants to password-check; [second parameter]
		  	if($pass !== ""){
//		  		if (preg_match("/\b$user[1]\b/", $pass)) {

                if(password_verify($pass, $user[1])){
		  			return "";
		  		}else{
                    return "Incorrect password";
                }
		  	}
			fclose($fh);
		  	return true;
		  }
		}
		fclose($fh);
		return false;
	}

	public function login($usr, $pass){
		$err = "";
		if (isset($_POST["Username"]) && isset($_POST["Password"])) {
			if ($_POST["Username"] !== "" || strlen($_POST["Username"]) < 4) {
				if ($_POST["Password"] !== "" || strlen($_POST["Password"]) < 4) {
					if ($this->userExists($usr, $pass)) {
						setcookie('Username', $usr);
						setcookie('Password', $pass);
						header('Location:index.php');
					}
				} else {
					$err = "Password incorrect";
				}
			} else {
				$err = "Username not found";
			}	
		} 
		
	}
}

