<?php

require_once("src/model/LoginModel.php");
require_once("src/view/LoginView.php");

class LoginController
{
    private $view;
    private $model;

    //Definiera konstanter istället för att behöva skriva om text

    const WRONGINFO = "Wrong username and/or password";
    const ENTERUSER = "You must enter a username";
    const ENTERPASS = "You must enter a password";
    const ENTERMATCH = "You must enter matching passwords";

    //I konstruktorn skapar vi 1 instans av viewklassen och 1 av modellklassen
    public function __construct()
    {
        $this->model = new LoginModel();
        $this->view = new LoginView($this->model);
        if(isset($_POST["Username"]))
            $this->usr_username = $_POST["Username"];
        if(isset($_POST["Password"]))
            $this->usr_password = $_POST["Password"];

    }

    //Main-funktionen för att kolla om vi är inloggade/vill logga in/vill registrera/vill logga ut
    public function doLogin()
    {
        //Kolla om en get har satts på att logga ut, logga isåfall uit användaren och döda cookiesen/sessionen
        if (isset($_GET["logout"])) {
            setcookie("UID", "", time() - 3600);
            setcookie("SID", "", time() - 3600);
            session_destroy();
            header('Location:index.php');
            //Annars titta om en get ahr satts på register, registrera då ny användare
        } else if (isset($_GET["register"])) {
            //Kontrollers för username/pass
            if (isset($_POST["newuser"])) {
                if (!$_POST["newuser"])
                    return $this->view->showForm("", self::ENTERUSER, true);
                if (!isset($_POST["newpass1"]) && !isset($_POST["newpass2"]))
                    return $this->view->showForm("", self::ENTERPASS, true);
                if (!$_POST["newpass1"])
                    return $this->view->showForm("", self::ENTERPASS, true);
                if (!$_POST["newpass2"])
                    return $this->view->showForm("", self::ENTERMATCH, true);
                //kontroll och skapning av användare till textfil, om allt valideras skapas kakorna för sessionshantering
                $errorMessage = $this->model->addUser($_POST["newuser"], $_POST["newpass1"], $_POST["newpass2"]);
                if (!$errorMessage) {
                    setcookie('UID', $_POST["newuser"]);
                    $hashPass = password_hash($this->usr_password, PASSWORD_BCRYPT);
                    setcookie('SID', $hashPass);
                    header('Location: index.php');
                } else {
                    return $this->view->showForm("", $errorMessage, true);
                }
            }
            //Om användaren redan är inloggad med SID-kaka och UID-kaka kollar vi sessionsinformationen, stämmer den så
            //loggar in in användaren annars loggas han ut(vi förstör kak-datan)
        } else if (isset($_COOKIE["UID"]) && isset($_COOKIE["SID"])) {
            if(!isset($_SESSION["logged_in"])) {
                if ($this->model->verifyUserSession($_COOKIE["UID"])) {
                    $_SESSION["logged_in"] = true;
                    return $this->view->memberPage();
                } else {
                    header("location:?logout");
                }
            }else{
                return $this->view->memberPage();
            }
        }
        //Om användaren har skickat en get på att logga in (tryckt på login-knappen)börjar med kontrollers
        else if (isset($_GET["login"])) {
            if (!isset($this->usr_username) && !isset($this->usr_password))
                return $this->view->showForm(self::ENTERUSER);
            if (!$this->usr_username)
                return $this->view->showForm(self::ENTERUSER);
            if (!$this->usr_password)
                return $this->view->showForm(self::ENTERPASS);

            $ret = $this->model->userExists($this->usr_username, $this->usr_password);
            if ($ret)
                return $this->view->showForm(self::WRONGINFO);

            $userExists = $this->model->userExists($this->usr_username, $this->usr_password);
            if($userExists)
                return $this->view->showForm(self::WRONGINFO);
            //Om användaren kommer igenom kontrollerna skapar vi kakor för sessionshantering, antingen för långtids eller för sessionen
            $hashedPass = password_hash($this->usr_password, PASSWORD_BCRYPT);
            if (isset($_POST["rememberMe"])) {
                //user checked "remember me"
                //set expiration of cookies to today's time PLUS 365 days
                setcookie('UID', $this->usr_username, time() + 60 * 60 * 24 * 365);
                setcookie('SID', $hashedPass, time() + 60 * 60 * 24 * 365);
            } else {
                //save user for session only then remove cookies
                setcookie('UID', $this->usr_username);
                setcookie('SID', $hashedPass);
            }
            $this->model->createUserSession($this->usr_username);
            header('Location:index.php');
        }
        //Annars om personen vill registrera sig visar vi registreringsformuläret
        if(isset($_GET["register"]))
            return $this->view->showForm("","", true);
        return $this->view->showForm();
    }
}