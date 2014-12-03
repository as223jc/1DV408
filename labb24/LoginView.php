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

	public function showForm($logErr="", $regErr="", $reg=false){
		$ret = "
                <form action='?login' method='post' enctype='multipart/form-data' id='loginForm'>
                <h2>Existing user</h2><br>";

        if($logErr)
            $ret .= "<label class='errMsg'>$logErr</label><br>";


        $ret .= "Username:
            <input type='text' name='Username' class='inputBox'><br>
            Password:
            <input type='password' name='Password' class='inputBox'><br>
            Remember me:
            <input type='checkbox' name='rememberMe' value='1' id='checkBox'><br>
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
                    <input type='text' name='newuser' class='inputBox'><br>
                    Password:
                    <input type='password' name='newpass1' class='inputBox'><br>
                    Repeat Password:
                    <input type='password' name='newpass2' class='inputBox'><br>
                    <input type='submit' value='Create' name='submit2' class='button'>
                    </form>";
        }
        $ret .= "<p id='time'>".$this->showTime()."</p><hr>";
	return $ret;
	}

	public function memberPage() {
        $user = strtolower($_COOKIE["UID"]);
        $user = ucfirst($user);
        return "Welcome back, $user! <a href='?logout'>Logout</a><p id='time'>".$this->showTime()."</p>";
	}

    public function showTime(){
        setlocale(LC_ALL, 'swedish');
        return ucfirst($swedish = strftime("%A, %#d %B %Y. Klockan är [%X]"));
    }

}