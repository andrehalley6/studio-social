<?php
ini_set("display_errors", "off");
session_start();
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
include("php_function.php");
?>
<div id="messages-container">
<?php
(isset($_POST['offset']) && $_POST['offset'] > 0) ? $offset = $_POST['offset'] : $offset = 0;
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

//limit
$limit = 4;

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/me/inbox?locale=id_ID&limit=1&offset=".$offset."&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$inbox = json_decode(file_get_contents("https://graph.facebook.com/me/inbox?locale=id_ID&limit=".$limit."&offset=".$offset."&access_token=".$access_token));

if(empty($inbox->data))	//if empty data, return to first page
{
	$offset = 0;
	$inbox = json_decode(file_get_contents("https://graph.facebook.com/me/inbox?locale=id_ID&limit=".$limit."&offset=".$offset."&access_token=".$access_token));
}

$total_inbox = count($inbox->data);

for($i = 0; $i < $total_inbox; $i++)
{
	$link = $inbox->data[$i]->from->id;
	$post_id = $inbox->data[$i]->id;
	
	//link to user profile
	if ($i == 0) echo "<div class=\"fb_box first\">";
	else echo "<div class=\"fb_box\">";
	
	//profile pict
	echo "<a href=\"http://www.facebook.com/profile.php?id=".$link."\" target=\"_new\">";
	$profile_pict = "https://graph.facebook.com/".$link."/picture?type=small&access_token=".$access_token;
	echo "<img class=\"fb_pic\" src=\"".$profile_pict."\" height=\"50\" width=\"50\" />";
	echo "</a>";
	
	//name
	echo "<div class=\"content\">";
	echo "<div class=\"message\">";	
	
	//recipient, if exist
	if(isset($inbox->data[$i]->to))
	{
		$count_recipient = count($inbox->data[$i]->to->data);
		for($j = 0; $j < $count_recipient; $j++)
		{
			if($j < 3)	//show only 3 recipient maximum
			{
				echo ($j == 0) ? "" : ", &nbsp;";
				echo "<a href=\"http://www.facebook.com/profile.php?id=".$inbox->data[$i]->to->data[$j]->id."\" target=\"_new\" >";
				echo "<span>".$inbox->data[$i]->to->data[$j]->name."</span>";
				echo "</a>";
			}
			else	//show rest of recipients as "dan X lainnya."
			{
				echo " <span>dan ".($count_recipient - ($j + 1))." lainnya.</span>";
				break;
			}
		}
		echo "<br />";
	}
	
	echo "<ul class=\"action_box\">";
	echo (isset($inbox->data[$i]->comments)) ? "<li><a class=\"conversation\" id=\"view-conversation\" onclick=\"viewConversation('".$post_id."', 'content');\">Percakapan</a></li>" : "";
	//echo "<li><fb:send href=\"".$_SESSION['baseurl']."\" font=\"tahoma\"></fb:send></li>";
	echo "<li style=\"display:none;\"><a class=\"b_message\" id=\"reply-message".$i."\" onclick=\"replyFacebookMessage('".$post_id."');\" style=\"display:none;\">Pesan</a></li>
	</ul>";
	
	$max_length = 140;
	$comment_message = "";

	if (isset($inbox->data[$i]->comments)) {
		$comment_count = count($inbox->data[$i]->comments->data);
		if ($_SESSION['facebook_name'] == $inbox->data[$i]->comments->data[$comment_count-1]->from->name) $comment_message = "&lt;- ";
		$comment_message .= $inbox->data[$i]->comments->data[$comment_count-1]->message;
	}
	else
	{
		$comment_message = $inbox->data[$i]->message;
	}
	
	echo (strlen($comment_message) > $max_length) ? "<p>". print_message($comment_message, $max_length)." ...</p>" : "<p>".$comment_message."</p>";
	
	//time
	$time = $inbox->data[$i]->updated_time;
	$fb_datetime = new DateTime($time);
	$fb_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
	//echo "<cite>".nicetime($fb_datetime->format("d M Y H:i:s"))."</cite>";
	echo "<cite class=\"timeago\" title=\"".$time."\"></cite>";
	echo "</div>";	//end tag div class message
	echo "</div>";	//end tag div class content
	echo "</div>";	//end tag div class twitter_box
}
?>

<div id="paging_box">
    <a id="first" class="link" onclick="changeMessagePage('0', 'content');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeMessagePage('<?php echo ($offset-$limit); ?>', 'content');"></a></li>
	        <li><a id="next" class="link" onclick="changeMessagePage('<?php echo ($offset+$limit); ?>', 'content');"></a></li>
		</ul>
    </div>
</div>

</div>
