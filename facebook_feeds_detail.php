<?php
ini_set("display_errors", "off");
session_start();
include("php_function.php");
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
?>
<div id="feeds-content">
<?php
(isset($_POST['offset']) && $_POST['offset'] > 0) ? $offset = $_POST['offset'] : $offset = 0;
(isset($_POST['post_id'])) ? $post_id = $_POST['post_id'] : $post_id = "";
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/".$post_id."/comments?locale=id_ID&offset=".$offset."&limit=4&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$comments = json_decode(file_get_contents("https://graph.facebook.com/".$post_id."/comments?locale=id_ID&offset=".$offset."&limit=4&access_token=".$access_token));

if(empty($comments->data))	//if empty data, return to first page
{
	$offset = 0;
	$comments = json_decode(file_get_contents("https://graph.facebook.com/".$post_id."/comments?locale=id_ID&offset=".$offset."&limit=4&access_token=".$access_token));
}
$total_comments = count($comments->data);

for($i = 0; $i < $total_comments; $i++)
{
	$id = $comments->data[$i]->id;
	$from_name = $comments->data[$i]->from->name;
	$from_id = $comments->data[$i]->from->id;
	$message = $comments->data[$i]->message;
	(isset($comments->data[$i]->likes->count)) ? $like_this = $comments->data[$i]->likes->count : $like_this = 0;
	
	//link to user profile
	if ($i == 0) echo "<div class=\"fb_box first\">";
	else echo "<div class=\"fb_box\">";
	
	//profile pict
	echo "<a href=\"http://www.facebook.com/profile.php?id=".$from_id."\" target=\"_new\">";
	$profile_pict = "https://graph.facebook.com/".$from_id."/picture?type=square&access_token=".$access_token;
	echo "<img class=\"fb_pic\" src=\"".$profile_pict."\" height=\"50\" width=\"50\" />";
	echo "</a>";
	
	//name
	echo "<div class=\"content\">";
	echo "<div class=\"message\">";
	echo "<a href=\"http://www.facebook.com/profile.php?id=".$from_id."\" target=\"_new\">";
	echo "<h3>".$from_name."</h3>";                
	echo "</a>";

	//likes comments	
	$FQL = "SELECT likes FROM stream WHERE post_id = '".$post_id."'";
	$user_likes = json_decode(file_get_contents("https://graph.facebook.com/fql?access_token=".$access_token."&locale=id_ID&q=".urlencode($FQL)));
	
	if($user_likes->data[0]->likes->user_likes == 1)	//user already likes status, print "Tidak Suka"
		$print_likes = "<li><a class=\"unlike\" id=\"like-this".$i."\" onclick=\"unlikeComment('".$id."', 'like-this".$i."', 'likes-span".$i."');\">Tidak Suka</a></li>";
	else	//vice versa, print "Suka"
		$print_likes = "<li><a class=\"like\" id=\"like-this".$i."\" onclick=\"likeComment('".$id."', 'like-this".$i."', 'likes-span".$i."');\">Suka</a></li>";
	
	echo "
	<ul class=\"action_box\">
	".$print_likes."
	</ul>
	";
	
	echo "<p>".$message."</p>";
	
	//time
	$time = $comments->data[$i]->created_time;
	$fb_datetime = new DateTime($time);
	$fb_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
	//echo "<cite>".nicetime($fb_datetime->format("d M Y H:i:s"))."</cite>";
	echo "<cite class=\"timeago\" title=\"".$time."\"></cite>";
	
	echo "<br /><cite><span id=\"likes-span".$i."\">".$like_this." suka</span></cite>";
	echo "</div>";	//closing tag div message
	echo "</div>";	//closing tag div content
	echo "</div>";	//closing tag div fb_box
}
?>
<div id="paging_box">
    <a id="first" class="link" onclick="changeViewCommentsPage('0', 'feeds-container', '<?php echo $post_id; ?>');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeViewCommentsPage('<?php echo ($offset-4); ?>', 'feeds-container', '<?php echo $post_id; ?>');"></a></li>
	        <li><a id="next" class="link" onclick="changeViewCommentsPage('<?php echo ($offset+4); ?>', 'feeds-container', '<?php echo $post_id; ?>');"></a></li>
		</ul>
    </div>
</div>

</div>