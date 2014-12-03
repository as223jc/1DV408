<?php

class LoginModel {
    const UserFileLocation = "users.txt";
    const SessFileLocation = "session.txt";

    public function __construct(){
//        ini_set('display_errors', 1);
//        error_reporting(E_ALL);
	}
    //Funktion för att verifiera in-data
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
    //Lägg till ny användare efter verifiering
	public function addUser($otheruser, $password1, $password2) {
		$errMessage = $this->verifyInput($otheruser, $password1, $password2);
        $otheruser = strtolower($otheruser);
		if ($errMessage === "") {
            $hashedPass = password_hash($password1, PASSWORD_BCRYPT);
			$fh = fopen(self::UserFileLocation, "a+");
			fwrite($fh, $otheruser.':'."$hashedPass\n");
			fclose($fh);
			return false;
		}
		return $errMessage;		
	}
    //Skapa ny och spara ner användarsession med agent och remote address
    public function createUserSession($usr){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $addr = $_SERVER['REMOTE_ADDR'];
        $fh = fopen(self::SessFileLocation, "a+");
        fwrite($fh, "$usr:$agent:$addr\n");
        fclose($fh);
    }
    //Verifiera den sparande användarsessionen
    public function verifyUserSession($usr){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $addr = $_SERVER['REMOTE_ADDR'];
        $fh = fopen(self::SessFileLocation, "r");
        while (!feof($fh)) {
            $data = explode(":", fgets($fh));
            if (preg_match("/\b$usr\b/i", $data[0])) {
                if($data[1] === $agent) {
                    if (trim($data[2]) === $addr) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    //Kontrollera om en användare redan existerar
    public function userExists($usr, $pass = ""){
        $fh = fopen(self::UserFileLocation, "r");
		while (!feof($fh)) {
		     $user = explode(":", fgets($fh));
            if(preg_match("/\b$usr\b/i", $user[0])){
                $user[1] = rtrim($user[1]);
            //if user specified he wants to password-check; [second parameter]
		  	if($pass){
                if(password_verify($pass, $user[1])){
                    fclose($fh);
                    return false;
		  		}else{
                    fclose($fh);
                    return true;
                }
		  	}
			fclose($fh);
		  	return true;
		  }
		}
		fclose($fh);
        if($pass)
		    return true;
        return false;
	}
}

