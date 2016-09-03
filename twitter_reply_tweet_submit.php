<?php
//twitter lib
require("lib/twitter/EpiCurl.php");
require("lib/twitter/EpiOAuth.php");
require("lib/twitter/EpiTwitter.php");

session_start(); 
$twitter = new EpiTwitter($_SESSION['ck'], $_SESSION['cs'], $_SESSION['ot'], $_SESSION['ots']);

include("twitter_function.php");
ReplyTweet($twitter, urldecode($_POST['reply_tweet']), $_POST['reply_id']);
?>