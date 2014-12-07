<?php

session_start();

class LoginView{
	private $model;
	public function __construct(LoginModel $loginmodel){
		$this->model = $loginmodel;
	}	

//	public function isOnline(){
//		if(isset($_COOKIE['Username']) && isset($_COOKIE['Password'])) {
//			return true;
//		}
//		return false;
//	}

	public function showForm($logErr="", $regErr="", $reg=false){
		$ret = "
                <form action='?login' method='post' enctype='multipart/form-data' id='loginForm'>
                <h2>Existing user</h2><br>";

        if($logErr)
            $ret .= "<label class='errMsg'>$logErr</label><br>";
            $ret .= "<label class='logoutMsg'>";
        $ret .= $this->getTempMessage();
        $ret .= "</label><br>";

        $ret .= "Username:
            <input type='text' name='Username' class='inputBox'";
        $ret.=  " value='";
        $ret.= $this->getSessionUserName();
        $ret.=  "'";
        $ret.=  "><br>
            Password:
            <input type='password' name='Password' class='inputBox'><br>
            Remember me:
            <input type='checkbox' name='rememberMe' value='0' id='checkBox'><br>
            <input type='submit' value='Login' name='submit1' class='button'>
            <br><br>
            </form>
            <a href='?register'>New Account</a>";
        if($reg) {
            $ret = "<form action='?register' method='post' enctype='multipart/form-data' id='loginForm'>
                    <h2>New user</h2><br>";
            if ($regErr)
                $ret .= "<label class='errMsg'>$regErr</label><br>";

            $ret .= "Username:
                    <input type='text' name='newuser' class='inputBox'";
            $ret.=  " value='";
            $ret.= $this->getRegSessionUserName();
            $ret.=  "'";
            $ret.=  "><br>
                    Password:
                    <input type='password' name='newpass1' class='inputBox'><br>
                    Repeat Password:
                    <input type='password' name='newpass2' class='inputBox'><br>
                    <input type='submit' value='Create' name='submit2' class='button'>
                    </form>";
            $ret .= "<a href='index.php'>Back</a>";
        }

        $ret .= "<p id='time'>".$this->showTime()."</p><hr>";
	return $ret;
	}

	public function memberPage() {
        $user = strtolower($_COOKIE["UID"]);
        $user = ucfirst($user);
//        $ret = "";
        $ret = $this->getTempMessage();
//        if(isset($_SESSION["rememberMe"]) && $_SESSION["rememberMe"] === "remember")
//            $rememberme = "We will remember you next time!";
        return $ret .= "<br><a href='?logout'>Logout</a><p id='time'>".$this->showTime()."</p>";
	}


    public function showTime(){
        setlocale(LC_ALL, 'swedish');
        return ucfirst($swedish = strftime("%A, %#d %B %Y. Klockan är [%X]"));
    }
    public function getTempMessage(){
        if(isset($_SESSION["temp"]))
            return $_SESSION["temp"];
        return false;
    }
    public function getSessionUserName(){
        if(isset($_SESSION["username"]))
            return $_SESSION["username"];
        return false;
    }
    public function getRegSessionUserName(){
        if(isset($_SESSION["registrationusername"]))
            return $_SESSION["registrationusername"];
        return false;
    }
    public function isUserAlreadyLoggedIn(){
        return isset($_SESSION["logged_in"]);
    }
    public function didUserPressRegister(){
        return isset($_GET["register"]);
    }
    public function didUserPressLogin(){
        return isset($_GET["login"]);
    }
    public function didUserPressLoginSubmit(){
        return isset($_POST["submit1"]);
    }
    public function didUserPressRegisterSubmit(){
        return isset($_POST["submit2"]);
    }
    public function didUserPressLogout(){
        return isset($_GET["logout"]);
    }
    public function hasUserEnteredNewUsername(){
        return isset($_POST["newuser"]);
    }
    public function hasUserEnteredPassword1(){
        return isset($_POST["newpass1"]);
    }
    public function hasUserEnteredPassword2(){
        return isset($_POST["newpass2"]);
    }
    public function isCookieUidSet(){
        return isset($_COOKIE["UID"]);
    }
    public function isCookieSidSet(){
        return isset($_COOKIE["SID"]);
    }
    public function getCookieUid(){
        return $_COOKIE["UID"];
    }
    public function getCookieSid(){
        return $_COOKIE["SID"];
    }
    public function getEnteredNewUsername(){
        return $_POST["newuser"];
    }
    public function getEnteredNewPassword1(){
        return $_POST["newpass1"];
    }
    public function getEnteredNewPassword2(){
        return $_POST["newpass2"];
    }
    public function hasUserEnteredUsername(){
        return isset($_POST["Username"]);
    }
    public function hasUserEnteredPassword(){
        return isset($_POST["Password"]);
    }
    public function getEnteredUsername(){
        return $_POST["Username"];
    }
    public function getEnteredPassword(){
        return $_POST["Password"];
    }

}