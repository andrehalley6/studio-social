<?php
ini_set("display_errors", "off");
//twitter lib
require("lib/twitter/EpiCurl.php");
require("lib/twitter/EpiOAuth.php");
require("lib/twitter/EpiTwitter.php");

session_start();

if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}

include("php_function.php");

$twitterInit = new EpiTwitter($_SESSION['ck'], $_SESSION['cs'], $_SESSION['ot'], $_SESSION['ots']);

//Twitter session authentication
include("twitter_authenticate_session.php");

(isset($_POST['page']) && $_POST['page'] > 0) ? $page = $_POST['page'] : $page = 1;
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

$limit = 12;	//variable limit data for each social network, for twitter max limit = 200


//Facebook access token authentication
include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/me/inbox?locale=id_ID&limit=1&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}

$inbox = json_decode(file_get_contents("https://graph.facebook.com/me/inbox?locale=id_ID&limit=".$limit."&access_token=".$access_token));
foreach($inbox->data as $facebook_data)
{
	$fb_datetime = new DateTime($facebook_data->updated_time);
	$fb_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
	$facebook_inbox[] = array(
							"social"			=>	"facebook", 
							"post_id"			=>	$facebook_data->id, 
							"from_id"			=>	$facebook_data->from->id, 
							"from_name"			=>	$facebook_data->from->name, 
							"recipients"		=>	isset($facebook_data->to) ? $facebook_data->to : NULL, //contains object of recipients
							"message"			=>	$facebook_data->message, 
							"comments"			=>	isset($facebook_data->comments) ? $facebook_data->comments : NULL, 
							"date"				=>	nicetime($fb_datetime->format("d M Y H:i:s")), 
							"sort_date"			=>	$fb_datetime->format("U"), 
							"iso_date"			=>	$facebook_data->updated_time
							);
}

$messages = $twitterInit->get('/direct_messages.json', array('count' => $limit));
foreach($messages->response as $twitter_data)
{
	$twitter_time = new DateTime($twitter_data['created_at']);
	$twitter_time->setTimezone(new DateTimeZone(date_default_timezone_get()));
	
	$twitter_messages[] = array(
							"social"				=>	"twitter", 
							"sender_id"				=>	$twitter_data['sender']['id'], 
							"sender_screen_name"	=>	$twitter_data['sender']['screen_name'], 
							"sender_name"			=>	$twitter_data['sender']['name'], 
							"sender_picture"		=>	$twitter_data['sender']['profile_image_url'], 
							"recipient_id"			=>	$twitter_data['recipient']['id'], 
							"recipient_screen_name"	=>	$twitter_data['recipient']['screen_name'], 
							"recipient_name"		=>	$twitter_data['recipient']['name'], 
							"recipient_picture"		=>	$twitter_data['recipient']['profile_image_url'], 
							"text"					=>	$twitter_data['text'], 
							"date"					=>	nicetime($twitter_time->format("d M Y H:i:s")), 
							"sort_date"				=>	$twitter_time->format("U"), 
							"iso_date"				=>	date("c", strtotime($twitter_data['created_at']))
							);
}
$inbox_data = order_array_num(array_merge($facebook_inbox, $twitter_messages), "sort_date", "DESC");

//check data availability
$show_per_page = 4;
$start = ($page * $show_per_page) - ($show_per_page - 1);
if(empty($inbox_data[$start]))	//if array empty, return to first data
	$page = 1;
?>
<div id="messages-container">
<?php
for($i = ($page * $show_per_page) - ($show_per_page - 1), $k = 0; $i <= ($page * $show_per_page); $i++, $k++)
{
	if(!empty($inbox_data[$i]))
	{
		if($inbox_data[$i]['social'] == "facebook")	//facebook
		{
			if ($k == 0) echo "<div class=\"fb_box first\">";
			else echo "<div class=\"fb_box\">";
			
			//profile pict
			echo "<a href=\"http://www.facebook.com/profile.php?id=".$inbox_data[$i]['from_id']."\" target=\"_new\">";
			$profile_pict = "https://graph.facebook.com/".$inbox_data[$i]['from_id']."/picture?type=small&access_token=".$access_token;
			echo "<img class=\"fb_pic\" src=\"".$profile_pict."\" height=\"50\" width=\"50\" />";
			echo "</a>";
			
			//name
			echo "<div class=\"content\">";
			echo "<div class=\"message\">";
			
			//recipient, if exist
			if($inbox_data[$i]['recipients'])
			{
				$count_recipient = count($inbox_data[$i]['recipients']->data);
				for($j = 0; $j < $count_recipient; $j++)
				{
					if($j < 3)	//show only 3 recipient maximum
					{
						echo ($j == 0) ? "" : ", &nbsp;";
						echo "<a href=\"http://www.facebook.com/profile.php?id=".$inbox_data[$i]['recipients']->data[$j]->id."\" target=\"_new\">";
						echo "<span>".$inbox_data[$i]['recipients']->data[$j]->name."</span>";
						echo "</a>";
					}
					else	//show rest of recipients as "dan X lainnya."
					{
						echo " <span>dan ".($count_recipient - ($j + 1))." lainnya.</span>";
						break;
					}
				}
			}
			
			echo "<ul class=\"action_box\">";
			echo ($inbox_data[$i]['comments']) ? "<li><a id=\"view-conversation\" onclick=\"viewConversation('".$inbox_data[$i]['post_id']."', 'content');\">Percakapan</a></li>" : "";
			//echo "<li><fb:send href=\"".$_SESSION['baseurl']."\" font=\"tahoma\"></fb:send></li>";
			echo "<li style=\"display:none;\"><a id=\"reply-message".$i."\" onclick=\"replyFacebookMessage('".$inbox_data[$i]['post_id']."');\">Pesan</a></li>
			</ul>";
			
			$max_length = 140;
			$comment_message = "";
			if (isset($inbox_data[$i]['comments'])) {
				$comment_count = count($inbox_data[$i]['comments']->data);
				if ($_SESSION['facebook_name'] == $inbox_data[$i]['comments']->data[$comment_count-1]->from->name) $comment_message = "&lt;- ";
				$comment_message .= $inbox_data[$i]['comments']->data[$comment_count-1]->message;
			}
			else
			{
				$comment_message = $inbox_data[$i]['message'];
			}
			
			echo (strlen($comment_message) > $max_length) ?"<p>". print_message($comment_message, $max_length)." ...</p>" : "<p>".$comment_message."</p>";			
			
			echo "<cite class=\"timeago\" title=\"".$inbox_data[$i]['iso_date']."\"></cite>";
			echo "</div>";	//closing tag message
			echo "</div>";	//closing tag content
			echo "</div>";	//closing tag div fb_box
		}
		else	//twitter
		{
			if ($k == 0) echo "<div class=\"fb_box first\">";
			else echo "<div class=\"fb_box\">";
			
			//sender information
			echo "<a href=\"http://www.twitter.com/".$inbox_data[$i]['sender_screen_name']."\" target=\"_new\">";
			echo "<img class=\"fb_pic\" src=\"".$inbox_data[$i]['sender_picture']."\" />";
			echo "</a>";
			
			echo "<div class=\"content\">";
			echo "<div class=\"message\">";
			echo "<a href=\"http://www.twitter.com/".$inbox_data[$i]['sender_screen_name']."\" target=\"_new\">";
			echo "<h3>".$inbox_data[$i]['sender_name']."</h3>";
			echo "</a>";
			
			echo "
			<ul class=\"action_box\">
			<li><a class=\"b_message\" id=\"twitter-message".$i."\" onclick=\"replyMessage('".$inbox_data[$i]['sender_id']."', '".$inbox_data[$i]['sender_screen_name']."', '".$i."');\">Balas</a></li>
			</ul>
			";
			
			echo "<p>".$inbox_data[$i]['text']."</p>";
			
			echo "<cite class=\"timeago\" title=\"".$inbox_data[$i]['iso_date']."\"></cite>";
			echo "</div>";	//end tag div class message
			echo "</div>";	//end tag div class content
			echo "</div>";	//end tag div class twitter_box
		}
	}
}
?>

<div id="paging_box">
    <a id="first" class="link" onclick="changeAllMessagesPage('1', 'content');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeAllMessagesPage('<?php echo ($page-1); ?>', 'content');"></a></li>
	        <li><a id="next" class="link" onclick="changeAllMessagesPage('<?php echo ($page+1); ?>', 'content');"></a></li>
		</ul>
    </div>
</div>

</div>

