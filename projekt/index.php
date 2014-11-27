<?php
require_once('src/controller/LoginController.php');
try{

    $loginResponse="";
    $registerResponse ="";
    $registrationSuccess = "";
    $mysqli = new mysqli("localhost", "admin", "", "projekt");
    $lControl = new Login($mysqli);
    @$characterList = $lControl->getAllCharacters($_COOKIE["UID"]);
    $posts = $lControl->getAllPosts();
    @$accInfo = $lControl->getAccountData($_COOKIE['UID']);

    if(isset($_POST["submitLogin"])) {
        $loginResponse = $lControl->loginHandler();
    } else if(isset($_POST["submitRegister"])) {
        $registerResponse = $lControl->loginHandler();
        if($registerResponse===true) {
            $registrationSuccess = "Account has been created! You may log into your account above.";
            $registerResponse = "";
        }
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
                    if(isset($_SESSION["logged_in"]))
                echo "
                    <p id='loggedinSq'>
                        Welcome back, <a href='?accountpage'>". @$_COOKIE['UID'] ."</a>!
                        <a href='?accountpage'>Account</a>,   <a href='src/controller/logout.php'>Logout</a>
                    </p>
                ";}
                ?>
            </div>


        </header><!--header end-->

        <main id="main-content">
            <main id="front-page">
            <?php
                //foreach($posts as $key in $value) sort array by id, last id first. post consists of a new article class="article_post and header.text=$title, main.text=$text, footer.text=$author, $date
            if(isset($_GET["createcharacter"])) {
            $mainContent["createcharacter"] = "
                <div id='accountPage'>
                    <form id='characterCreation' action='' method='post'>
                        <input type='text' name='playername' placeholder='playername'>
                        <select name='sex' id='sex'>
                            <option value='male'>male</option>
                            <option value='female'>female</option>
                        </select>
                        <select name='city' id='city'>
                            <option value='kalmar'>kalmar</option>
                            <option value='stockholm'>stockholm</option>
                        </select>
                        <button id='charactersubmit' name='charactersubmit'>Create</button>
                    </form>
                </div>
            ";}
            else if(isset($_GET["characterpage"])) {
                foreach ($characterList as $key){
                   var_dump($character[] = array_search($_GET["characterpage"], $key));
                }
                die(var_dump($character));

                $mainContent["characterpage"] = "
                <div id='characterPage'>
                    <p id='characterPage_title'>".$_GET["characterpage"][0]."</p>
                    <span id='Name' class='italic small gray'>Name: </span><span>" . $characterList[""]["playername"] . "</span><br>
                    <span id='Level' class='italic small gray'>Level: </span><span>" . $characterList[""]["level"] . "</span><br>
                    <span id='Sex' class='italic small gray'>Sex: </span><span>" . $characterList["sex"] . "</span><br>
                    <span id='Created' class='italic small gray'>Created: </span><span>" . $characterList["created"] . "</span><br>
                    <span id='Lastlogin' class='italic small gray'>Last login: </span><span>" . date("YmdGi", $characterList[""][""]) . "</span><br>
                </div>
                ";
                echo $mainContent["accountpage"];
            }
            else if(isset($_GET["accountpage"])) {
                $mainContent["accountpage"] = "
                <div id='accountPage'>
                    <p id='accountpage_title'>Account Page</p>
                    <span id='Username' class='italic small gray'>Username: </span><span>" . $accInfo['username'] . "</span><br>
                    <span id='E-mailaddress' class='italic small gray'>E-mail address: </span><span>" . $accInfo['email'] . "</span><br>
                    <span id='Created' class='italic small gray'>Created: </span><span>" . $accInfo['created'] . "</span><br>
                    <span id='Lastlogin' class='italic small gray'>Last login: </span><span>" . date("YmdGi", $accInfo['lastlogin']) . "</span><br>
                    <span id='Characters'><br><h3>Character List</h3>
                    <ul id='characterList' class='italic small'>";
                if (count($characterList) > 0)
                    foreach ($characterList as $key)
                        $mainContent["accountpage"] .= "<li><a class='white' href='?characterpage=".$key['playername']."'>". $key['playername'] . "</a> - level ".$key['level']."</li>";
                else
                    $mainContent["accountpage"] .= "<li class='italic small underline'>You have no characters</li>";
                    $mainContent["accountpage"] .= "</ul>
                    </span>
                    <p><br><br><a class='white small' href='?createCharacter'>Create New Character</a></p>
                </div>
                ";
                echo $mainContent["accountpage"];
            } else {
//                $mainContent["blogposts"]
                $posts = array_reverse($posts);
                foreach($posts as $key){
                    $str = "<article class='article_post'>";
                    $str .= "<header class='article_header'>";
                    $str .= $key['title'];
                    $str .= "</header>";
                    $str .= "<main class='article_body'>";
                    $str .= $key['text'];
                    $str .= "</main>";
                    $str .= "<footer class='article_footer'>";
                    $str .= $key['author'].", ".$key['created'];
                    $str .= "</footer>";
                    $str .= "</article>";
                    echo $str;
                }
            }?>

            </main>
            <?php
            if(isset($_POST["accountCreate"]) || isset($_POST["submitRegister"]))
                echo $mainContent["registerUser"] = "
                <div id='register-div'>
                    <form id='register-form' method='post' action=''>
                        <input type='text' name='register_username' placeholder='Username'>
                        <input type='password' name='register_password' placeholder='******'>
                        <input type='password' name='register_password2' placeholder='******'>
                        <input type='email' name='register_email' placeholder='email@adress.com'>
                        <button type='submit' name='submitRegister' id='submitRegister'>Register</button>
                            <div id='formError'><p>".$registerResponse."</p></div>
                            <div id='formSuccess'><p>".$registrationSuccess."</p></div>
                    </form>
                </div>
            ";
            ?>
        </main><!--main end-->
        <footer id="page-footer">
            <p>created by axel standar,<br>as223jc</p>
        </footer>
    </div><!--wrapper end-->
</body>
</html>