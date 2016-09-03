<?php
session_start();
include("config.php");

//facebook lib
require("lib/facebook.php");

include("facebook_init.php");
include("facebook_function.php");

try{
	SendMessage($_SESSION['facebook_access_token'], $_POST['id'], urldecode($_POST['message']), $facebook);
} catch(FacebookApiException $e) {
	echo "<pre>";print_r($e);echo "</pre>";
}

//header("Location: index.php");
/*echo "<script>self.location.href='index.php';</script>";*/
?>