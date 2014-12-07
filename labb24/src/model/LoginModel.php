<?php

class LoginModel {
    const UserFileLocation = "users.txt";
    const SessFileLocation = "session.txt";
    const CookieFileLocation = "cookies.txt";

    //Funktion för att verifiera in-data
	public function verifyInput($otheruser, $password1, $password2)
	{
        $err = "";

		if (strlen($otheruser)<4) {
			$err .= "Username must be longer than 3 characters<br>";
		}  else if (!preg_match("/^[a-z0-9_]+$/i", $otheruser)) {
			$err .= "Username must be A-Z and no symbols<br>";
		}  else if ($this->userExists($otheruser)) {
			$err .= "Username already exists<br>";
		}
        if ((strlen($password1)<6 || strlen($password2)<6) || ($password1 == NULL || $password2 == NULL)) {
			$err .= "Passwords must be longer than 5 characters<br>";
		}  else if($password1 !== $password2) {
			$err .= "Passwords do not match";
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

    //Uppdatera userpassword-hashen för cookieinfo
    public function updateUser($user, $password, $cookietime = 0) {
        $user = strtolower($user);
        $data = file(self::UserFileLocation);
        $index = 0;
        foreach($data as $key){
            $line = explode(":", $key);
            if($user === $line[0]){
                unset($data[$index]);
                if($cookietime !== 0)
                    $data[] = $user.":".$password.":".$cookietime;
                else
                    $data[] = $user.":".$password;
                break;
            }
            $index++;
        }
        $fh = fopen(self::UserFileLocation, "w");
        foreach($data as $key){
            fwrite($fh, $key);
        }
        fclose($fh);
        return true;
    }
    //create a cookietimer server-side
    public function createCookieSession($usr, $cTime){
        $usr = strtolower($usr);
        $fh = fopen(self::CookieFileLocation, "a+");
            fwrite($fh, $usr.':'.$cTime."\n");
            fclose($fh);
            return true;
    }
    //check if cookie is still valid
    public function isCookieSessionValid($usr){
        $usr = strtolower($usr);
        $data = file(self::CookieFileLocation);
        $index = 0;
        $timeNow = time();
        $cookieValid = true;

        foreach($data as $key) {
            $line = explode(":", $key);
            $cookieTime = $line[1];
            if ($usr === $line[0]) {
                if (($cookieTime - $timeNow) <= 0) {
                    unset($data[$index]);
                    $cookieValid = false;
                }
                $index++;
            }
        }

        $fh = fopen(self::CookieFileLocation, "w");
        foreach($data as $key){
            fwrite($fh, $key);
        }
        fclose($fh);

        return $cookieValid;
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
    //Kontrollera om en användare redan existerar, 1 param = true om match hittas annars false
    //Om man skickar med para2 jämförs lösenord också, 2 param = false om dubbel match hittas annars true
    public function userExists($usr, $pass = "", $verify=false){
        $fh = fopen(self::UserFileLocation, "r");
		while (!feof($fh)) {
		     $user = explode(":", fgets($fh));
            if(preg_match("/\b$usr\b/i", $user[0])) {
                $user[1] = rtrim($user[1]);
                //if user specified he wants to password-check; [second parameter]
                if ($pass && !$verify) {

                    if (password_verify($pass, $user[1])) {
                        fclose($fh);
                        return false;
                    } else {
                        fclose($fh);
                        return true;
                    }
                }else if($pass && $verify){
                    if($pass === $user[1]){
                        return false;
                    }else{
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

