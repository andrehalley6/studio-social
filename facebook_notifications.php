<?php
ini_set("display_errors", "off");
session_start();
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
include("php_function.php");
include("facebook_function.php");
?>
<div id="notifications-container">
<?php
(isset($_POST['offset']) && $_POST['offset'] > 0) ? $offset = $_POST['offset'] : $offset = 0;
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

//limit
$limit = 5;

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/me/notifications?locale=id_ID&include_read=1&limit=1&offset=".$offset."&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$notifications = json_decode(file_get_contents("https://graph.facebook.com/me/notifications?locale=id_ID&include_read=1&limit=".$limit."&offset=".$offset."&access_token=".$access_token));

if(empty($notifications->data))	//if empty data, return to first page
{
	$offset = 0;
	$notifications = json_decode(file_get_contents("https://graph.facebook.com/me/notifications?locale=id_ID&include_read=1&limit=".$limit."&offset=".$offset."&access_token=".$access_token));
}

$total_notifications = count($notifications->data);
for($i = 0; $i < $total_notifications; $i++)
{
	if ($i == 0) echo "<div class=\"fb_box first notification\">";
	else echo "<div class=\"fb_box notification\">";
	
	echo "<div class=\"content\">";
	echo "<div class=\"message\">";
	
	echo "<a href=\"".$notifications->data[$i]->link."\" target=\"_new\"><p>".$notifications->data[$i]->title."</p></a>";
	echo "<div class=\"clear_both\"></div>";
	
	//time
	$time = $notifications->data[$i]->created_time;
	$fb_datetime = new DateTime($time);
	$fb_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
	//echo "<cite>".nicetime($fb_datetime->format("d M Y H:i:s"))."</cite>";
	echo "<cite class=\"timeago\" title=\"".$time."\"></cite>";
	echo "</div>";	//close tag message
	echo "</div>";	//close tag content
	echo "</div>";	//close tag fb_box
	
	if($notifications->data[$i]->unread == 1)
		MarkNotificationsAsRead($_SESSION['facebook_access_token'], $notifications->data[$i]->id, "");
}
?>
<div id="paging_box">
    <a id="first" class="link" onclick="changeNotificationsPage('0', 'content'); updateNotifications('notify-count');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeNotificationsPage('<?php echo ($offset-$limit); ?>', 'content'); updateNotifications('notify-count');"></a></li>
        	<li><a id="next" class="link" onclick="changeNotificationsPage('<?php echo ($offset+$limit); ?>', 'content'); updateNotifications('notify-count');"></a></li>
		</ul>
    </div>
</div>

</div>

