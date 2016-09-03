<?php
function renew_access_token($old_access_token, $old_code)
{
	include("config.php");
	//require("lib/facebook.php");
	//include("facebook_init.php");
	
	$app_id = $FB_APPID;
	$app_secret = $FB_SECRET; 
	$my_url = $BASEURL;
	 
	// known valid access token stored in a database 
	$access_token = $old_access_token;
	
	$code = $_REQUEST['code'];
	if(empty($code))	$code = $old_code;
	
	// If we get a code, it means that we have re-authed the user 
	//and can get a valid access_token. 
	if (isset($code)) {
		$token_url="https://graph.facebook.com/oauth/access_token?client_id="
		  . $app_id . "&redirect_uri=" . urlencode($my_url) 
		  . "&client_secret=" . $app_secret 
		  . "&code=" . $code . "&display=popup";
		//$response = file_get_contents($token_url);
		$response = curl_get_file_contents($token_url);
		//$response = callFb($token_url);
		//echo "<pre>"; print_r($response); echo "</pre>";
		$params = null;
		parse_str($response, $params);
		$access_token = $params['access_token'];
	}
		
	// Attempt to query the graph:
	$graph_url = "https://graph.facebook.com/me?"
		. "access_token=" . $access_token;
	$response = curl_get_file_contents($graph_url);
	$decoded_response = json_decode($response);
	
	//Check for errors 
	if ($decoded_response->error) {
		// check to see if this is an oAuth error:
		if ($decoded_response->error->type== "OAuthException") {
		  // Retrieving a valid access token. 
		  $dialog_url= "https://www.facebook.com/dialog/oauth?"
			. "client_id=" . $app_id 
			. "&redirect_uri=" . urlencode($my_url);
		  echo("<script> self.location.href='" . $dialog_url . "'</script>");
		  exit(0);
		  /*$loginUrl = $facebook->getLoginUrl(array(
			"redirect_uri"	=>	$BASEURL,
			"scope"			=>	"read_stream,publish_stream,read_mailbox,manage_notifications,user_photos,user_birthday,user_videos,friends_photos,friends_videos"
		  ));
		  echo("<script> self.location.href='" . $loginUrl . "'</script>");*/
		  //echo $decoded_response->error->message;
		}
		else {
		  $dialog_url= "https://www.facebook.com/dialog/oauth?"
			. "client_id=" . $app_id 
			. "&redirect_uri=" . urlencode($my_url);
		  echo("<script> self.location.href='" . $dialog_url . "'</script>");
		  exit(0);
		  //echo "other error has happened";
		  //return NULL;
		  /*$loginUrl = $facebook->getLoginUrl(array(
			"redirect_uri"	=>	$BASEURL,
			"scope"			=>	"read_stream,publish_stream,read_mailbox,manage_notifications,user_photos,user_birthday,user_videos,friends_photos,friends_videos"
		  ));
		  echo("<script> self.location.href='" . $loginUrl . "'</script>");*/
		}
	} 
	else {
		// success
		//echo("success" . $decoded_response->name)."<br />";
		//echo($old_access_token)."<br />";
		//echo($access_token)."<br />";
		return $access_token;
	}
}

// note this wrapper function exists in order to circumvent PHP's 
//strict obeying of HTTP error codes.  In this case, Facebook 
//returns error code 400 which PHP obeys and wipes out 
//the response.
function curl_get_file_contents($URL) {
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($c, CURLOPT_URL, $URL);
	$contents = curl_exec($c);
	$err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
	curl_close($c);
	if ($contents) return $contents;
	else return FALSE;
}

function callFb($url)
{
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true
	));
 
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
?>