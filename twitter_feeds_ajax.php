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

include("php_function.php");
include("twitter_authenticate_session.php");
$twitterInit = new EpiTwitter($_SESSION['ck'], $_SESSION['cs'], $_SESSION['ot'], $_SESSION['ots']);
?>
<div id="feeds-content">
<?php
(isset($_POST['page']) && $_POST['page'] > 0) ? $page = $_POST['page'] : $page = 1;

//limit
$limit = 4;

$timeline = $twitterInit->get('/statuses/home_timeline.json', array('count' => $limit, 'page' => $page));
$total_timeline = count($timeline->response);
$home_timeline = $timeline->response;

if(empty($home_timeline))
{
	$page = 1;
	$timeline = $twitterInit->get('/statuses/home_timeline.json', array('count' => $limit, 'page' => $page));
	$total_timeline = count($timeline->response);
	$home_timeline = $timeline->response;
}

for($i = 0; $i < $total_timeline; $i++)
{
	$screen_name = $home_timeline[$i]['user']['screen_name'];
	if ($i == 0) echo "<div class=\"fb_box first\">";
	else echo "<div class=\"fb_box\">";
	
	//profile pic
	echo "<a class=\"button_detail\" href=\"http://www.twitter.com/".$screen_name."\" target=\"_new\">";
	echo "<img class=\"fb_pic\" src=\"".$home_timeline[$i]['user']['profile_image_url']."\" />";
	echo "</a>";
	
	echo "<div class=\"content\">";
	echo "<div class=\"message\">";
	echo "<a href=\"http://www.twitter.com/".$screen_name."\" target=\"_new\">";
	echo "<h3>".$home_timeline[$i]['user']['name']."</h3>";	//name
	echo "</a>";
	
	echo "
	<ul class=\"action_box\">
	<li><a class=\"b_reply\" id=\"reply-tweet".$i."\" onclick=\"replyTweet('".$home_timeline[$i]['user']['id']."', '".$screen_name."', '".$i."');\">Balas</a></li>
	<li><a class=\"b_retweet\" id=\"retweet".$i."\" onclick=\"retweet('".$home_timeline[$i]['id']."');\">Retweet</a></li>
	<li><a class=\"b_message\" id=\"compose-message".$i."\" onclick=\"replyMessage('".$home_timeline[$i]['user']['id']."', '".$screen_name."', '".$i."');\">Pesan</a></li>
	<li><a id=\"form-compose".$i."\" style=\"display:none;\"></a></li>
	</ul>
	";
	
	echo "<p>".$home_timeline[$i]['text']."</p>";
	
	$time = $home_timeline[$i]['created_at'];
	$twitter_time = new DateTime($time);
	$twitter_time->setTimezone(new DateTimeZone(date_default_timezone_get()));
	//echo "<cite>".nicetime($twitter_time->format("d M Y H:i:s"))."</cite>";
	echo "<cite class=\"timeago\" title=\"".date("r", strtotime($time))."\"></cite>";
	echo "</div>";	//end tag div class message
	echo "</div>";	//end tag div class content
	echo "</div>";	//end tag div class twitter_box
}
?>

<div id="paging_box">
    <a id="first" class="link" onclick="changeTwitterFeedsPage('1', 'feeds-container');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeTwitterFeedsPage('<?php echo ($page-1); ?>', 'feeds-container');"></a></li>
	        <li><a id="next" class="link" onclick="changeTwitterFeedsPage('<?php echo ($page+1); ?>', 'feeds-container');"></a></li>
		</ul>
    </div>
</div>

</div>