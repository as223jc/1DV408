<?php
session_start();
if(session_destroy()){
    unset($_SESSION["logged_in"]);

    unset($_COOKIE["UID"]);
    unset($_COOKIE["SID"]);
    setcookie("UID", "", time()-60*60*24*366, '/');
    setcookie("SID", "", time()-60*60*24*366, '/');

    header("Location: ../../index.php");
}

