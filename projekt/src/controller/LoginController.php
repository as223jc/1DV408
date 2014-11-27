<?php
require_once('src/model/Users.php');

session_start();

class Login {
    private $users;
    function __construct(mysqli $mysqli) {
        $this->users = new Users($mysqli);
    }
    function loginHandler()
    {
        if (!isset($_SESSION["logged_in"])) {
            if (!$this->isAlreadyLoggedIn()) {
                if (isset($_POST["login_username"])){
                    return $this->logOn();
                }
                else if (isset($_POST["submitRegister"])) {
                    return $this->createAccount(
                        $_POST["register_username"],
                        $_POST["register_password"],
                        $_POST["register_password2"],
                        $_POST["register_email"]
                    );
                }
            }
        }
    }

            //reload ui? hide login form? replace with 'welcome back, username!
            //                                         'account info logout
//

    function logOn(){
        return $this->users->logOn();
    }

    function accountExists($username, $pass){
        return $this->users->checkUserNamePassword($username, $pass);
    }

    function checkEmail($email){
        return $this->users->checkEmail($email);
    }

    function isAlreadyLoggedIn(){
        return $this->users->checkLoggedIn();
    }

    function createAccount($usr, $pass1, $pass2, $email){
        return $this->users->createNewAccount($usr, $pass1, $pass2, $email);
    }

    function getAccountData($usr){
        return $this->users->retrieveAccount($usr);
    }

    function getAllPosts(){
        return $this->users->retrievePosts();
    }

    function getAllCharacters($usr){
        return $this->users->getCharacterListWithUsername($usr);
    }

    function deleteCharacterSuccessful($playername){
        return $this->users->deleteCharacter($playername);
    }

    function characterCreated($characterInfo){
        return $this->users->createNewCharacter($characterInfo);
    }

    function logOut(){
        if(session_destroy()){
            unset($_SESSION["logged_in"]);
            unset($_COOKIE["SID"]);
            unset($_COOKIE["UID"]);
            setcookie("SID", '', time() - 3600);
            setcookie("UID", '', time() - 3600);
            header("Location: ../../index.php");
        }
    }
}