<?php

class htmlPage {
    public function showHtml($body){
        echo '<!doctype html>
<html lang="sv">
<head>
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300" rel="stylesheet" type="text/css">
<!--    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>-->
<meta charset="UTF-8">
<title>The System - as223jc</title>
<link rel="stylesheet" href="style/style.css"/>
</head>
<body>
<div id="wrapper">
<header id="page-logo">
<h1 id="page-logotype"><a href="index.php">Hatt</a></h1>
<div id="login-div">
' . $body . '
</main><!--main end-->
<footer id="page-footer">
<p>created by axel standar,<br>as223jc</p>
</footer>
</div><!--wrapper end-->
<script type="application/javascript">
    //    $(".delchar").click(function(){
    //        if(confirm("Are you sure you want to delete your character?"))
    //            return true;
    //        return false;
    //    })
</script>
</body>
</html>';
    }
}
