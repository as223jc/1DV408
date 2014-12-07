<?php
require_once('htmlPage.php');
require_once('src/view/ControlViews.php');

class PageViews
{
    function __construct()
    {
        $this->ctrlViews = new ControlViews();
    }

    function View($info)
    {
        $htmlView = new htmlPage();

        $ret = $this->loginView(
            $info["loginResponse"],
            $info["accInfo"]);

        $ret .= $this->accountView(
            $info["charResponse"],
            $info["characterList"],
            $info["accInfo"],
            $info["registerResponse"],
            $info["registrationSuccess"],
            $info["posts"],
            $info["postResponse"]);
        $ret .= "</main>";
        $htmlView->showHtml($ret);
    }

    function loginView($loginResponse, $accInfo)
    {
        $ret = "";
        if (!$this->ctrlViews->isUserAlreadyLoggedIn())
            $ret = "
                <form id='login-form' method='post' action=''>
                    <input type='text' name='login_username' placeholder='Username'>
                    <input type='password' name='login_password' placeholder='******'>
                    <button type='submit' name='submitLogin' id='submitLogin'>Login</button>
                    <button type='submit' name='accountCreate' id='submitCreate'>Create account</button>
                    <div id='formError'><p>" . $loginResponse . "</p></div>
                </form>
                ";
        else {
            if ($this->ctrlViews->isUserAlreadyLoggedIn()) {
                $ret = "
                    <p id='loggedinSq'>
                        Welcome back, <a href='?accountpage'>" . @$_COOKIE['UID'] . "</a>!
                        <a href='?accountpage'>Account</a>,";
                if ($accInfo["accounttype"] === 2)
                    $ret .= "<a href='?createpost'>New Post</a>";
                $ret .= "
                             <a href='src/controller/logout.php'>Logout</a>
                    </p>
                ";
            }
        }
        $ret .= '
        </div>
    </header><!--header end-->
    <main id="main-content">
        <main id="front-page">
            ';
        return $ret;
    }

    function accountView($charResponse, $characterList, $accInfo, $registerResponse, $registrationSuccess, $posts, $postResponse)
    {
        $ret = "";
        if ($this->ctrlViews->didUserPressCreateCharacter()) {
            $ret .= "
                <div id='createCharacterPage'>
                <fieldset class='pagefieldset'>
                <legend>Create New Character</legend>

                    <form id='characterCreation' action='' method='post'>
                        <input type='text' name='playername' placeholder='playername'><br>
                        <select name='sex' id='sex'>
                            <option value='0'>male</option>
                            <option value='1'>female</option>
                        </select><br>
                        <select name='city' id='city'>
                            <option value='kalmar'>kalmar</option>
                            <option value='stockholm'>stockholm</option>
                        </select><br>
                        <button id='charactersubmit' name='charactersubmit'>Create</button>
                    </form>
                    <div id='formError'><p>" . $charResponse . "</p></div>
                    </fieldset>
                </div>
            ";
        } else if ($this->ctrlViews->didUserPressViewCharacter()) {
            foreach ($characterList as $key) {
                if ($key["playername"] === $_GET["characterpage"])
                    $character = $key;
                $_SESSION["charD"] = $character["playername"];
            }

            $ret .= "
                <div id='characterPage'>
                <fieldset class='pagefieldset'>
                <legend>" . $character["playername"] . "</legend>
                    <span id='Name' class='italic small gray'>Name: </span><span>" . $character["playername"] . "</span><br>
                    <span id='Level' class='italic small gray'>Level: </span><span>" . $character["level"] . "</span><br>
                    <span id='Sex' class='italic small gray'>Sex: </span><span>" . $character["sex"] . "</span><br>
                    <span id='Created' class='italic small gray'>Created: </span><span>" . $character["created"] . "</span><br>
                    <span id='Lastlogin' class='italic small gray'>Last login: </span><span>";

                    $ret .= $character["lastlogin"] === 0 ? "Never logged in" : date("YmdGi", $character["lastlogin"]);

                    $ret .= "</span><br>
                    <p>
                    <br>
                    <a class='red small delchar' href='?delete' onclick='confirmClick()'>Delete</a>
                    <a class='white small returntoacc' href='?accountpage'>Back To Account</a>
                    </p>
                    </fieldset>
                </div>
                ";
        } else if ($this->ctrlViews->didUserPressViewAccount() && $this->ctrlViews->isUserAlreadyLoggedIn()) {
            $ret .= "
                <div id='accountPage'>
                <fieldset class='pagefieldset'>
                <legend>Account Page</legend>
                    <span id='Username' class='italic small gray'>Username: </span><span>" . $accInfo['username'] . "</span><br>
                    <span id='E-mailaddress' class='italic small gray'>E-mail address: </span><span>" . $accInfo['email'] . "</span><br>
                    <span id='Created' class='italic small gray'>Created: </span><span>" . date("Y-m-d G:i", strtotime($accInfo["created"])) . "</span><br>
                    <span id='Lastlogin' class='italic small gray'>Last login: </span><span>" . date("Y-m-d G:i", strtotime($accInfo["lastlogin"])) . "</span><br>
                    <span id='Characters'><br><h3>Character List</h3>
                    <ul id='characterList' class='italic small'>";
            if (count($characterList) > 0)
                foreach ($characterList as $key)
                    $ret .= "<li  id='charlist'><a class='white' href='?characterpage=" . $key['playername'] . "'>" . $key['playername'] . "</a> - level " . $key['level'] . "</li><br>";
            else
                $ret .= "<li class='italic small underline'>You have no characters</li>";
            $ret .= "</ul>
                    </span>
                    <p><br><br><a class='white small' href='?createCharacter'>Create New Character</a></p>
                    </fieldset>
                </div>
                ";
        } else if ($accInfo["accounttype"] === 2 && $this->ctrlViews->didUserPressCreatePost()) {
            $ret .= "
               <div id='post-div'>
                    <fieldset class='pagefieldset'>
                        <legend>New Post</legend>
                        <form action='' method='post'>
                        <input type='text' name='newposttitle' id='newposttitle' placeholder='Enter a title'/>
                        <textarea name='newposttext' id='newposttext' cols='30' rows='10' placeholder='Type something..'></textarea>
                        <button name='submitpost' id='postButton'>Submit</button>
                        </form>
                    </fieldset>
                    <div id='formError'><p>" . $postResponse . "</p></div>
                </div>
                ";
        } else if ($accInfo["accounttype"] === 2 && $this->ctrlViews->didUserPressEditPost()) {
            $ret .= "
               <div id='post-div'>
                    <fieldset class='pagefieldset'>
                        <legend>Edit Post</legend>
                        <form action='' method='post'>
                        <input type='text' name='editposttitle' id='newposttitle' placeholder='Enter a title' value='";
            $ret .= $posts[($_GET["index"])]["title"];
            $ret .= "'><textarea name='editposttext' id='newposttext' cols='30' rows='10' placeholder='Type something..'>";
            $ret .= $posts[$_GET["index"]]["text"];
            $ret .= "</textarea><input type='hidden' name='editpostid' value='" . $_GET["postedit"] . "'>
                        <button name='submitedit' id='postButton'>Submit Edit</button>
                        </form>
                    </fieldset>
                    <div id='formError'><p>" . $postResponse . "</p></div>
                </div>
                ";
        } else {
            $posts = array_reverse($posts);
            if (count($posts) > 0) {
                $index = 0;
                $ret .= "<div id='all_posts'>";
                foreach ($posts as $key) {
                    $ret .= "<article class='article_post'>";
                    if ($accInfo["accounttype"] === 2) {
                        $ret .= "<a title='Delete Post' class='small red' id='postx' href='?postdel=" . $posts[$index]["id"] . "&index=" . ((count($posts) - 1) - $index) . "'>x</a>
                        <a title='Edit Post' class='small blue' id='postx' href='?postedit=" . $posts[$index]["id"] . "&index=" . ((count($posts) - 1) - $index) . "'>o</a>";
                    }
                    $ret .= "<header class='article_header'>
                 " . $key['title'] .
                        "</header>
                 <main class='article_body'>
                 " . $key['text'] .
                        "</main>
                 <footer class='article_footer'>
                 " . $key['author'] . ", " . date("d-M-Y G:i", strtotime($key['created']))
                        . "</footer>
                 </article>";
                    $index++;
                }
                $ret .= "</div>";
            } else {
                $ret .= "<article class='article_post'><header class='article_header'>Välkommen</header><p id='exampletext'>Just nu finns det inga posts och detta är en exempeltext som kommer att ersättas när det finns inlägg</p></article>";
            }

            $ret .= "</main>";

            if ($this->ctrlViews->didUserPressCreateAccount() || $this->ctrlViews->didUserPressSubmitCreateAccount())
                $ret .= "
                <div id='register-div'>
                <fieldset class='pagefieldset'>
                <legend>Create New Character</legend>
                    <form id='register-form' method='post' action=''>
                        <input type='text' name='register_username' placeholder='Username'>
                        <input type='password' name='register_password' placeholder='******'>
                        <input type='password' name='register_password2' placeholder='******'>
                        <input type='email' name='register_email' placeholder='email@adress.com'>
                        <button type='submit' name='submitRegister' id='submitRegister'>Register</button>
                            <div id='formError'><p>" . $registerResponse . "</p></div>
                            <div id='formSuccess'><p>" . $registrationSuccess . "</p></div>
                    </form>
                    </fieldset>
                </div>
            ";


        }
        return $ret;
    }
}


