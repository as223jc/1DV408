<?php
require_once("src/model/LoginModel.php");
require_once("src/view/LoginView.php");

class LoginController
{
    private $view;
    private $model;

    //Definiera konstanter istället för att behöva skriva om text

    const WRONGINFO = "Wrong username and/or password<br>";
    const ENTERUSER = "You must enter a username<br>";
    const ENTERPASS = "You must enter a password<br>";
    const ENTERMATCH = "You must enter matching passwords<br>";
    const DAYSECONDS = 3624;

    //I konstruktorn skapar vi 1 instans av viewklassen och 1 av modellklassen
    public function __construct() {
        $this->model = new LoginModel();
        $this->view = new LoginView($this->model);

    }

    //Main-funktionen för att kolla om vi är inloggade/vill logga in/vill registrera/vill logga ut
    public function doLogin() {
        if($this->view->getTempMessage()) {
                @$_SESSION["count"] += 1;
                if ($_SESSION["count"] > 1) {
                    unset($_SESSION["temp"]);
                    unset($_SESSION["username"]);
                    $_SESSION["count"] = 0;
                }
        }

        //Kolla om en get har satts på att logga ut, logga isåfall uit användaren och döda cookiesen/sessionen
        if ($this->view->didUserPressLogout()) {
            if ($this->view->isCookieUidSet() && $this->view->isCookieSidSet()) {
                $this->logOut("You have logged out");
            }
            //Annars titta om en get har satts på register, registrera då ny användare
        } else if ($this->view->didUserPressRegister()) {
            //Kontrollers för username/pass
            if ($this->view->didUserPressRegisterSubmit()) {
                $_POST["newuser"] = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST["newuser"]);
                $_SESSION["registrationusername"] = $this->view->getEnteredNewUsername();

                $ret = $this->model->verifyInput($this->view->getEnteredNewUsername(), $this->view->getEnteredNewPassword1(), $this->view->getEnteredNewPassword2());

                //kontroll och skapning av användare till textfil, om allt valideras skapas kakorna för sessionshantering
                $errorMessage = $this->model->addUser($this->view->getEnteredNewUsername(), $this->view->getEnteredNewPassword1(), $this->view->getEnteredNewPassword2());
                if (!$errorMessage) {
                    $_SESSION["temp"] = "Account created!";
                    $_SESSION["username"] = $this->view->getEnteredNewUsername();
                    header('Location: index.php');
                } else {
                    return $this->view->showForm("", $errorMessage, true);
                }
            }
            //Om användaren redan är inloggad med SID-kaka och UID-kaka kollar vi sessionsinformationen, stämmer den så
            //loggar in in användaren annars loggas han ut(vi förstör kak-datan)
        } else if ($this->view->isCookieUidSet() && $this->view->isCookieSidSet()) {
            if($this->model->isCookieSessionValid($this->view->getCookieUid())) {
                if ($this->model->userExists($this->view->getCookieUid())) {
                    if (!$this->view->isUserAlreadyLoggedIn()) {
                        if ($this->model->verifyUserSession($this->view->getCookieUid())) {
                            if (!$this->model->userExists($this->view->getCookieUid(), $this->view->getCookieSid(), true)) {
                                $_SESSION["logged_in"] = true;
                                $_SESSION["temp"] = "Login via cookies succeeded";
                                return $this->view->memberPage();
                            } else
                                $this->logOut("Manipulated cookie information");
                        }
                        header("location:?logout");
                    } else {
                        return $this->view->memberPage();
                    }
                } else {
                    $this->logOut("Manipulated cookie information");
                }
            }else{
                if(isset($_COOKIE["LID"]))
                    $this->logOut("Manipulated cookie information");
                $this->logOut();
            }
        }
        //Om användaren vill logga in (tryckt på login-knappen)börjar med kontrollers
        else if ($this->view->didUserPressLoginSubmit()) {
            $_SESSION["username"] = $this->view->getEnteredUsername();
            if (!$this->view->hasUserEnteredUsername() || !$this->view->getEnteredUsername())
                return $this->view->showForm(self::ENTERUSER);
            if (!$this->view->getEnteredPassword())
                return $this->view->showForm(self::ENTERPASS);
            $ret = $this->model->userExists($this->view->getEnteredUsername(), $this->view->getEnteredPassword());
            if ($ret)
                return $this->view->showForm(self::WRONGINFO);

            $userExists = $this->model->userExists($this->view->getEnteredUsername(), $this->view->getEnteredPassword());
            if($userExists)
                return $this->view->showForm(self::WRONGINFO);
            //Om användaren kommer igenom kontrollerna skapar vi kakor för sessionshantering, antingen för långtids eller för sessionen
            $hashedPass = password_hash($this->view->getEnteredPassword(), PASSWORD_BCRYPT);
            if (isset($_POST["rememberMe"])) {
                //user checked "remember me"
                //set expiration of cookies to cookietime
                $cookietime = time() + 60; //60 seconds
                setcookie('UID', $this->view->getEnteredUsername(), $cookietime);
                setcookie('SID', $hashedPass, $cookietime);
                //Cookie för att komma ihåg att användaren använder sig av remember me funktionen
                setcookie('LID', true, $cookietime);
                $_SESSION["temp"] = "Login succeeded and we will remember you next time";
                $this->model->createCookieSession($this->view->getEnteredUsername(), $cookietime);
            } else {
                //save cookie for session only
                setcookie('UID', $this->view->getEnteredUsername());
                setcookie('SID', $hashedPass);
                $_SESSION["temp"] = "Login succeeded";
            }
            $_SESSION["logged_in"] = true;
            $this->model->updateUser($this->view->getEnteredUsername(), $hashedPass);
            $this->model->createUserSession($this->view->getEnteredUsername());
            header('Location:index.php');
        }
        //Annars om personen vill registrera sig visar vi registreringsformuläret
        if(isset($_GET["register"]))
            return $this->view->showForm("","", true);
        return $this->view->showForm();
    }

    function logOut($msg = ""){
        session_unset();
        unset($_COOKIE["UID"]);
        unset($_COOKIE["SID"]);
        setcookie("UID", "", time() - self::DAYSECONDS * 366, '/');
        setcookie("SID", "", time() - self::DAYSECONDS * 366, '/');
        setcookie("UID", "", time() - self::DAYSECONDS * 366);
        setcookie("SID", "", time() - self::DAYSECONDS * 366);
        setcookie("LID", "", time() - self::DAYSECONDS * 366);
        $_SESSION["temp"] = $msg;
        header('Location:index.php');
    }
}