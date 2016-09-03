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
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

//limit
$limit = 4;

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/me/home?locale=id_ID&offset=".$offset."&limit=1&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$news_feed = json_decode(file_get_contents("https://graph.facebook.com/me/home?locale=id_ID&offset=".$offset."&limit=".$limit."&access_token=".$access_token));

if(empty($news_feed->data))	//if empty data, return to first page
{
	$offset = 0;
	$news_feed = json_decode(file_get_contents("https://graph.facebook.com/me/home?locale=id_ID&offset=".$offset."&limit=".$limit."&access_token=".$access_token));
}
$total_news_feed = count($news_feed->data);

for($i = 0; $i < $total_news_feed; $i++)
{
	$user = $news_feed->data[$i]->from->id;
	$post_id = $news_feed->data[$i]->id;
	$type = $news_feed->data[$i]->type;
	(isset($news_feed->data[$i]->likes->count)) ? $like_this = $news_feed->data[$i]->likes->count : $like_this = 0;
	
	if ($i == 0) echo "<div class=\"fb_box first noflow\">";
	else echo "<div class=\"fb_box noflow\">";
	
	echo "<a class=\"button_detail\" href=\"http://www.facebook.com/profile.php?id=".$user."\" target=\"_new\">";	//link to user profile
	$profile_pict = "https://graph.facebook.com/".$user."/picture?type=square&access_token=".$access_token;
	echo "<img class=\"fb_pic\" src=\"".$profile_pict."\" height=\"50\" width=\"50\" />";	//profile picture
	echo "</a>";
	
	echo "<div class=\"content\">";
	echo "<div class=\"message\">";
	echo "<a href=\"http://www.facebook.com/profile.php?id=".$user."\" target=\"_new\">";
	echo "<h3>".$news_feed->data[$i]->from->name."</h3>";	//name
	echo "</a>";
	
	//recipient, if any
	if(isset($news_feed->data[$i]->to))
	{
		$count_recipient = count($news_feed->data[$i]->to->data);
		for($j = 0; $j < $count_recipient; $j++)
		{
			if($j < 3)	//show only 3 recipient maximum
			{
				echo ($j == 0) ? "&nbsp;&gt;&nbsp;" : ", &nbsp;";
				echo "<a href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->to->data[$j]->id."\" target=\"_new\">";
				echo "<span>".$news_feed->data[$i]->to->data[$j]->name."</span>";
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
	
	if(isset($news_feed->data[$i]->actions))	//not all type of feeds can be commented/liked
	{
		$message_plugins = ""; $print_message = ""; $view_comment = "";
		
		//check if user already likes status
		$FQL = "SELECT likes FROM stream WHERE post_id = '".$post_id."'";
		$user_likes = json_decode(file_get_contents("https://graph.facebook.com/fql?access_token=".$access_token."&locale=id_ID&q=".urlencode($FQL)));
		
		if($user_likes->data[0]->likes->user_likes == 1)	//user already likes status, print "Tidak Suka"
			$print_likes = "<li><a class=\"unlike\" id=\"like-this".$i."\" onclick=\"unlikeComment('".$post_id."', 'like-this".$i."', 'likes-span".$i."');\">Tidak Suka</a></li>";
		else	//vice versa, print "Suka"
			$print_likes = "<li><a class=\"like\" id=\"like-this".$i."\" onclick=\"likeComment('".$post_id."', 'like-this".$i."', 'likes-span".$i."');\">Suka</a></li>";
		
		//check user type (user, page, group, etc), if not user, we can't send message to them
		$user_type = json_decode(file_get_contents("https://graph.facebook.com/".$user."?metadata=1&access_token=".$access_token));
		if($user_type->type == "user")
		{
			$message_plugins = "<li><fb:send href=\"".$_SESSION['baseurl']."\" font=\"tahoma\"></fb:send></li>";
			$print_message = "<li style=\"display:none\"><a class=\"b_message\" id=\"send-message".$i."\" onclick=\"sendFacebookMessage('".$user."', '".urlencode($news_feed->data[$i]->from->name)."');\">Pesan</a></li>";
		}
		
		//check comments count, if > 0, then print 'Lihat Komentar'
		if($news_feed->data[$i]->comments->count > 0)
		{
			$view_comment = "<li><a class=\"b_viewcomment\" id=\"view-comment".$i."\" onclick=\"viewComments('".$post_id."', 'feeds-container');\">Lihat Komentar</a></li>";
		}
		
		echo "<ul class=\"action_box\">
		<li><a class=\"b_comment\" id=\"comment-box".$i."\" onclick=\"replyFacebookComment('".$post_id."', 'comment-box".$i."', 'comment-span".$i."');\">Komentar</a></li>
		".$print_likes."
		".$print_message."
		</ul>";
	}
	
	$array_story = explode("_", $post_id);
	$max_length = 140;	//max character to show
	if(isset($news_feed->data[$i]->message))
	{
		$message = $news_feed->data[$i]->message;
		if(strlen($message) > $max_length)
			$message = print_message($message, $max_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
	}
	elseif(isset($news_feed->data[$i]->story))
	{
		$message = $news_feed->data[$i]->story;
		if(strlen($message) > $max_length)
			$message = print_message($message, $max_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
	}
	else
	{
		isset($news_feed->data[$i]->caption) ? $message = $news_feed->data[$i]->caption : $message = $news_feed->data[$i]->name;
		if(strlen($message) > $max_length)
			$message = print_message($message, $max_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
	}
	
	echo "<p>".$message."</p>";
	
	//limit description length
	$max_description_length = 175;
	isset($news_feed->data[$i]->description) ? $description = $news_feed->data[$i]->description : $description = "";
	if(strlen($description) > $max_description_length)
		$description = print_message($description, $max_description_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
	
	//type
	echo "<div class=\"more\">";
	if($type == "status")
	{
		//(isset($news_feed->data[$i]->message)) ? $message = $news_feed->data[$i]->message : $message = $news_feed->data[$i]->story;
		//echo "<p>".$message."</p>";
	}
	elseif($type == "link")
	{
		//(isset($news_feed->data[$i]->message)) ? $message = $news_feed->data[$i]->message : $message = $news_feed->data[$i]->story;
		//echo "<p>".$message."</p>";
		echo "<a href=\"".$news_feed->data[$i]->link."\" target=\"_new\">";
		echo (isset($news_feed->data[$i]->picture)) ? "<p><img src=\"".$news_feed->data[$i]->picture."\" title=\"".$news_feed->data[$i]->caption."\" /></p>" : "";
		echo "<p>".$news_feed->data[$i]->name."</p></a>";
		//echo "<p>".$news_feed->data[$i]->description."</p>";
		echo "<p>".$description."</p>";
	}
	elseif($type == "photo")
	{
		//(isset($news_feed->data[$i]->message)) ? $message = $news_feed->data[$i]->message : $message = $news_feed->data[$i]->story;
		//echo "<p>".$message."</p>";
		echo (isset($news_feed->data[$i]->picture)) ? "<p><img src=\"".$news_feed->data[$i]->picture."\" /></p>" : "<p><img src=\"https://graph.facebook.com/".$news_feed->data[$i]->object_id."/picture?type=album&access_token=".$access_token."\" /></p>";
		echo "<p><a href=\"".$news_feed->data[$i]->link."\" target=\"_new\" title=\"".$news_feed->data[$i]->caption."\">".$news_feed->data[$i]->name."</a></p>";
		//echo "<p>".$news_feed->data[$i]->description."</p>";
		echo "<p>".$description."</p>";
	}
	elseif($type == "video")
	{
		//(isset($news_feed->data[$i]->message)) ? $message = $news_feed->data[$i]->message : $message = $news_feed->data[$i]->story;
		//echo "<p>".$message."</p>";
		echo (isset($news_feed->data[$i]->picture)) ? "<p><img src=\"".$news_feed->data[$i]->picture."\" /></p>" : "<p><img src=\"https://graph.facebook.com/".$news_feed->data[$i]->object_id."/picture?type=album&access_token=".$access_token."\" /></p>";
		echo "<p><a href=\"".$news_feed->data[$i]->link."\" target=\"_new\" title=\"".$news_feed->data[$i]->caption."\">".$news_feed->data[$i]->name."</a></p>";
		//echo "<p>".$news_feed->data[$i]->description."</p>";
		echo "<p>".$description."</p>";
	}
	else
	{
		//(isset($news_feed->data[$i]->message)) ? $message = $news_feed->data[$i]->message : $message = $news_feed->data[$i]->story;
		//echo "<p>".$message."</p>";
		echo "<p>".$description."</p>";
	}
	
	//time
	$time = $news_feed->data[$i]->created_time;
	$fb_datetime = new DateTime($time);
	$fb_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
	//echo "<cite>".nicetime($fb_datetime->format("d M Y H:i:s"))."</cite>";
	echo "<cite class=\"timeago\" title=\"".$time."\"></cite>";
	
	//actions
	if(isset($news_feed->data[$i]->actions))
	{
		echo "<br /><cite><span id=\"comment-span".$i."\">".$news_feed->data[$i]->comments->count." komentar</span>";
		echo " | <span id=\"likes-span".$i."\">".$like_this." suka</span>";
		echo "</cite>";
	}
		
	echo "</div>"; // end of div 'more'
	echo "</div>"; // end of div 'message'
	
	$max_comment_length = 80;
	if($type == "status")
	{
		if(isset($news_feed->data[$i]->actions))
		{
			//comments detail
			echo "<div class=\"comments\">";
			
			//show some user comments
			if($news_feed->data[$i]->comments->count > 0)
			{
				echo "<ul>";
				for($k = 0; $k < count($news_feed->data[$i]->comments->data) && $k < 3; $k++) {	//show max 3 comments
					//limit comment length
					isset($news_feed->data[$i]->comments->data[$k]->message) ? $comment = $news_feed->data[$i]->comments->data[$k]->message : $comment = "";
					if(strlen($comment) > $max_comment_length)	{
						$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
					}
					
					echo "
					<li>
						<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->id."\" target=\"_new\">
							<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$news_feed->data[$i]->comments->data[$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
						</a>
						<div class=\"comment\">
						<h3 class=\"name\">
							<a href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->from->id."\" target=\"_new\">".$news_feed->data[$i]->comments->data[$k]->from->name."</a>
						</h3><br />
						<code>".$comment."</code><br />
						<cite class=\"timeago\" title=\"".$news_feed->data[$i]->comments->data[$k]->created_time."\"></cite>
						</div>
					</li>";
				}
				echo "</ul>";
			}
			
			echo "</div>";	//end of comments
		}
	}
	elseif($type == "link")
	{
		if(isset($news_feed->data[$i]->actions))
		{
			//comments detail
			echo "<div class=\"comments\">";
			
			//show some user comments
			if($news_feed->data[$i]->comments->count > 0)
			{
				echo "<ul>";
				for($k = 0; $k < count($news_feed->data[$i]->comments->data) && $k < 3; $k++) {	//show max 3 comments
					//limit comment length
					isset($news_feed->data[$i]->comments->data[$k]->message) ? $comment = $news_feed->data[$i]->comments->data[$k]->message : $comment = "";
					if(strlen($comment) > $max_comment_length)	{
						$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
					}
					
					echo "
					<li>
						<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->id."\" target=\"_new\">
							<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$news_feed->data[$i]->comments->data[$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
						</a>
						<div class=\"comment\">
						<h3 class=\"name\">
							<a href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->from->id."\" target=\"_new\">".$news_feed->data[$i]->comments->data[$k]->from->name."</a>
						</h3><br />
						<code>".$comment."</code><br />
						<cite class=\"timeago\" title=\"".$news_feed->data[$i]->comments->data[$k]->created_time."\"></cite>
						</div>
					</li>";
				}
				echo "</ul>";
			}
			
			echo "</div>";	//end of comments
		}
	}
	elseif($type == "photo")
	{
		if(isset($news_feed->data[$i]->actions))
		{
			//comments detail
			echo "<div class=\"comments\">";
			
			//show some user comments
			if($news_feed->data[$i]->comments->count > 0)
			{
				echo "<ul>";
				for($k = 0; $k < count($news_feed->data[$i]->comments->data) && $k < 3; $k++) {	//show max 3 comments
					//limit comment length
					isset($news_feed->data[$i]->comments->data[$k]->message) ? $comment = $news_feed->data[$i]->comments->data[$k]->message : $comment = "";
					if(strlen($comment) > $max_comment_length)	{
						$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
					}
					
					echo "
					<li>
						<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->id."\" target=\"_new\">
							<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$news_feed->data[$i]->comments->data[$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
						</a>
						<div class=\"comment\">
						<h3 class=\"name\">
							<a href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->from->id."\" target=\"_new\">".$news_feed->data[$i]->comments->data[$k]->from->name."</a>
						</h3><br />
						<code>".$comment."</code><br />
						<cite class=\"timeago\" title=\"".$news_feed->data[$i]->comments->data[$k]->created_time."\"></cite>
						</div>
					</li>";
				}
				echo "</ul>";
			}
			
			echo "</div>";	//end of comments
		}
	}
	elseif($type == "video")
	{
		if(isset($news_feed->data[$i]->actions))
		{
			//comments detail
			echo "<div class=\"comments\">";
			
			//show some user comments
			if($news_feed->data[$i]->comments->count > 0)
			{
				echo "<ul>";
				for($k = 0; $k < count($news_feed->data[$i]->comments->data) && $k < 3; $k++) {	//show max 3 comments
					//limit comment length
					isset($news_feed->data[$i]->comments->data[$k]->message) ? $comment = $news_feed->data[$i]->comments->data[$k]->message : $comment = "";
					if(strlen($comment) > $max_comment_length)	{
						$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
					}
					
					echo "
					<li>
						<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->id."\" target=\"_new\">
							<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$news_feed->data[$i]->comments->data[$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
						</a>
						<div class=\"comment\">
						<h3 class=\"name\">
							<a href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->from->id."\" target=\"_new\">".$news_feed->data[$i]->comments->data[$k]->from->name."</a>
						</h3><br />
						<code>".$comment."</code><br />
						<cite class=\"timeago\" title=\"".$news_feed->data[$i]->comments->data[$k]->created_time."\"></cite>
						</div>
					</li>";
				}
				echo "</ul>";
			}
			
			echo "</div>";	//end of comments
		}
	}
	else	//other type
	{
		if(isset($news_feed->data[$i]->actions))
		{
			//comments detail
			echo "<div class=\"comments\">";
			
			//show some user comments
			if($news_feed->data[$i]->comments->count > 0)
			{
				echo "<ul>";
				for($k = 0; $k < count($news_feed->data[$i]->comments->data) && $k < 3; $k++) {	//show max 3 comments
					//limit comment length
					isset($news_feed->data[$i]->comments->data[$k]->message) ? $comment = $news_feed->data[$i]->comments->data[$k]->message : $comment = "";
					if(strlen($comment) > $max_comment_length)	{
						$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
					}
					
					echo "
					<li>
						<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->id."\" target=\"_new\">
							<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$news_feed->data[$i]->comments->data[$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
						</a>
						<div class=\"comment\">
						<h3 class=\"name\">
							<a href=\"http://www.facebook.com/profile.php?id=".$news_feed->data[$i]->comments->data[$k]->from->id."\" target=\"_new\">".$news_feed->data[$i]->comments->data[$k]->from->name."</a>
						</h3><br />
						<code>".$comment."</code><br />
						<cite class=\"timeago\" title=\"".$news_feed->data[$i]->comments->data[$k]->created_time."\"></cite>
						</div>
					</li>";
				}
				echo "</ul>";
			}
			
			echo "</div>";	//end of comments
		}
	}
	
	echo "</div>";  //closing tag div content
	//echo "<a class=\"button_detail right\"></a>";
	echo "</div>";	//closing tag div fb_box
}
?>
<div id="paging_box">
    <a id="first" class="link" onclick="changeFeedsPage('0', 'feeds-container');"></a>
    <div id="paging_button">
        <ul>
        	<li><a id="prev" class="link" onclick="changeFeedsPage('<?php echo ($offset-$limit); ?>', 'feeds-container');"></a></li>
	        <li><a id="next" class="link" onclick="changeFeedsPage('<?php echo ($offset+$limit); ?>', 'feeds-container');"></a></li>
		</ul>
    </div>
</div>

</div>