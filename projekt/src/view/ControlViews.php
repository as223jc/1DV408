<?php
class ControlViews{
    //
    //Checks
    //

    function didUserPressSubmitLogin(){
        return isset($_POST["submitLogin"]);
    }
    function didUserPressCreateCharacter(){
        return isset($_GET["createCharacter"]);
    }
    function didUserPressSubmitCreateCharacter(){
        return isset($_POST["charactersubmit"]);
    }
    function didUserPressDeleteCharacter(){
        return isset($_GET["delete"]);
    }
    function didUserPressCreatePost(){
        return isset($_GET["createpost"]);
    }
    function didUserPressSubmitPost(){
        return isset($_POST["submitpost"]);
    }
    function didUserPressEditPost(){
        return isset($_GET["postedit"]);
    }
    function didUserPressSubmitEditPost(){
        return isset($_POST["submitedit"]);
    }
    function didUserPressDeletePost(){
        return isset($_GET["postdel"]);
    }
    function isUserAlreadyLoggedIn(){
        return isset($_SESSION["logged_in"]);
    }
    function didUserPressCreateAccount(){
        return isset($_POST["accountCreate"]);
    }
    function didUserPressSubmitCreateAccount(){
        return isset($_POST["submitRegister"]);
    }
    function didUserPressViewCharacter(){
        return isset($_GET["characterpage"]);
    }
    function didUserPressViewAccount(){
        return isset($_GET["accountpage"]);
    }
    function isLoginUsernameSet(){
        return isset($_POST["login_username"]);
    }
    function isLoginPasswordSet(){
        return isset($_POST["login_password"]);
    }

    //
    //Gets
    //

    //Login
    function getExistingAccountName(){
        return $_POST["login_username"];
    }
    function getExistingAccountPassword(){
        return $_POST["login_password"];
    }

    //Account Registration
    function getNewAccountName(){
        return $_POST["register_username"];
    }
    function getNewAccountPassword1(){
        return $_POST["register_password"];
    }
    function getNewAccountPassword2(){
        return $_POST["register_password2"];
    }
    function getNewAccountMail(){
        return $_POST["register_email"];
    }

    //Character Creation
    function getNewPlayerName(){
        return $_POST["playername"];
    }
    function getNewPlayerSex(){
        return $_POST["sex"];
    }
    function getNewPlayerCity(){
        return $_POST["city"];
    }

    //Character Deletion
    function getSessionPlayerName(){
        return $_SESSION["charD"];
    }

    //Get Username from cookie
    function getCookieUsername(){
        return $_COOKIE["UID"];
    }

    //Posts
    function getNewPostTitle(){
        return $_POST["newposttitle"];
    }
    function getNewPostText(){
        return $_POST["newposttext"];
    }
    function getEditPostId(){
        return $_POST["editpostid"];
    }
    function getEditPostTitle(){
        return $_POST["editposttitle"];
    }
    function getEditPostText(){
        return $_POST["editposttext"];
    }
}