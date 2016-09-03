<?php
function UpdateStatus($twitter, $message)
{
	$params = array(
		"status"	=>	$message
	);
	$twitter->post("/statuses/update.json", $params);
}

function ReplyTweet($twitter, $message, $reply_id)
{
	//reply tweet work same as tweet with @user on message
	$params = array(
		"status"				=>	$message, 
		"in_reply_to_status_id"	=>	$reply_id
	);
	$twitter->post("/statuses/update.json", $params);
}

function Retweet($twitter, $id)
{
	$twitter->post("/statuses/retweet/".$id.".json");
}

function ComposeMessage($twitter, $text, $id, $screen_name)
{
	if(!empty($id))	//post using id
		$twitter->post("/direct_messages/new.json", array("text" => $text, "user_id" => $id));
	elseif(!empty($screen_name))	//post using screen name
		$twitter->post("/direct_messages/new.json", array("text" => $text, "screen_name" => $screen_name));
}

/* New Functions */

function FriendshipsLookup($twitter, $screen_name)
{
	$params = array(
		"screen_name"	=>	$screen_name
	);
	return $twitter->get("/friendships/lookup.json", $params);
}

function SearchFriends($twitter, $key)
{
	$params = array(
		"q"			=>	urlencode($key), 
		"page"		=>	1, 
		"per_page"	=>	5
	);
	return $twitter->get("/users/search.json", $params);
}

function FriendsID($twitter, $screen_name)
{
	$params = array(
		"screen_name"	=>	$screen_name
	);
	return $twitter->get("/friends/ids.json", $params);
}

function FollowersID($twitter, $screen_name)
{
	$params = array(
		"screen_name"	=>	$screen_name
	);
	return $twitter->get("/followers/ids.json", $params);
}

function UserLookup($twitter, $screen_name)
{
	$array_friends = FollowersID($twitter, $screen_name);
	$array_friends->response;
	$data = array();
	foreach($array_friends['ids'] as $friends)
	{
		$user_data = $twitter->get("/users/lookup.json", array("user_id"	=>	$friends));
		$user_data->response;
		$data[] = array(
			"id"			=>	$user_data[0]['id'], 
			"label"			=>	$user_data[0]['screen_name'],
			"value"			=>	$user_data[0]['screen_name'], 
			"name"			=>	$user_data[0]['name'],
			"screen_name"	=>	$user_data[0]['screen_name'], 
			"image"			=>	$user_data[0]['profile_image_url']
		);
	}
	return $data;
}
?>