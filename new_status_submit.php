<?php
session_start();
include("config.php");

//facebook lib
require("lib/facebook.php");

//twitter lib
require("lib/twitter/EpiCurl.php");
require("lib/twitter/EpiOAuth.php");
require("lib/twitter/EpiTwitter.php");

include("facebook_init.php");
include("facebook_function.php");

$twitter = new EpiTwitter($_SESSION['ck'], $_SESSION['cs'], $_SESSION['ot'], $_SESSION['ots']);

include("twitter_function.php");

$fb = NULL;
$tw = NULL;

if(is_array($_POST['db']))
{
	$db_count = count($_POST['db']);
	for($i = 0; $i < $db_count; $i++)
	{
		if($_POST['db'][$i] == "facebook")	$fb = TRUE;
		elseif($_POST['db'][$i] == "twitter")	$tw = TRUE;
	}
}
else
{
	if($_POST['db'][0] == "facebook")	$fb = TRUE;
	elseif($_POST['db'][0] == "twitter")	$tw = TRUE;
}

if($_POST['type'][0] == "status")
{
	if($fb)	CreateNewStatus($_SESSION['facebook_access_token'], urldecode($_POST['message']), $facebook);
	if($tw)	UpdateStatus($twitter, urldecode($_POST['message']));
}
else	//type = photo
{
	//upload photos to facebook
	if(urldecode($_POST['facebook_albums']) == "new_albums")
	{
		$album_id = CreateNewAlbums($_SESSION['facebook_access_token'], urldecode($_POST['album_name']), urldecode($_POST['album_description']), $facebook);//return new album id
		UploadPhotosToAlbum($_SESSION['facebook_access_token'], $album_id['id'], urldecode($_POST['photo_caption']), urldecode($_POST['photo_name']), $facebook);
	}
	else
	{
		UploadPhotosToAlbum($_SESSION['facebook_access_token'], urldecode($_POST['facebook_albums']), urldecode($_POST['photo_caption']), urldecode($_POST['photo_name']), $facebook);
	}
	unlink(urldecode($_POST['photo_name']));	// delete temporary files
}
?>