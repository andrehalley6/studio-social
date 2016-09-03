<?php 
/*if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}*/

session_start();
include("config.php");

//twitter lib
require("lib/twitter/EpiCurl.php");
require("lib/twitter/EpiOAuth.php");
require("lib/twitter/EpiTwitter.php");;

$twitter = new EpiTwitter($_SESSION['ck'], $_SESSION['cs'], $_SESSION['ot'], $_SESSION['ots']);

include("twitter_function.php");

$friends_data = UserLookup($twitter, $_SESSION['twitter_screen_name']);
echo json_encode($friends_data);
?>