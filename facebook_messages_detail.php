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
(isset($_POST['post_id'])) ? $post_id = $_POST['post_id'] : $post_id = "";
(isset($_POST['offset']) && $_POST['offset'] > 0) ? $offset = $_POST['offset'] : $offset = 0;
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

//limit
$limit = 4;

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/".$post_id."/comments?locale=id_ID&offset=".$offset."&limit=1&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$conversation = json_decode(file_get_contents("https://graph.facebook.com/".$post_id."/comments?locale=id_ID&offset=".$offset."&limit=".$limit."&access_token=".$access_token));

if(empty($conversation->data))	//if empty conversation, return to first page
{
	$offset = 0;
	$conversation = json_decode(file_get_contents("https://graph.facebook.com/".$post_id."/comments?locale=id_ID&offset=".$offset."&limit=".$limit."&access_token=".$access_token));
}
$total_conversation = count($conversation->data);

for($i = 0; $i < $total_conversation; $i++)
{
	$link = $conversation->data[$i]->from->id;
	
	if ($i == 0) echo "<div class=\"fb_box first\">";
	else echo "<div class=\"fb_box\">";
	
	echo "<a href=\"http://www.facebook.com/profile.php?id=".$link."\" target=\"_new\">";	//link to user profile
	$profile_pict = "https://graph.facebook.com/".$link."/picture?type=small";
	echo "<img class=\"fb_pic\" src=\"".$profile_pict."\" height=\"50\" width=\"50\" />";	//profile pict
	echo "</a>";
	
	echo "<div class=\"content\">";
	echo "<div class=\"message\">";
	echo "<a href=\"http://www.facebook.com/profile.php?id=".$link."\" target=\"_new\">";
	echo "<h3>".$conversation->data[$i]->from->name."</h3>";	//name
	echo "</a>";
	
	echo "<ul class=\"action_box\">";
	echo "<li style=\"display:none\"><a class=\"b_message\" id=\"reply-message".$i."\" onclick=\"replyFacebookMessage('".$post_id."');\">Pesan</a></li>
	</ul>";
	
	$max_length = 140;
	echo (strlen($conversation->data[$i]->message) > $max_length) ? "<p>".print_message($conversation->data[$i]->message, $max_length)." ...</p>" : "<p>".$conversation->data[$i]->message."</p>";
	
	//time
	$time = $conversation->data[$i]->created_time;
	$fb_datetime = new DateTime($time);
	$fb_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
	//echo "<cite>".nicetime($fb_datetime->format("d M Y H:i:s"))."</cite>";
	echo "<cite class=\"timeago\" title=\"".$time."\"></cite>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
}
?>
<div id="paging_box">
    <a id="first" class="link" onclick="changeDetailMessagePage('0', 'content', '<?php echo $post_id; ?>');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeDetailMessagePage('<?php echo ($offset-$limit); ?>', 'content', '<?php echo $post_id; ?>');"></a></li>
	        <li><a id="next" class="link" onclick="changeDetailMessagePage('<?php echo ($offset+$limit); ?>', 'content', '<?php echo $post_id; ?>');"></a></li>
		</ul>
    </div>
</div>

</div>