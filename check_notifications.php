<?php
ini_set("display_errors", "off");
session_start();
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}

(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/me/notifications?limit=1&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$notifications = json_decode(file_get_contents("https://graph.facebook.com/me/notifications?limit=1&access_token=".$access_token));
$new_notifications = isset($notifications->summary->unseen_count) ? $notifications->summary->unseen_count : 0;

echo $new_notifications;
?>