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

$limit = 24;	//variable limit data for each social network, for twitter max limit = 200


//Facebook access token authentication
include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/me/home?locale=id_ID&limit=1&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}

$news_feed = json_decode(file_get_contents("https://graph.facebook.com/me/home?limit=".$limit."&locale=id_ID&access_token=".$access_token));
foreach($news_feed->data as $facebook_data)
{
	$fb_datetime = new DateTime($facebook_data->created_time);
	$fb_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
	$facebook_news_feed[] = array(
							"social"			=>	"facebook", 
							"post_id"			=>	$facebook_data->id, 
							"from_id"			=>	$facebook_data->from->id, 
							"from_name"			=>	$facebook_data->from->name, 
							"type"				=>	$facebook_data->type, 
							"actions"			=>	isset($facebook_data->actions) ? TRUE : FALSE, 
							"likes_count"		=>	isset($facebook_data->likes->count) ? $facebook_data->likes->count : 0, 
							"likes_data"		=>	isset($facebook_data->likes->data) ? $facebook_data->likes->data : NULL, 
							"comments_count"	=>	isset($facebook_data->comments->count) ? $facebook_data->comments->count : 0, 
							"comments_data"		=>	isset($facebook_data->comments->data) ? $facebook_data->comments->data : NULL, 
							"recipients"		=>	isset($facebook_data->to) ? $facebook_data->to : NULL, //contains object of recipients
							"message"			=>	$facebook_data->message, 
							"story"				=>	$facebook_data->story, 
							"link"				=>	$facebook_data->link, 
							"picture"			=>	$facebook_data->picture, 
							"name"				=>	$facebook_data->name, 
							"caption"			=>	$facebook_data->caption, 
							"description"		=>	$facebook_data->description, 
							"object_id"			=>	$facebook_data->object_id, 
							"date"				=>	nicetime($fb_datetime->format("d M Y H:i:s")), 
							"sort_date"			=>	$fb_datetime->format("U"), 
							"iso_date"			=>	$facebook_data->created_time
							);
}

$timeline = $twitterInit->get('/statuses/home_timeline.json', array("count"	=> $limit));
foreach($timeline->response as $twitter_data)
{
	$twitter_time = new DateTime($twitter_data['created_at']);
	$twitter_time->setTimezone(new DateTimeZone(date_default_timezone_get()));
	
	$twitter_timeline[] = array(
							"social"		=>	"twitter", 
							"id"			=>	$twitter_data['id'], 
							"screen_name"	=>	$twitter_data['user']['screen_name'], 
							"user_id"		=>	$twitter_data['user']['id'], 
							"name"			=>	$twitter_data['user']['name'], 
							"picture"		=>	$twitter_data['user']['profile_image_url'], 
							"text"			=>	$twitter_data['text'], 
							"date"			=>	nicetime($twitter_time->format("d M Y H:i:s")), 
							"sort_date"		=>	$twitter_time->format("U"), 
							"iso_date"		=>	date("c", strtotime($twitter_data['created_at']))
							);
}
$social_data = order_array_num(array_merge($facebook_news_feed, $twitter_timeline), "sort_date", "DESC");

//check data availability
$show_per_page = 4;
$start = ($page * $show_per_page) - ($show_per_page - 1);
if(empty($social_data[$start]))	//if array empty, return to first data
	$page = 1;
?>
<div id="feeds-container">
    <div id="feeds-content">
    <?php
	for($i = ($page * $show_per_page) - ($show_per_page - 1), $first = 0; $i <= ($page * $show_per_page); $i++, $first++)
    {
		if(!empty($social_data[$i]))
		{
			if($social_data[$i]['social'] == "facebook")	//facebook
			{
				$type = $social_data[$i]['type'];
				if ($first == 0) echo "<div class=\"fb_box first\">";
				else echo "<div class=\"fb_box\">";
				
				//profile pict
				echo "<a class=\"button_detail\" href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['from_id']."\" target=\"_new\">";
				$profile_pict = "https://graph.facebook.com/".$social_data[$i]['from_id']."/picture?type=square&access_token=".$access_token;
				echo "<img class=\"fb_pic\" src=\"".$profile_pict."\" height=\"50\" width=\"50\" />";
				echo "</a>";
				
				//name
				echo "<div class=\"content\">";
				echo "<div class=\"message\">";
				echo "<a href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['from_id']."\" target=\"_new\">";
				echo "<h3>".$social_data[$i]['from_name']."</h3>";                
				echo "</a>";
				
				//recipient, if exist
				if($social_data[$i]['recipients'])
				{
					$count_recipient = count($social_data[$i]['recipients']->data);
					for($j = 0; $j < $count_recipient; $j++)
					{
						if($j < 3)	//show only 3 recipient maximum
						{
							echo ($j == 0) ? "&nbsp;&gt;&nbsp;" : ", &nbsp;";
							echo "<a href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['recipients']->data[$j]->id."\" target=\"_new\">";
							echo "<span>".$social_data[$i]['recipients']->data[$j]->name."</span>";
							echo "</a>";
						}
						else	//show rest of recipients as "dan X lainnya."
						{
							echo " <span>dan ".($count_recipient - ($j + 1))." lainnya.</span>";
							break;
						}
					}
				}
				
				if($social_data[$i]['actions'])	//not all type of feeds can be commented/liked
				{
					$message_plugins = ""; $print_message = ""; $view_comment = "";
					
					//check if user already likes status
					$FQL = "SELECT likes FROM stream WHERE post_id = '".$social_data[$i]['post_id']."'";
					$user_likes = json_decode(file_get_contents("https://graph.facebook.com/fql?access_token=".$access_token."&locale=id_ID&q=".urlencode($FQL)));
					
					if($user_likes->data[0]->likes->user_likes == 1)	//user already likes status, print "Tidak Suka"
						$print_likes = "<li><a class=\"unlike\" id=\"like-this".$i."\" onclick=\"unlikeAllFeedsComment('".$social_data[$i]['post_id']."', 'like-this".$i."', 'likes-span".$i."');\">Tidak Suka</a></li>";
					else	//vice versa, print "Suka"
						$print_likes = "<li><a class=\"like\" id=\"like-this".$i."\" onclick=\"likeAllFeedsComment('".$social_data[$i]['post_id']."', 'like-this".$i."', 'likes-span".$i."');\">Suka</a></li>";
					
					//check user type (user, page, group, etc), if not user, we can't send message to them
					$user_type = json_decode(file_get_contents("https://graph.facebook.com/".$social_data[$i]['from_id']."?metadata=1&access_token=".$access_token));
					if($user_type->type == "user")
					{
						$message_plugins = "<li><fb:send href=\"".$_SESSION['baseurl']."\" font=\"tahoma\"></fb:send></li>";
						$print_message = "<li style=\"display:none;\"><a class=\"b_message\" id=\"send-message".$i."\" onclick=\"sendFacebookMessage('".$social_data[$i]['from_id']."', '".urlencode($social_data[$i]['from_name'])."');\" style=\"display:none;\">Pesan</a></li>";
					}
					
					//check comments count, if > 0, then print 'Lihat Komentar'
					if($social_data[$i]['comments_count'] > 0)
					{
						$view_comment = "<li><a class=\"b_viewcomment\" id=\"view-comment".$i."\" onclick=\"viewAllFeedsComments('".$social_data[$i]['post_id']."', 'content');\">Lihat Komentar</a></li>";
					}
					
					echo "<ul class=\"action_box\">
					<li><a class=\"b_comment\" id=\"comment-box".$i."\" onclick=\"replyFacebookComment('".$social_data[$i]['from_id']."', 'comment-box".$i."', 'comment-span".$i."');\">Komentar</a></li>
					".$print_likes."
					".$print_message."
					</ul>";
				}

				
				// type = status, link, photo, video, other.
				$array_story = explode("_", $social_data[$i]['post_id']);
				$max_length = 140;	//max character to show
				if(!empty($social_data[$i]['message']))
				{
					$message = $social_data[$i]['message'];
					if(strlen($message) > $max_length)
						$message = print_message($message, $max_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
				}
				elseif(!empty($social_data[$i]['story']))
				{
					$message = $social_data[$i]['story'];
					if(strlen($message) > $max_length)
						$message = print_message($message, $max_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
				}
				else
				{
					!empty($social_data[$i]['caption']) ? $message = $social_data[$i]['caption'] : $message = $social_data[$i]['name'];
					if(strlen($message) > $max_length)
						$message = print_message($message, $max_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
				}
				
				echo "<p>".$message."</p>";

				//limit description length
				$max_description_length = 175;
				!empty($social_data[$i]['description']) ? $description = $social_data[$i]['description'] : $description = "";
				if(strlen($description) > $max_description_length)
					$description = print_message($description, $max_description_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
		
				echo "<div class=\"more\">";
				//type
				if($type == "status")
				{
				}
				elseif($type == "link")
				{
					echo "<a href=\"".$social_data[$i]['link']."\" target=\"_new\">";
					echo ($social_data[$i]['picture']) ? "<p><img src=\"".$social_data[$i]['picture']."\" title=\"".$social_data[$i]['caption']."\" /></p>" : "";
					echo "<p>".$social_data[$i]['name']."</p></a>";
					echo "<p>".$description."</p>";
				}
				elseif($type == "photo")
				{
					echo ($social_data[$i]['picture']) ? "<p><img src=\"".$social_data[$i]['picture']."\" /></p>" : "<p><img src=\"https://graph.facebook.com/".$social_data[$i]['object_id']."/picture?type=album&access_token=".$access_token."\" /></p>";
					echo "<p><a href=\"".$social_data[$i]['link']."\" target=\"_new\" title=\"".$social_data[$i]['caption']."\">".$social_data[$i]['name']."</a></p>";
					echo "<p>".$description."</p>";
				}
				elseif($type == "video")
				{
					echo ($social_data[$i]['picture']) ? "<p><img src=\"".$social_data[$i]['picture']."\" /></p>" : "<p><img src=\"https://graph.facebook.com/".$social_data[$i]['object_id']."/picture?type=album&access_token=".$access_token."\" /></p>";
					echo "<p><a href=\"".$social_data[$i]['link']."\" target=\"_new\" title=\"".$social_data[$i]['caption']."\">".$social_data[$i]['name']."</a></p>";
					echo "<p>".$description."</p>";
				}
				else	//other type
				{
					echo "<p>".$description."</p>";
				}
				
				echo "<cite class=\"timeago\" title=\"".$social_data[$i]['iso_date']."\"></cite>";
				
				//actions
				if($social_data[$i]['actions'])
				{
					echo "<br /><cite><span id=\"comment-span".$i."\">".$social_data[$i]['comments_count']." komentar</span>";
					echo " | <span id=\"likes-span".$i."\">".$social_data[$i]['likes_count']." suka</span>";
					echo "</cite>";
				}
				
				echo "</div>"; // end of div 'more'
				echo "</div>"; // end of div 'message'
				
				$max_comment_length = 80;
				if($type == "status")
				{
					if($social_data[$i]['actions'])
					{
						//comments detail
						echo "<div class=\"comments\">";
						
						//show some user comments
						if($social_data[$i]['comments_count'] > 0)
						{
							echo "<ul>";
							for($k = 0; $k < count($social_data[$i]['comments_data']) && $k < 3; $k++) {	//show max 3 comments
								//limit comment length
								isset($social_data[$i]['comments_data'][$k]->message) ? $comment = $social_data[$i]['comments_data'][$k]->message : $comment = "";
								if(strlen($comment) > $max_comment_length)	{
									$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
								}
					
								echo "
								<li>
									<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->id."\" target=\"_new\">
										<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$social_data[$i]['comments_data'][$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
									</a>
									<div class=\"comment\">
									<h3 class=\"name\">
										<a href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->from->id."\" target=\"_new\">".$social_data[$i]['comments_data'][$k]->from->name."</a>
									</h3><br />
									<code>".$comment."</code><br />
									<cite class=\"timeago\" title=\"".$social_data[$i]['comments_data'][$k]->created_time."\"></cite>
									</div>
								</li>";
							}	//end for
							echo "</ul>";
						}	//end if
						
						echo "</div>";	//end of comments
					}
				}
				elseif($type == "link")
				{
					if($social_data[$i]['actions'])
					{
						//comments detail
						echo "<div class=\"comments\">";
						
						//show some user comments
						if($social_data[$i]['comments_count'] > 0)
						{
							echo "<ul>";
							for($k = 0; $k < count($social_data[$i]['comments_data']) && $k < 3; $k++) {	//show max 3 comments
								//limit comment length
								isset($social_data[$i]['comments_data'][$k]->message) ? $comment = $social_data[$i]['comments_data'][$k]->message : $comment = "";
								if(strlen($comment) > $max_comment_length)	{
									$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
								}
					
								echo "
								<li>
									<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->id."\" target=\"_new\">
										<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$social_data[$i]['comments_data'][$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
									</a>
									<div class=\"comment\">
									<h3 class=\"name\">
										<a href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->from->id."\" target=\"_new\">".$social_data[$i]['comments_data'][$k]->from->name."</a>
									</h3><br />
									<code>".$comment."</code><br />
									<cite class=\"timeago\" title=\"".$social_data[$i]['comments_data'][$k]->created_time."\"></cite>
									</div>
								</li>";
							}	//end for
							echo "</ul>";
						}	//end if
						
						echo "</div>";	//end of comments
					}
				}
				elseif($type == "photo")
				{
					if($social_data[$i]['actions'])
					{
						//comments detail
						echo "<div class=\"comments\">";
						
						//show some user comments
						if($social_data[$i]['comments_count'] > 0)
						{
							echo "<ul>";
							for($k = 0; $k < count($social_data[$i]['comments_data']) && $k < 3; $k++) {	//show max 3 comments
								//limit comment length
								isset($social_data[$i]['comments_data'][$k]->message) ? $comment = $social_data[$i]['comments_data'][$k]->message : $comment = "";
								if(strlen($comment) > $max_comment_length)	{
									$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
								}
					
								echo "
								<li>
									<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->id."\" target=\"_new\">
										<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$social_data[$i]['comments_data'][$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
									</a>
									<div class=\"comment\">
									<h3 class=\"name\">
										<a href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->from->id."\" target=\"_new\">".$social_data[$i]['comments_data'][$k]->from->name."</a>
									</h3><br />
									<code>".$comment."</code><br />
									<cite class=\"timeago\" title=\"".$social_data[$i]['comments_data'][$k]->created_time."\"></cite>
									</div>
								</li>";
							}	//end for
							echo "</ul>";
						}	//end if
						
						echo "</div>";	//end of comments
					}
				}
				elseif($type == "video")
				{
					if($social_data[$i]['actions'])
					{
						//comments detail
						echo "<div class=\"comments\">";
						
						//show some user comments
						if($social_data[$i]['comments_count'] > 0)
						{
							echo "<ul>";
							for($k = 0; $k < count($social_data[$i]['comments_data']) && $k < 3; $k++) {	//show max 3 comments
								//limit comment length
								isset($social_data[$i]['comments_data'][$k]->message) ? $comment = $social_data[$i]['comments_data'][$k]->message : $comment = "";
								if(strlen($comment) > $max_comment_length)	{
									$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
								}
					
								echo "
								<li>
									<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->id."\" target=\"_new\">
										<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$social_data[$i]['comments_data'][$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
									</a>
									<div class=\"comment\">
									<h3 class=\"name\">
										<a href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->from->id."\" target=\"_new\">".$social_data[$i]['comments_data'][$k]->from->name."</a>
									</h3><br />
									<code>".$comment."</code><br />
									<cite class=\"timeago\" title=\"".$social_data[$i]['comments_data'][$k]->created_time."\"></cite>
									</div>
								</li>";
							}	//end for
							echo "</ul>";
						}	//end if
						
						echo "</div>";	//end of comments
					}
				}
				else	//other type
				{
					if($social_data[$i]['actions'])
					{
						//comments detail
						echo "<div class=\"comments\">";
						
						//show some user comments
						if($social_data[$i]['comments_count'] > 0)
						{
							echo "<ul>";
							for($k = 0; $k < count($social_data[$i]['comments_data']) && $k < 3; $k++) {	//show max 3 comments
								//limit comment length
								isset($social_data[$i]['comments_data'][$k]->message) ? $comment = $social_data[$i]['comments_data'][$k]->message : $comment = "";
								if(strlen($comment) > $max_comment_length)	{
									$comment = print_message($comment, $max_comment_length)."<a href=\"http://www.facebook.com/permalink.php?story_fbid=".$array_story[1]."&id=".$array_story[0]."\" target=\"_new\" class=\"see_more\">(lihat lebih)</a>";
								}
					
								echo "
								<li>
									<a class=\"pic\" href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->id."\" target=\"_new\">
										<img class=\"fb_pic\" src=\"https://graph.facebook.com/".$social_data[$i]['comments_data'][$k]->from->id."/picture?type=square&access_token=".$access_token."\" height=\"50\" width=\"50\" />
									</a>
									<div class=\"comment\">
									<h3 class=\"name\">
										<a href=\"http://www.facebook.com/profile.php?id=".$social_data[$i]['comments_data'][$k]->from->id."\" target=\"_new\">".$social_data[$i]['comments_data'][$k]->from->name."</a>
									</h3><br />
									<code>".$comment."</code><br />
									<cite class=\"timeago\" title=\"".$social_data[$i]['comments_data'][$k]->created_time."\"></cite>
									</div>
								</li>";
							}	//end for
							echo "</ul>";
						}	//end if
						
						echo "</div>";	//end of comments
					}
				}

				echo "</div>";	//closing tag div feed_content
				//echo "<a class=\"button_detail right\"></a>";
				echo "</div>";	//closing tag div fb_box
			}
			else	//twitter
			{
				if ($first == 0) echo "<div class=\"fb_box first\">";
				else echo "<div class=\"fb_box\">";
				
				//profile pic
				echo "<a class=\"button_detail\" href=\"http://www.twitter.com/".$social_data[$i]['screen_name']."\" target=\"_new\">";
				echo "<img class=\"fb_pic\" src=\"".$social_data[$i]['picture']."\" />";
				echo "</a>";
				
				echo "<div class=\"content\">";
				echo "<div class=\"message\">";
				echo "<a href=\"http://www.twitter.com/".$social_data[$i]['screen_name']."\" target=\"_new\">";
				echo "<h3>".$social_data[$i]['name']."</h3>";
				echo "</a>";
				
				echo "
				<ul class=\"action_box\">
				<li><a class=\"b_reply\" id=\"reply-tweet".$i."\" onclick=\"replyTweet('".$social_data[$i]['user_id']."', '".$social_data[$i]['screen_name']."', '".$i."');\">Balas</a></li>
				<li><a class=\"b_retweet\" id=\"retweet".$i."\" onclick=\"retweet('".$social_data[$i]['id']."');\">Retweet</a></li>
				<li><a class=\"b_message\" id=\"compose-message".$i."\" onclick=\"replyMessage('".$social_data[$i]['user_id']."', '".$social_data[$i]['screen_name']."', '".$i."');\">Pesan</a></li>
				<li><a id=\"form-compose".$i."\" style=\"display:none;\"></a></li>
				</ul>
				";
				
				echo "<p>".$social_data[$i]['text']."</p>";
				echo "<cite class=\"timeago\" title=\"".$social_data[$i]['iso_date']."\"></cite>";
				echo "</div>";	//end tag div class message
				echo "</div>";	//end tag div class content
				echo "</div>";	//end tag div class twitter_box
			}
		}
    }
    ?>
    </div>
    
    <div id="paging_box">
        <a id="first" class="link" onclick="changeAllFeedsPage('1', 'content');"></a>
        <div id="paging_button">
            <ul>
                <li><a id="prev" class="link" onclick="changeAllFeedsPage('<?php echo ($page-1); ?>', 'content');"></a></li>
                <li><a id="next" class="link" onclick="changeAllFeedsPage('<?php echo ($page+1); ?>', 'content');"></a></li>
            </ul>
        </div>
    </div>

</div>