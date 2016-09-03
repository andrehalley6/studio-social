// JavaScript Document

var interval_timeout = "600000";	//interval timeout in miliseonds (600000 ms = 10 minute)
var facebook_login = "";			//empty string
var twitter_login = "";				//empty string

//var is_ie7 = false;

function checkNotifications(notify)	//check facebook notifications every 10 minutes
{
	$.post("check_notifications.php", {}, function(data){
		if(parseInt(data) != 0){
			$("#" + notify).html(data).attr('class', 'notifications_notify');
		}
	});
}

function updateNotifications(notify)
{
	//clear number of notifications if use open notifications menu
	$.post("check_notifications.php", {}, function(data){
		if(parseInt(data) - 5 <= 0){	//data - 5, because total notifications shown in facebook_notifications.php is 5
			$("#" + notify).html("&nbsp;").removeAttr('class');
		}
		else{
			$("#" + notify).html(data).attr('class', 'notifications_notify');
		}
	});
}



/*
*
*	Left Menu Functions
*
*/

function showContent(content_type, container)	//content_type : facebook, twitter, all
{
	if(content_type == "af")
	{
		//all feeds, call ajax for all feeds
		$.post("all_feeds.php", {}, function(data){
			$("#" + container).html(data);
			FB.XFBML.parse($("#" + container).get(0));
		});
	}
	else if(content_type == "ff")
	{
		//facebook feeds, call ajax for facebook feeds
		$.post("facebook_feeds.php?", { content_type: "" + content_type + "" }, function(data) {
			$("#" + container).html(data);
		});
		
		$.post("facebook_feeds_ajax.php?", { content_type: "" + content_type + "" }, function(data){
			$("#feeds-container").html(data);
			FB.XFBML.parse($("#" + container).get(0));
		});
	}
	else if(content_type == "tf")
	{
		//twitter feeds, call ajax for twitter feeds
		$.post("twitter_feeds.php?", {}, function(data){
			$("#" + container).html(data);
		});
		
		$.post("twitter_feeds_ajax.php?", {}, function(data){
			$("#feeds-container").html(data);
		});
	}
	else if(content_type == "am")
	{
		//all messages, call ajax for all messages
		$.post("all_messages.php", { }, function(data){
			$("#" + container).html(data);
			FB.XFBML.parse($("#" + container).get(0));
		});
	}
	else if(content_type == "fm")
	{
		//facebook messages, call ajax for facebook messages
		$.post("facebook_messages.php?", { content_type: content_type }, function(data){
			$("#" + container).html(data);
			FB.XFBML.parse($("#" + container).get(0));
		});
	}
	else if(content_type == "tm")
	{
		//twitter messages, call ajax for twitter messages
		$.post("twitter_messages.php", {}, function(data){
			$("#" + container).html(data);
		});
	}
	else if(content_type == "an")
	{
		//all notifications, call ajax for all notifications
		//Twitter don't have notifications, so call facebook_notifications instead
		$.post("facebook_notifications.php?", { content_type: content_type }, function(data){
			$("#" + container).html(data);
		});
	}
	else if(content_type == "fn")
	{
		//facebook notifications, call ajax for facebook notifications
		$.post("facebook_notifications.php?", { content_type: content_type }, function(data){
			$("#" + container).html(data);
		});
	}
	else if(content_type == "tn")
	{
		//twitter notifications, call ajax for twitter notifications
		$.post("twitter_notifications.php", {}, function(data){
			$("#" + container).html(data);
		});
	}
}

function changeActiveSelection(id, container, additional_class)	//show active menu (active menu color = white)
{
	if(additional_class == "")
	{
		$("#" + container + " a").removeAttr("class");	// remove class attribute for all <a>
		$("#" + id).attr("class", "active");			// add class = 'active' for selected id
	}
	else
	{
		$("#" + container + " a").removeAttr("class");				// remove class attribute for all <a>
		$("#" + id).attr("class", "active " + additional_class);	// add class = 'active' for selected id
		$("#" + container + " a").attr("class", additional_class);	
	}
}





/*
*
* Top Menu Function
*
*/

function changeType(type, container)	//function to change type (update status/upload new photos)
{
	$.post("new_status_ajax.php?", { type: "" + type + "" }, function(data){
		$("#" + container).html(data);
	});
}

function updateStatus()	//show pop-up dialog for status update/photo upload
{
	$update_status
		.load("new_status.php")
		.dialog({
			autoOpen	:	true,
			title		: 	"Perbarui Status",
			modal		: 	true,
			draggable	: 	false,
			resizable	:	false,
			width		: 	400,
			height		: 	"auto", 
			position	:	[130, 0], 
			close		: 	function(event, ui) { $(this).remove(); }
		}, "open");
}

function closeStatusDialog()
{
	//close modal dialog if user press cancel button
	$update_status.remove();
}

function socialType(type, container)	//change between twitter/facebook message
{
	$.post("new_message_ajax.php?", { type: type }, function(data){
		$("#" + container).html(data);
		FB.XFBML.parse($("#" + container).get(0));
	});
}

function createNewMessage()	//show pop-up dialog for create new message
{
	$compose_message
		.load("new_message.php")
		.dialog({
			autoOpen	:	true,
			title		: 	"Kirim Pesan",
			modal		: 	true,
			draggable	: 	false,
			resizable	:	false,
			width		: 	400,
			height		: 	"auto", 
			position	:	[130, 0], 
			close		: 	function(event, ui) { $(this).remove(); }
		}, "open");
}

function closeCreateNewMessage()
{
	//close pop-up dialog if user press cancel
	$compose_message.remove();
}

function enableNewAlbums()
{
	if($("#facebook_albums").val() == "new_albums")
	{
		$("#album_name").removeAttr("disabled");		//remove album_name disabled attr
		$("#album_name").removeAttr("style");			//remove album_name style attr
		$("#album_description").removeAttr("disabled");	//remove album_description disabled attr
		$("#album_description").removeAttr("style");	//remove album_description style attr
	}
	else
	{
		$("#album_name").attr("disabled", "disabled");						//add disabled attr to album name
		$("#album_name").attr("style", "background-color:#ccc;");			//add style attr to album name
		$("#album_description").attr("disabled", "disabled");				//add disabled attr to album name
		$("#album_description").attr("style", "background-color:#ccc;");	//add style attr to album name
	}
}

function showMenu(type, container, fb_url, twitter_url)	//show top menu (feeds, messages, notifications, new status/new messages)
{
	if(type == "f")	//f : feeds
	{
		$("#" + container)
			.load("menu.php?type=" + type + "&fb_url=" + fb_url + "&twitter_url=" + twitter_url);
		
		$("#new-status").html("Ubah Status");
		$("#new-status").unbind('click').click(function(){
			updateStatus();
		});
	}
	else if(type == "m")	//m : messages
	{
		$("#" + container)
			.load("menu.php?type=" + type + "&fb_url=" + fb_url + "&twitter_url=" + twitter_url);
		
		$("#new-status").html("Kirim Pesan");
		$("#new-status").unbind('click').click(function(){
			createNewMessage();
		});
	}
	else if(type == "n")	//n : notifications
	{
		$("#" + container)
			.load("menu.php?type=" + type + "&fb_url=" + fb_url + "&twitter_url=" + twitter_url);
		
		$("#new-status").html("Ubah Status");
		$("#new-status").unbind('click').click(function(){
			updateStatus();
		});
	}
	
	if(facebook_login != "" && twitter_login != "")
		showContent("a" + type, "content");	//show all feeds
	else if(facebook_login != "")
		showContent("f" + type, "content");	//show facebook feeds
	else if(twitter_login != "")
		showContent("t" + type, "content");	//show twitter feeds
}

function refreshContent()	//refresh entire page
{
	location.reload(true);
}

function checkLength(id, container)	//check input length (twitter can post max 140 character)
{
	var count = $("#" + id).val().length;
	$("#char-left").html((140 - count) + " karakter tersisa.");
}



/*
*
*	All Feeds, All Messages, All Notifications functions
*
*/

function viewAllFeedsComments(post_id, container)	//show all feeds
{
	$.post("all_feeds_detail.php", {post_id: post_id}, function(data){
		$("#" + container).html(data);
	});
}

function likeAllFeedsComment(post_id, id, span_id)	//like facebook post
{
	$.post("all_feeds_likes.php?", { post_id: post_id, id: id }, function(data){
		$("#" + id).html("Tidak Suka");
		$("#" + id).removeAttr('onclick');
		$("#" + id)[0].onclick = function(){unlikeAllFeedsComment(post_id, id, span_id); return false;}
		$("#" + span_id).html(data + " suka");
	});
}

function unlikeAllFeedsComment(post_id, id, span_id)	//unlike facebook post
{
	$.post("all_feeds_unlikes.php?", { post_id: post_id, id: id }, function(data){
		$("#" + id).html("Suka");
		$("#" + id).removeAttr('onclick');
		$("#" + id)[0].onclick = function(){likeAllFeedsComment(post_id, id, span_id); return false;}
		$("#" + span_id).html(data + " suka");
	});
}

function changeViewAllFeedsComments(offset, container, post_id)	//show detail comments
{
	$.post("all_feeds_detail.php", {offset: offset, post_id: post_id}, function(data){
		$("#" + container).html(data);
	});
}

function changeAllFeedsPage(page, container)	//change all feeds page
{
	$.post("all_feeds.php?", { page: page}, function(data){
		$("#" + container).html(data);
		FB.XFBML.parse($("#" + container).get(0));
	});
}

function changeAllMessagesPage(page, container)	//change all messages page
{
	$.post("all_messages.php?", { page: page }, function(data){
		$("#" + container).html(data);
		FB.XFBML.parse($("#" + container).get(0));
	});
}






/*
*
*	Facebook API Function (reply comment, like comment, reply message, change page of ceontent)
*
*/

function replyFacebookComment(post_id, id, comment_container)	//show pop-up dialog for reply comment
{
	$fb_reply_comment
		.load("facebook_reply_comment.php?post_id=" + post_id + "&id=" + id + "&comment_container=" + comment_container)
		.dialog({
			autoOpen		: 	true,
			title			: 	"Komentar",
			modal			: 	true,
			draggable		: 	false,
			resizable		:	false,
			width			: 	350,
			height			: 	"auto", 
			position		:	[180, 60], 
			close			: 	function(event, ui) { $(this).remove(); }
		}, "open");
}

function closeReplyFacebookCommentDialog()	//close pop-up dialog for reply comment
{
	$fb_reply_comment.remove();
}

function showFacebookContent(type, container)
{
	if(type == 'newsfeed')
	{
		$.post("facebook_feeds_ajax.php", { }, function(data){
			$("#" + container).html(data);
			FB.XFBML.parse($("#" + container).get(0));
		});
	}
	else if(type == 'photo')
	{
		$.post("facebook_photos.php", { }, function(data){
			$("#" + container).html(data);
		});
	}
	else if(type == 'profile')
	{
		$.post("facebook_profile.php", { }, function(data){
			$("#" + container).html(data);
		});
	}
}

function likeComment(post_id, id, span_id)	//like status
{
	$.post("facebook_likes.php?", { post_id: post_id, id: id }, function(data){
		$("#" + id).html("Tidak Suka");
		$("#" + id).removeAttr('onclick');
		$("#" + id)[0].onclick = function(){unlikeComment(post_id, id, span_id); return false;}
		$("#" + span_id).html(data + " suka");
	});
}

function unlikeComment(post_id, id, span_id)	//unlike status
{
	$.post("facebook_unlikes.php?", { post_id: post_id, id: id }, function(data){
		$("#" + id).html("Suka");
		$("#" + id).removeAttr('onclick');
		$("#" + id)[0].onclick = function(){likeComment(post_id, id, span_id); return false;}
		$("#" + span_id).html(data + " suka");
	});
}

function changeFunction(id)
{
	alert(id + 'changefunction');
}

function replyFacebookMessage(post_id, container_id)
{
	$fb_reply_message
		.load("facebook_reply_message.php?post_id=" + post_id + "&container=" + container_id)
		.dialog({
			autoOpen		: 	true,
			title			: 	"Balas Pesan",
			modal			: 	true,
			draggable		: 	false,
			resizable		:	false,
			width			: 	350,
			height			: 	"auto", 
			position		:	[180, 60], 
			close			: 	function(event, ui) { $(this).remove(); }
		}, "open");
}

function closeReplyFacebookMessageDialog()
{
	$fb_reply_message.remove();
}

function sendFacebookMessage(destination_id, destination_name)
{
	$fb_send_message
		.load("facebook_send_message.php?id=" + destination_id + "&name=" + destination_name)
		.dialog({
			autoOpen		: 	true,
			title			: 	"Kirim Pesan",
			modal			: 	true,
			draggable		: 	false,
			resizable		:	false,
			width			: 	350,
			height			: 	"auto", 
			position		:	[180, 60], 
			close			: 	function(event, ui) { $(this).remove(); }
		}, "open");
}

function closeSendFacebookMessageDialog()
{
	$fb_send_message.remove();
}

function viewComments(post_id, container)	//show detail comments
{
	$.post("facebook_feeds_detail.php", {post_id: post_id}, function(data){
		$("#" + container).html(data);
	});
}

function viewConversation(post_id, container)	//show detail inbox
{
	$.post("facebook_messages_detail.php?", { post_id: post_id }, function(data){
		$("#" + container).html(data);
		FB.XFBML.parse($("#" + container).get(0));
	});
}

function detailPhotos(album_id, container)	//show photos
{
	$.post("facebook_photos_detail.php?", { album_id: album_id }, function(data){
		$("#" + container).html(data);
	});
}

function changeFeedsPage(offset, container)	//change facebook feeds page
{
	$.post("facebook_feeds_ajax.php?", { offset: "" + offset + "" }, function(data){
		$("#" + container).html(data);
		FB.XFBML.parse($("#" + container).get(0));
	});
}

function changeViewCommentsPage(offset, container, post_id)	//change detail comments page
{
	$.post("facebook_feeds_detail.php", {offset: offset, post_id: post_id}, function(data){
		$("#" + container).html(data);
	});
}

function changeMessagePage(offset, container)	//change inbox page
{
	$.post("facebook_messages.php?", { offset: "" + offset + "" }, function(data){
		$("#" + container).html(data);
		FB.XFBML.parse($("#" + container).get(0));
	});
}

function changeDetailMessagePage(offset, container, post_id)	//change detail inbox page
{
	$.post("facebook_messages_detail.php", { offset: offset, post_id: post_id }, function(data){
		$("#" + container).html(data);
	});
}

function changeNotificationsPage(offset, container)	//change notifications page
{
	$.post("facebook_notifications.php?", { offset: "" + offset + "" }, function(data){
		$("#" + container).html(data);
	});
}

function changeAlbumsPage(offset, container)	//change photo albums page
{
	$.post("facebook_photos.php?", { offset: offset }, function(data){
		$("#" + container).html(data);
	});
}

function changePhotosPage(offset, album_id, container)	//change photos page
{
	$.post("facebook_photos_detail.php?", { offset: offset, album_id: album_id  }, function(data){
		$("#" + container).html(data);
	});
}






/*
*
*	Twitter Function
*
*/

function showTwitterContent(type, container)
{
	if(type == 'newsfeed')
	{
		$.post("twitter_feeds_ajax.php", { }, function(data){
			$("#" + container).html(data);
		});
	}
	else if(type == 'mention')
	{
		$.post("twitter_mention.php", { }, function(data){
			$("#" + container).html(data);
		});
	}
	else if(type == 'profile')
	{
		$.post("twitter_profile.php", { }, function(data){
			$("#" + container).html(data);
		});
	}
}

function replyTweet(reply_id, screen_name, id)
{
	$twitter_reply_tweet
		.load("twitter_reply_tweet.php?reply_id=" + reply_id + "&screen_name=" + screen_name + "&id=" + id)
		.dialog({
			autoOpen		: 	true,
			title			: 	"Membalas Tweet",
			modal			: 	true,
			draggable		: 	false,
			resizable		:	false,
			width			: 	350,
			height			: 	"auto", 
			position		:	[180, 60], 
			close			: 	function(event, ui) { $(this).remove(); }
		}, "open");
}

function closeReplyTweetDialog()
{
	$twitter_reply_tweet.remove();
}

function retweet(id)
{
	$.post("twitter_retweet.php?", { id: id }, function(data){
	});
}

function replyMessage(user_id, screen_name, id)
{
	$twitter_reply_message
		.load("twitter_compose_messages.php?user_id=" + user_id + "&screen_name=" + screen_name + "&id=" + id)
		.dialog({
			autoOpen		: 	true,
			title			: 	"Membalas Pesan",
			modal			: 	true,
			draggable		: 	false,
			resizable		:	false,
			width			: 	350,
			height			: 	"auto", 
			position		:	[180, 60], 
			close			: 	function(event, ui) { $(this).remove(); }
		}, "open");
}

function closeReplyMessageDialog()
{
	$twitter_reply_message.remove();
}

function changeTwitterFeedsPage(page, container)
{
	$.post("twitter_feeds_ajax.php?", { page: page }, function(data){
		$("#" + container).html(data);
	});
}

function changeTwitterMentionsPage(page, container)
{
	$.post("twitter_mention.php?", { page: page }, function(data){
		$("#" + container).html(data);
	});
}

function changeTwitterMessagesPage(page, container)
{
	$.post("twitter_messages.php?", { page: page }, function(data){
		$("#" + container).html(data);
	});
}

function twitterSession()
{
	$.post("twitter_session.php", {}, function(data){
	});
}

function disabledRecipients(recipients_container, mark_container)
{
	$("#" + recipients_container).attr("class", "required dialog_input recipients_disabled");
	$("#" + recipients_container).attr("readonly", "readonly");
	$("#" + mark_container).removeAttr("style");
	$("#" + mark_container).unbind("click").click(function(){	//delete recipients if user click the X picture
		deleteRecipients('screen_name', 'user_id', 'xmark', 'recipients_img');
	});
	
	//enabled submit button
	$("#submit").removeAttr("disabled").removeAttr("class").attr("class", "dialog_button");
}

function deleteRecipients(screen_name, user_id, xmark, recipients_img)	//parameter is html id
{
	$("#" + recipients_img).attr("src", "img/questionmark.jpg");
	$("#" + xmark).attr("style", "display:none;");
	$("#" + user_id).removeAttr("value");
	$("#" + screen_name).removeAttr("value").removeAttr("class").removeAttr("readonly").attr("class", "required dialog_input");
	
	//disabled submit button
	$("#submit").removeAttr("class").attr("class", "submit_disabled").attr("disabled", "disabled");
}



/*
*
*	Input Submit Function
*
*/

function submitReplyTweet()
{
	$("#form_reply_tweet").validate({
		debug			: false, 
		submitHandler	: function(form) {
			$.post("twitter_reply_tweet_submit.php", {reply_id: $("#reply_id").val(), reply_tweet: escape($("#reply_tweet").val())}, function(data) {
				closeReplyTweetDialog();
			});
		}
	});
}

function submitTwitterComposeMessage()
{
	$("#form_compose_message").validate({
		debug			: false, 
		submitHandler	: function(form) {
			$.post("twitter_compose_messages_submit.php", {user_id: $("#user_id").val(), screen_name: $("#screen_name").val(), text_message: escape($("#text_message").val())}, function(data) {
				closeReplyMessageDialog();
			});
		}
	});
}

function submitNewStatus()
{
	$("#form_update_status").validate({
		debug			: false, 
		submitHandler	: function(form){
			$.post("new_status_submit.php", {type: $('.rd:checked').map(function(i,n) {return $(n).val();}).get(), message: escape($("#status-message").val()), db: $('.db:checked').map(function(i,n) {return $(n).val();}).get(), facebook_albums: escape($("#facebook_albums").val()), album_name: escape($("#album_name").val()), album_description: escape($("#album_description").val()), photo_name: escape($("#photo_name").val()), photo_caption: escape($("#photo_caption").val())}, function(data){
				closeStatusDialog();
			});
		}
	});
}

function submitNewMessage()
{
	$("#form_new_message").validate({
		debug			:	false, 
		submitHandler	:	function(form) {
			$.post("new_message_submit.php", {user_id: $("#user_id").val(), screen_name: $("#screen_name").val(), text_message: escape($("#text_message").val())}, function(data) {
				closeCreateNewMessage();
			});
		}
	});
}

function submitFacebookSendMessage()
{
	$("#form_send_message").validate({
		debug			: false, 
		submitHandler	: function(form){
			$.post("facebook_send_message_submit.php", {id: $("#id").val(), message: escape($("#message").val())}, function(data){
				closeSendFacebookMessageDialog();
			});
		}
	});
}

function submitFacebookReplyMessage()
{
	$("#form_reply_message").validate({
		debug			: false, 
		submitHandler	: function(form){
			$.post("facebook_reply_message_submit.php", {post_id: $("#post_id").val(), message: escape($("#message").val())}, function(data){
				closeReplyFacebookMessageDialog();
			});
		}
	});
}

function submitFacebookReplyComment(comment_container)
{
	$("#form_reply_comment").validate({
		debug			: false, 
		submitHandler	: function(form) {
			$.post("facebook_reply_comment_submit.php", {post_id: $("#post_id").val(), comment: escape($("#comment").val())}, function(data) {
				$("#" + comment_container).html(data + " komentar.");
				closeReplyFacebookCommentDialog();
			});
		}
	});
}