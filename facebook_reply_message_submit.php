<?php
session_start();
include("config.php");
require("lib/facebook.php");

include("facebook_init.php");
include("facebook_function.php");

try{
	ReplyMessage($_SESSION['facebook_access_token'], $_POST['post_id'], urldecode($_POST['message']), $facebook);
} catch(FacebookApiException $e){
	echo "<pre>";print_r($e);echo "</pre>";
}

//header("Location: index.php");
/*echo "<script>self.location.href='index.php';</script>";*/
?>