<?php
session_start();

if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}

include("config.php");
require("lib/facebook.php");

include("facebook_init.php");
include("facebook_function.php");

try{
	LikePost($_SESSION['facebook_access_token'], $_POST['post_id'], $facebook);
	$like_count = $facebook->api("/".$_POST['post_id'], "GET");
	echo (isset($like_count['likes']['count'])) ? $like_count['likes']['count'] : "0";
} catch(FacebookApiException $e){
	$loginUrl = $facebook->getLoginUrl(array(
		//"redirect_uri"	=>	$BASEURL,
		"display"		=>	"popup", 
		"scope"			=>	"read_stream,publish_stream,read_mailbox,manage_notifications,user_photos,user_birthday,user_videos,friends_photos,friends_videos"
	));
	echo "<script>self.location.href='".$loginUrl."'</script>";
}
?>