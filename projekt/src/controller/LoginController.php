<?php
require_once('src/model/Users.php');
require_once('src/view/PageViews.php');
require_once('src/view/ControlViews.php');

session_start();

class Login {
    private $users;
    private $pViews;
    private $ctrlViews;
    function __construct(mysqli $sqli) {
        $this->users = new Users($sqli);
        $this->pViews = new PageViews();
        $this->ctrlViews = new ControlViews();
    }
    function loginHandler() {
        $loginResponse="";
        $registerResponse ="";
        $registrationSuccess = "";
        $charResponse = "";
        $postResponse = "";

        @$characterList = $this->users->getCharacterListWithUsername($this->ctrlViews->getCookieUsername());
        $posts = $this->users->retrievePosts();
        @$accInfo = $this->users->retrieveAccount($this->ctrlViews->getCookieUsername());
        $character = null;

        if($this->ctrlViews->didUserPressDeleteCharacter()) {
            $this->users->deleteCharacter($this->ctrlViews->getSessionPlayerName());
            header("location: ?accountpage");
        } else if($this->ctrlViews->didUserPressDeletePost()){
            if($this->users->deletePost($posts[$_GET["index"]]["id"])){
                header("location: index.php");
            }
        }
        if($this->ctrlViews->didUserPressSubmitLogin()) {
            $loginResponse = $this->tryLogin();
        } else if($this->ctrlViews->didUserPressSubmitCreateAccount()) {
            $registerResponse = $this->tryLogin();
            if($registerResponse===true) {
                $registrationSuccess = "Account has been created! You may log into your account above.";
                $registerResponse = "";
            }
        }else if($this->ctrlViews->didUserPressSubmitCreateCharacter()){
            $charResponse = $this->users->createNewCharacter([
                "accountid"=>$accInfo["id"],
                "playername"=>$this->ctrlViews->getNewPlayerName(),
                "sex"=>$this->ctrlViews->getNewPlayerSex(),
                "playerposition"=>$this->ctrlViews->getNewPlayerCity()
            ]);
            if($charResponse===true)
                header("location:?accountpage");
        } else if($this->ctrlViews->didUserPressSubmitPost()){
            $postResponse = $this->users->createNewPost([
                "title"=>$this->ctrlViews->getNewPostTitle(),
                "text"=>$this->ctrlViews->getNewPostText(),
                "author"=>$accInfo["username"]
            ]);
            if(!$postResponse)
                header("location:index.php");
        } else if($this->ctrlViews->didUserPressSubmitEditPost()){
            $postResponse = $this->users->editExistingPost([
                "id"=>$this->ctrlViews->getEditPostId(),
                "title"=>$this->ctrlViews->getEditPostTitle(),
                "text"=>$this->ctrlViews->getEditPostText()
            ]);
            if(!$postResponse)
                header("location:index.php");
        }

        $this->pViews->View([
            "loginResponse"=>$loginResponse,
            "accInfo"=>$accInfo,
            "registerResponse"=>$registerResponse,
            "registrationSuccess"=>$registrationSuccess,
            "charResponse"=>$charResponse,
            "postResponse"=>$postResponse,
            "posts"=>$posts,
            "characterList"=>$characterList,
            "postResponse"=>$postResponse
        ]);
    }

    function tryLogin(){
        if (!$this->ctrlViews->isUserAlreadyLoggedIn()) {
            if (!$this->users->checkLoggedIn()) {
                if ($this->ctrlViews->isLoginUsernameSet()){
                    return $this->users->logOn();
                } else if ($this->ctrlViews->didUserPressSubmitCreateAccount()) {
                    return $this->users->createNewAccount(
                        $this->ctrlViews->getNewAccountName(),
                        $this->ctrlViews->getNewAccountPassword1(),
                        $this->ctrlViews->getNewAccountPassword2(),
                        $this->ctrlViews->getNewAccountMail()
                    );
                }
            }
        }
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