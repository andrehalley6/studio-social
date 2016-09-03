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
<div id="feeds-content">
<?php
(isset($_POST['page']) && $_POST['page'] > 0) ? $page = $_POST['page'] : $page = 1;

//limit
$limit = 4;

$mentions = $twitterInit->get('/statuses/mentions.json', array('count' => $limit, 'page' => $page));
$total_mentions = count($mentions->response);
$mentions->response;

if(empty($mentions))
{
	$page = 1;
	$mentions = $twitterInit->get('/statuses/mentions.json', array('count' => $limit, 'page' => $page));
	$total_mentions = count($mentions->response);
	$mentions->response;
}

for($i = 0; $i < $total_mentions; $i++)
{
	$screen_name = $mentions[$i]['user']['screen_name'];
	if ($i == 0) echo "<div class=\"fb_box first\">";
	else echo "<div class=\"fb_box\">";
	
	echo "<a class=\"button_detail\" href=\"http://www.twitter.com/".$screen_name."\" target=\"_new\">";
	echo "<img class=\"fb_pic\" src=\"".$mentions[$i]['user']['profile_image_url']."\" />";	//profile picture
	echo "</a>";
	
	echo "<div class=\"content\">";
	echo "<div class=\"message\">";
	echo "<a href=\"http://www.twitter.com/".$screen_name."\" target=\"_new\">";
	echo "<h3>".$mentions[$i]['user']['name']."</h3>";	//name
	echo "</a>";
	
	echo "
	<ul class=\"action_box\">
	<li><a class=\"b_reply\" id=\"reply-tweet".$i."\" onclick=\"replyTweet('".$mentions[$i]['id']."', '".$screen_name."', '".$i."');\">Balas</a></li>
	</ul>
	";
	
	echo "<p>".$mentions[$i]['text']."</p>";
	
	$time = $mentions[$i]['created_at'];
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
    <a id="first" class="link" onclick="changeTwitterMentionsPage('1', 'feeds-container');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeTwitterMentionsPage('<?php echo ($page-1); ?>', 'feeds-container');"></a></li>
	        <li><a id="next" class="link" onclick="changeTwitterMentionsPage('<?php echo ($page+1); ?>', 'feeds-container');"></a></li>
		</ul>
    </div>
</div>

</div>