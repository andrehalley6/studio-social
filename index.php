<?php
	define('SpeedUpSocial', 1);
	
	//session
	session_start();
	
	//config
	include("config.php");
	
	//facebook lib
	require("lib/facebook.php");
	
	//twitter lib
	require("lib/twitter/EpiCurl.php");
	require("lib/twitter/EpiOAuth.php");
	require("lib/twitter/EpiTwitter.php");
	
	// Facebook Initialization
	include("facebook_init.php");
	
	//session baseurl, for facebook message plugins
	$_SESSION['baseurl'] = $BASEURL;
	$_COOKIE['baseurl'] = $BASEURL;
	
	// Get User ID
	$user = $facebook->getUser();
	if ($user) {
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me');
			
			// facebook session variable
			$_SESSION['facebook_name'] = $user_profile['name'];
			$_COOKIE['facebook_name'] = $user_profile['name'];
			(isset($_REQUEST['code'])) ? $_SESSION['facebook_code'] = $_REQUEST['code'] : "";
			(isset($_REQUEST['state'])) ? $_SESSION['facebook_state'] = $_REQUEST['state'] : "";
			$_SESSION['facebook_access_token'] = $facebook->getAccessToken();
			$_COOKIE['facebook_access_token'] = $facebook->getAccessToken();
		} catch (FacebookApiException $e) {
			error_log($e);
			$user = NULL;
			unset($_SESSION['facebook_name'], $_SESSION['facebook_code'], $_SESSION['facebook_state'], $_SESSION['facebook_access_token']);
		}
	}
	else {
		//clear facebook session
		$user = NULL;
		unset($_SESSION['facebook_name'], $_SESSION['facebook_code'], $_SESSION['facebook_state'], $_SESSION['facebook_access_token']);
	}
	
	// Login or logout url will be needed depending on current user state.
	if ($user) {
		$logoutUrl = $facebook->getLogoutUrl(array(
			"next"	=>	$BASEURL
		));
	} else {
		$loginUrl = $facebook->getLoginUrl(array(
			//"redirect_uri"	=>	$BASEURL, 
			"display"		=>	"popup", 
			"scope"			=>	"read_stream,publish_stream,read_mailbox,manage_notifications,user_photos,user_birthday,user_videos,friends_photos,friends_videos"
		));
	}
	
	// Twitter Authentication
	$twitterObj = new EpiTwitter($CONSUMER_KEY, $CONSUMER_SECRET);
	$twitterUrl = $twitterObj->getAuthorizeUrl();
	
	if($_SESSION['ot'] && $_SESSION['ots'])
	{
		try {
			
			$_SESSION['twitterInit'] = new EpiTwitter($CONSUMER_KEY, $CONSUMER_SECRET, $_SESSION['ot'], $_SESSION['ots']);
			$twitterInfo = $_SESSION['twitterInit']->get_accountVerify_credentials();
			$twitterInfo->response;
			
			$screen_name = $twitterInfo->screen_name;
			$profilepic = $twitterInfo->profile_image_url;
			
			$_SESSION['twitter_screen_name'] = $screen_name;
		} catch(EpiTwitterException $e) {
			error_log($e);
		}
	}
	elseif(isset($_GET['oauth_token']))
	{
		try {
			$twitterObj->setToken($_GET['oauth_token']);
			$token = $twitterObj->getAccessToken();
			//echo "<pre>";print_r($token);echo "</pre>";
			if(!isset($token->oauth_token) || !isset($token->oauth_token_secret)) 	echo "<script>self.location.href='".$twitterUrl."'</script>";
			$twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);
			
			//twitter session variable
			$_SESSION['ck'] = $CONSUMER_KEY;
			$_SESSION['cs'] = $CONSUMER_SECRET;
			$_SESSION['ot'] = $token->oauth_token;
			$_SESSION['ots'] = $token->oauth_token_secret;
			$_SESSION['ov'] = $_GET['oauth_verifier'];
			
			$twitterInfo = $twitterObj->get_accountVerify_credentials();
			$twitterInfo->response;
			
			$screen_name = $twitterInfo->screen_name;
			$profilepic = $twitterInfo->profile_image_url;
			
			$_SESSION['twitter_screen_name'] = $screen_name;
		} catch(EpiTwitterException $e) {
			error_log($e);
		}
	}
	
	$display_none = "";
	if(!$_SESSION['facebook_access_token'] && (!$_SESSION['ot'] && !$_SESSION['ots']))
	{
		//no facebook/twitter session do not display button "ubah status", "pesan baru", berita, pesan, and notifikasi
		$display_none = "style=\"display:none;\"";
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Studio Social</title>

<link rel="stylesheet" type="text/css" href="css/reset.css" />
<link rel="stylesheet" type="text/css" href="css/master.css" />
<link rel="stylesheet" type="text/css" href="css/dialog.css" />	<?php // ADDED : ANDRE, css for dialog windows ?>
<link rel="stylesheet" type="text/css" href="js/jquery-ui/jquery-ui-1.8.16.custom.css" />
<link rel="stylesheet" type="text/css" href="js/uploadify/uploadify.css" />

<script type="text/javascript" language="javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.livequery.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-timeago/jquery.timeago.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-timeago/jquery.timeago.id.js"></script> <?php //For Indonesian Language Settings ?>
<script type="text/javascript" language="javascript" src="js/jquery.blockUI.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" src="js/uploadify/swfobject.js"></script>
<script type="text/javascript" language="javascript" src="js/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" language="javascript" src="js/ajax.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	facebook_login = "<?php echo $_SESSION['facebook_name']; ?>";
	twitter_login = "<?php echo $_SESSION['twitter_screen_name']; ?>";
	
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);	//show loading process for ajax call;
	$("a.href").livequery(function(){	//show loading process for href class
		$(this).click(function(){
			$.blockUI();
		});
	});
	
	//horizontal accordion from dev.stormorph.com/scb-demo
	lastBlock = null;
    maxHeight = 313;
    minHeight = 65;
	$(".fb_box a.button_detail").livequery(function(){
		$(this).mouseover/*hover*/(function(){
			you = $(lastBlock).parent().children("div.content");
			you.animate({height:minHeight+"px"}, {queue:false, duration:400});
			//you.removeClass('detail');
			$(lastBlock).parent().removeClass('open');
			//$(lastBlock).parent().addClass('noflow');
			
			me = $(this).parent().children("div.content");
			me.animate({height:maxHeight+"px"}, {queue:false, duration:400});
			//me.addClass('detail');
			$(this).parent().addClass('open');
			//$(this).parent().removeClass('noflow');
			
			lastBlock = this;
			
			//$(lastBlock).animate({width: minWidth+"px"}, { queue:false, duration:400});
			//$(lastBlock).removeClass('active');
			//$(this).animate({width: maxWidth+"px"}, { queue:false, duration:400});
			//$(this).addClass('active');
			//lastBlock = this;
		});
	});
	
	// to remove the detail when the mouse leave the #content
	$("#content").mouseleave(function() {
		$(lastBlock).parent().removeClass('open');
	}).click(function(){
		$(lastBlock).parent().removeClass('open');
	});
	
	$("cite.timeago").livequery(function () { $(this).timeago(); });	//x time ago style
	
	if(twitter_login != "" && facebook_login == "")
	{
		setInterval(function(){	//renew session twitter every 10 minutes
			twitterSession();
		}, interval_timeout);
		$.ajaxSetup({ cache: false });
		
	}
	else if(facebook_login != "")	//only facebook notifications, because twitter don't have notifications
	{
		checkNotifications("notify-count");	//check notifications for every refresh/first time login
		setInterval(function(){	//check notifications every 10 minutes
			checkNotifications("notify-count");
		}, interval_timeout);
		$.ajaxSetup({ cache: false });
	}
	
	//redirect user to feeds
	if(facebook_login != "" || twitter_login != "")
	{
		showMenu("f", "button-con", "<?php echo urlencode($loginUrl); ?>", "<?php echo urlencode($twitterUrl); ?>");
		changeActiveSelection("feed", "header", "");
	}
	
	$update_status = $('<div></div>');		//facebook + twitter update status
	$compose_message = $('<div></div>');	//facebook + twitter compose new message (from top menu)
	
	$twitter_reply_tweet = $('<div></div>');	//twitter
	$twitter_reply_message = $('<div></div>');	//twitter
	$fb_reply_comment = $('<div></div>');		//facebook
	$fb_send_message = $('<div></div>');		//facebook
	$fb_reply_message = $('<div></div>');		//facebook
});
</script>

</head>

<!--body onload="parent.loadingoff();"-->
<body>
    <div id="fb-root"></div>
	<script>
	(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {return;}
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/id_ID/all.js#xfbml=1&appId=<?php echo $FB_APPID; ?>";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>
	
    <div id="left-side">
		<img id="logo" src="img/logo.jpg" />
        <div id="button-con">
        	<ul>
            	<li><a class="button" style="display:none;"><h3>&nbsp;</h3></a></li>
            	<li><a <?php if (!$user) echo "href=\"".$loginUrl."\" class=\"button href\" target=\"_self\""; else echo "onclick=\"showContent('ff', 'content'); changeActiveSelection('fb', 'button-con', 'button');\" class=\"button\""; ?> id="fb">
                	<h3>Facebook</h3>
                	<span><?php if ($user) echo $user_profile['name']; else echo "login"; ?></span>
				</a></li>
            	<li><a <?php if (!$_SESSION['ot'] && !$_SESSION['ots']) echo "href=\"".$twitterUrl."\" class=\"button href\" target=\"_self\""; else echo "onclick=\"showContent('tf', 'content'); changeActiveSelection('tw', 'button-con', 'button');\" class=\"button\""; ?> id="tw">
                	<h3>Twitter</h3>
                	<span><?php if ($_SESSION['ot'] && $_SESSION['ots']) echo $screen_name; else echo "login"; ?></span>
				</a></li>
            </ul>
        </div>

		<?php
        //logout button
        echo '<div class="login">';
        if($user){
            echo '<a href="'.$logoutUrl.'" target="_self" class="href">Logout Facebook</a>';
            echo '<div class="clear_both"></div>';
        }
        if($_SESSION['ot'] && $_SESSION['ots']){
            echo '<a href="twitter_logout.php" target="_self" class="href">Logout Twitter</a>';
        }
        echo '</div>';
        ?>

        <div id="linkmenu">
            <ul>
                <li><a id="link_facebook" href="http://www.facebook.com/DuniaSeruSpeedUp" target="_new">Dunia Seru SpeedUp</a></li>
                <li><a id="link_twitter" href="http://twitter.com/#!/DuniaSeruMu" target="_new">@DuniaSerumu</a></li>
            </ul>
        </div>
    </div>
    
    <div id="right-side">
        <div id="header">
            <ul class="topmenu">
                <li><a id="feed" <?php echo $display_none; ?> 
                		onclick="showMenu('f', 'button-con', '<?php echo $loginUrl; ?>', '<?php echo $twitterUrl; ?>'); 
                        		changeActiveSelection('feed', 'header', '');">&nbsp;</a></li>
                <li><a id="message" <?php echo $display_none; ?> 
                		onclick="showMenu('m', 'button-con', '<?php echo $loginUrl; ?>', '<?php echo $twitterUrl; ?>'); 
                        		changeActiveSelection('message', 'header', '');">&nbsp;</a></li>
                <li><a id="notification" <?php echo $display_none; ?> 
                		onclick="showMenu('n', 'button-con', '<?php echo $loginUrl; ?>', '<?php echo $twitterUrl; ?>'); 
                        		changeActiveSelection('notification', 'header', ''); <?php if(!empty($user)) echo "updateNotifications('notify-count');"; ?>">&nbsp;</a><span id="notify-count"></span></li>
            </ul>

            <div class="error" style="display:none;">
                <!-- facebook -->
                <?php if ($user):
                	echo '<div class="login">';
                    echo '<a href="'.$logoutUrl.'">Logout Facebook</a>';
                	echo '</div>';
                else:
                	echo '<div class="login">';
                    echo '<a href="'.$loginUrl.'">Login with Facebook</a>';
                	echo '</div>';
                endif ?>

                <!-- twitter -->
                <?php if($_SESSION['ot'] && $_SESSION['ots']):
                	echo '<div class="login">';
                    echo '<a href="twitter_logout.php">Logout Twitter</a>';
                	echo '</div>';
                else:
                	echo '<div class="login">';
                    echo '<a href="'.$twitterUrl.'">Login with Twitter</a>';
                	echo '</div>';
                endif ?>
            </div>

            <div class="submenu">
                <a id="new-status" <?php echo $display_none; ?>>Ubah Status</a>
                <a id="status-update" style="display:none;"></a>
                <a id="refresh" class="href" onclick="refreshContent();">&nbsp;</a>
            </div>
        </div>
		
        <div id="content">
	        <img src="img/welcomescreen.png" width="761" height="367" />
            <?php //ajax, default : Facebook / Twitter News Feed ?>
        </div>
    </div>
    
    <div style="clear:both;"></div><br />
    <div id="notes" style="color:#ff0000;">
    <strong>NOTES :</strong><br />
    &bull; This application made for Speed Up modem with their own browser, some features maybe malfunction with current browser.<br />
    &bull; This application using Twitter API v1.0. Twitter API v1.0 will be retire on 11 June 2013. <a href="https://dev.twitter.com/blog/api-v1-retirement-date-extended-to-june-11" target="_new">Click this link for more information.</a><br />
	&bull; Twitter API for this application already change to Version 1.1, but I don't change anything except API version, maybe some feature will not work correctly.<br />
    &bull; This application using Facebook PHP SDK version 2, current version is 3.2.<br />
    &bull; Some Facebook and/or Twitter features maybe deprecated because of development of new SDK.<br />
    </div>
</body>
</html>