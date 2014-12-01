<?php
require_once('src/controller/LoginController.php');
try{

    $loginResponse="";
    $registerResponse ="";
    $registrationSuccess = "";
    $charResponse = "";
    $postResponse = "";

    //connection för MYSQLI
    $mysqli = new mysqli("localhost", "admin", "", "projekt");

    $lControl = new Login($mysqli);
    @$characterList = $lControl->getAllCharacters($_COOKIE["UID"]);
    $posts = $lControl->getAllPosts();
    @$accInfo = $lControl->getAccountData($_COOKIE['UID']);
    $character = null;

    if(isset($_GET["delete"])) {
        $lControl->deleteCharacterSuccessful($_SESSION["charD"]);
        header("location: ?accountpage");
    } else if(isset($_GET["postdel"])){

        if($lControl->postDeleted($posts[$_GET["index"]]["id"])){
            header("location: index.php");}
    }
    if(isset($_POST["submitLogin"])) {
        $loginResponse = $lControl->loginHandler();
    } else if(isset($_POST["submitRegister"])) {
        $registerResponse = $lControl->loginHandler();
        if($registerResponse===true) {
            $registrationSuccess = "Account has been created! You may log into your account above.";
            $registerResponse = "";
        }
    }else if(isset($_POST["charactersubmit"])){
        $charResponse = $lControl->characterCreated([
            "accountid"=>$accInfo["id"],
            "playername"=>$_POST["playername"],
            "sex"=>$_POST["sex"],
            "playerposition"=>$_POST["city"]
            ]);
        if($charResponse===true)
            header("location:?accountpage");
    } else if(isset($_POST["submitpost"])){
        $postResponse = $lControl->postCreated([
            "title"=>$_POST["newposttitle"],
            "text"=>$_POST["newposttext"],
            "author"=>$accInfo["username"]
        ]);
        if($postResponse)
            header("location:index.php");
    } else if(isset($_POST["submitedit"])){
        $editResponse = $lControl->postEdited([
            "id"=>$_POST["editpostid"],
            "title"=>$_POST["editposttitle"],
            "text"=>$_POST["editposttext"]
        ]);
        if($editResponse)
            header("location:index.php");

    }



}catch(Exception $ex){
    if($mysqli->connect_errno > 0)
        die('Unable to connect to database [' . $mysqli->connect_error . ']');
    echo $ex;
}
?>
<!doctype html>
<html lang="sv">
<head>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
<!--    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>-->
    <meta charset="UTF-8">
    <title>The System - as223jc</title>
    <link rel="stylesheet" href="style/style.css"/>
</head>
<body>
    <div id="wrapper">
        <header id="page-logo">
            <h1 id="page-logotype"><a href="index.php">Hatt</a></h1>
            <div id='login-div'>
                <?php
                if(!isset($_SESSION["logged_in"]))
                    echo $loginDiv = "
                <form id='login-form' method='post' action=''>
                    <input type='text' name='login_username' placeholder='Username'>
                    <input type='password' name='login_password' placeholder='******'>
                    <button type='submit' name='submitLogin' id='submitLogin'>Login</button>
                    <button type='submit' name='accountCreate' id='submitCreate'>Create account</button>
                    <div id='formError'><p>".$loginResponse."</p></div>
                </form>
                ";
                else{
                    if(isset($_SESSION["logged_in"])){
                        echo "
                    <p id='loggedinSq'>
                        Welcome back, <a href='?accountpage'>". @$_COOKIE['UID'] ."</a>!
                        <a href='?accountpage'>Account</a>,";
                        if($accInfo["accounttype"]===2)
                            echo "<a href='?createpost'>New Post</a>";
                        echo "
                             <a href='src/controller/logout.php'>Logout</a>
                    </p>
                ";}}
                ?>
            </div>


        </header><!--header end-->

        <main id="main-content">
            <main id="front-page">
            <?php
                //foreach($posts as $key in $value) sort array by id, last id first. post consists of a new article class="article_post and header.text=$title, main.text=$text, footer.text=$author, $date
            if(isset($_GET["createCharacter"])) {
            $mainContent["createCharacter"] = "
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
                    <div id='formError'><p>".$charResponse."</p></div>
                    </fieldset>
                </div>
            ";
                echo $mainContent["createCharacter"];
            }
            else if(isset($_GET["characterpage"])) {
                foreach ($characterList as $key){
                    if($key["playername"] === $_GET["characterpage"])
                        $character = $key;
                    $_SESSION["charD"] = $character["playername"];
                }

                $mainContent["characterpage"] = "
                <div id='characterPage'>
                <fieldset class='pagefieldset'>
                <legend>".$character["playername"]."</legend>
                    <span id='Name' class='italic small gray'>Name: </span><span>" . $character["playername"] . "</span><br>
                    <span id='Level' class='italic small gray'>Level: </span><span>" . $character["level"] . "</span><br>
                    <span id='Sex' class='italic small gray'>Sex: </span><span>" . $character["sex"] . "</span><br>
                    <span id='Created' class='italic small gray'>Created: </span><span>" . $character["created"] . "</span><br>
                    <span id='Lastlogin' class='italic small gray'>Last login: </span><span>" . date("YmdGi", $character["lastlogin"]) . "</span><br>


                    <p>
                    <br>
                    <a class='red small delchar' href='?delete' onclick='confirmClick()'>Delete</a>
                    <a class='white small returntoacc' href='?accountpage'>Back To Account</a>
                    </p>
                    </fieldset>
                </div>
                ";
                echo $mainContent["characterpage"];
            }
            else if(isset($_GET["accountpage"])) {
                $mainContent["accountpage"] = "
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
                        $mainContent["accountpage"] .= "<li  id='charlist'><a class='white' href='?characterpage=".$key['playername']."'>". $key['playername'] . "</a> - level ".$key['level']."</li><br>";
                else
                    $mainContent["accountpage"] .= "<li class='italic small underline'>You have no characters</li>";
                    $mainContent["accountpage"] .= "</ul>
                    </span>
                    <p><br><br><a class='white small' href='?createCharacter'>Create New Character</a></p>
                    </fieldset>
                </div>
                ";
                echo $mainContent["accountpage"];
            }else if($accInfo["accounttype"] === 2 && isset($_GET["createpost"])){
                echo $adminContent["createPost"] = "
               <div id='post-div'>
                    <fieldset class='pagefieldset'>
                        <legend>New Post</legend>
                        <form action='' method='post'>
                        <input type='text' name='newposttitle' id='newposttitle' placeholder='Enter a title'/>
                        <textarea name='newposttext' id='newposttext' cols='30' rows='10' placeholder='Type something..'></textarea>
                        <button name='submitpost' id='postButton'>Submit</button>
                        </form>
                    </fieldset>
                </div>
                ";
            } else if($accInfo["accounttype"] === 2 && isset($_GET["postedit"])){
                $adminContent["postedit"] = "
               <div id='post-div'>
                    <fieldset class='pagefieldset'>
                        <legend>Edit Post</legend>
                        <form action='' method='post'>
                        <input type='text' name='editposttitle' id='newposttitle' placeholder='Enter a title' value='";
                $adminContent["postedit"] .= $posts[($_GET["index"])]["title"];
                $adminContent["postedit"] .="'><textarea name='editposttext' id='newposttext' cols='30' rows='10' placeholder='Type something..'>";
                $adminContent["postedit"] .= $posts[$_GET["index"]]["text"];
                $adminContent["postedit"] .= "</textarea><input type='hidden' name='editpostid' value='".$_GET["postedit"]."'>
                        <button name='submitedit' id='postButton'>Submit Edit</button>
                        </form>
                    </fieldset>
                </div>
                ";
                echo $adminContent["postedit"];
            } else {
//                $mainContent["blogposts"]
                $posts = array_reverse($posts);
                if(count($posts)>0) {
                    $index = 0;
                    foreach ($posts as $key) {
                        $str = "<article class='article_post'>";
                        if ($accInfo["accounttype"] === 2) {
                            $str .= "<a title='Delete Post' class='small red' id='postx' href='?postdel=" . $posts[$index]["id"] . "&index=" . ((count($posts) - 1) - $index) . "'>x</a>";
                            $str .= "<a title='Edit Post' class='small blue' id='postx' href='?postedit=" . $posts[$index]["id"] . "&index=" . ((count($posts) - 1) - $index) . "'>o</a>";
                        }
                        $str .= "<header class='article_header'>";
                        $str .= $key['title'];
                        $str .= "</header>";
                        $str .= "<main class='article_body'>";
                        $str .= $key['text'];
                        $str .= "</main>";
                        $str .= "<footer class='article_footer'>";
                        $str .= $key['author'] . ", " . date("d-M-Y G:i", strtotime($key['created']));
                        $str .= "</footer>";
                        $str .= "</article>";
                        echo $str;
                        $index++;
                    }
                }else{
                    echo "<article class='article_post'><header class='article_header'>Välkommen</header><p id='exampletext'>Just nu finns det inga posts och detta är en exempeltext som kommer att ersättas när det finns inlägg</p></article>";
                }
            }?>

            </main>
            <?php
            if(isset($_POST["accountCreate"]) || isset($_POST["submitRegister"]))
                echo $mainContent["registerUser"] = "
                <div id='register-div'>
                <fieldset class='pagefieldset'>
                <legend>Create New Character</legend>
                    <form id='register-form' method='post' action=''>
                        <input type='text' name='register_username' placeholder='Username'>
                        <input type='password' name='register_password' placeholder='******'>
                        <input type='password' name='register_password2' placeholder='******'>
                        <input type='email' name='register_email' placeholder='email@adress.com'>
                        <button type='submit' name='submitRegister' id='submitRegister'>Register</button>
                            <div id='formError'><p>".$registerResponse."</p></div>
                            <div id='formSuccess'><p>".$registrationSuccess."</p></div>
                    </form>
                    </fieldset>
                </div>
            ";

            ?>
        </main><!--main end-->
        <footer id="page-footer">
            <p>created by axel standar,<br>as223jc</p>
        </footer>
    </div><!--wrapper end-->
<script type="application/javascript">
//    $('.delchar').click(function(){
//        if(confirm("Are you sure you want to delete your character?"))
//            return true;
//        return false;
//    })
</script>
</body>
</html>