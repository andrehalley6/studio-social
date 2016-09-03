<?php
//twitter lib
require("lib/twitter/EpiCurl.php");
require("lib/twitter/EpiOAuth.php");
require("lib/twitter/EpiTwitter.php");

session_start(); 
$twitter = new EpiTwitter($_SESSION['ck'], $_SESSION['cs'], $_SESSION['ot'], $_SESSION['ots']);

include("twitter_function.php");

ComposeMessage($twitter, urldecode($_POST['text_message']), $_POST['user_id'], $_POST['screen_name']);
?>