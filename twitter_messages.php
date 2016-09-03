<?php
//twitter lib
require("lib/twitter/EpiCurl.php");
require("lib/twitter/EpiOAuth.php");
require("lib/twitter/EpiTwitter.php");

session_start(); 

if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
$twitterInit = new EpiTwitter($_SESSION['ck'], $_SESSION['cs'], $_SESSION['ot'], $_SESSION['ots']);

include("php_function.php");
include("twitter_authenticate_session.php");
?>
<div id="messages-container">
<?php
(isset($_POST['page']) && $_POST['page'] > 0) ? $page = $_POST['page'] : $page = 1;

//limit
$limit = 4;

$messages = $twitterInit->get('/direct_messages.json', array('count' => $limit, 'page' => $page));
$total_messages = count($messages->response);
$messages->response;

if(empty($messages->response))
{
	$page = 1;
	$messages = $twitterInit->get('/direct_messages.json', array('count' => $limit, 'page' => $page));
	$total_messages = count($messages->response);
	$messages->response;
}

for($i = 0; $i < $total_messages; $i++)
{
	$sender = $messages[$i]['sender'];
	$recipient = $messages[$i]['recipient'];
	
	if ($i == 0) echo "<div class=\"fb_box first\">";
	else echo "<div class=\"fb_box\">";
	
	//sender information
	echo "<a href=\"http://www.twitter.com/".$sender['screen_name']."\" target=\"_new\">";
	echo "<img class=\"fb_pic\" src=\"".$sender['profile_image_url']."\" />";
	echo "</a>";
	
	echo "<div class=\"content\">";
	echo "<div class=\"message\">";
	echo "<a href=\"http://www.twitter.com/".$sender['screen_name']."\" target=\"_new\">";
	echo "<h3>".$sender['name']."</h3>";
	echo "</a>";
	
	echo "
	<ul class=\"action_box\">
	<li><a class=\"b_message\" id=\"twitter-message".$i."\" onclick=\"replyMessage('".$sender['id']."', '".$sender['screen_name']."', '".$i."');\">Pesan</a></li>
	</ul>
	";
	
	echo "<p>".$messages[$i]['text']."</p>";
	
	$time = $messages[$i]['created_at'];
	$twitter_time = new DateTime($time);
	$twitter_time->setTimezone(new DateTimeZone(date_default_timezone_get()));
	//echo "<cite>".nicetime($twitter_time->format("d M Y H:i:s"))."</cite>";
	echo "<cite class=\"timeago\" title=\"".date("c", strtotime($time))."\"></cite>";
	echo "</div>";	//end tag div class message
	echo "</div>";	//end tag div class content
	echo "</div>";	//end tag div class twitter_box
}
?>

<div id="paging_box">
    <a id="first" class="link" onclick="changeTwitterMessagesPage('1', 'content');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeTwitterMessagesPage('<?php echo ($page-1); ?>', 'content');"></a></li>
	        <li><a id="next" class="link" onclick="changeTwitterMessagesPage('<?php echo ($page+1); ?>', 'content');"></a></li>
		</ul>
    </div>
</div>

</div>
