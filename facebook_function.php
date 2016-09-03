<?php
//if (!defined('SpeedUpSocial')) { die ("Direct access not premitted"); }

function LikePost($access_token, $post_id, $facebook)
{
	$facebook->api('/'.$post_id.'/likes', "POST");	//like post using api
}

function UnlikePost($access_token, $post_id, $facebook)
{
	$facebook->api('/'.$post_id.'/likes', "DELETE");	//unlike post using api
}

function PostComment($access_token, $post_id, $message, $facebook)
{
	$params = array(
		'message'	=> $message
	);
	$facebook->api('/'.$post_id.'/comments', "POST", $params);	//create comments using facebook api
}

function DeleteComment($access_token, $post_id, $facebook)
{
	$facebook->api('/'.$post_id, "DELETE");	//delete comments using facebook api
}

function CreateNewStatus($access_token, $message, $facebook)
{
	$params = array(
		'message'	=> $message
	);
	$facebook->api('/me/feed?access_token='.$access_token, "POST", $params);	//create status using facebook api
}

function DeleteStatus($access_token, $status_id, $facebook)
{
	$facebook->api('/'.$status_id, "DELETE");	//delete status using facebook api
}

function UploadPhotos($access_token, $message, $photo, $facebook)
{
	$facebook->setFileUploadSupport(true);
	$params = array(
		'message'	=>	$message,
		'source'	=>	'@'.realpath($photo)
	);
	$facebook->api('/me/photos?access_token='.$access_token, "POST", $params);	//upload photo using facebook api
}

function DeletePhotos($access_token, $photo_id, $facebook)
{
	$facebook->api('/'.$photo_id, "DELETE");	//delete photo using facebook api
}

/* New Function */

function UploadPhotosToAlbum($access_token, $album_id, $message, $photo, $facebook)
{
	$facebook->setFileUploadSupport(true);
	$params = array(
		'message'	=>	$message,
		'source'	=>	'@'.realpath($photo)
	);
	$facebook->api($album_id.'/photos?access_token='.$access_token, "POST", $params);	//upload photo using facebook api
}

function CreateNewAlbums($access_token, $album_name, $album_description, $facebook)
{
	$facebook->setFileUploadSupport(true);
	$params = array(
		'message'	=>	$album_description,
		'name'		=>	$album_name
	);
	return $facebook->api('/me/albums?access_token='.$access_token, "POST", $params);	//create new albums using facebook api, return album id (???)
}

function SendMessage($access_token, $destination_id, $message, $facebook)
{
	$params = array(
		"to"		=>	$destination_id, 
		"message"	=>	$message
	);
	$facebook->api("/me/inbox?access_token=".$access_token, "POST");
}

function ReplyMessage($access_token, $post_id, $message, $facebook)
{
	$params = array(
		"message"	=>	$message
	);
	$facebook->api("/".$post_id."/comments?access_token=".$access_token, "POST", $params);
}

function MarkNotificationsAsRead($access_token, $notifications_id, $facebook)
{
	//$facebook->api("/".$notifications_id."?unread=0&access_token=".$access_token, "POST");
	$url = "https://graph.facebook.com/".$notifications_id."/";
	$params = array(
		'access_token'	=> $access_token, 
		'unread'		=> 0
	);
	
	// set the target url
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_exec($ch);
	curl_close($ch);
}
?>