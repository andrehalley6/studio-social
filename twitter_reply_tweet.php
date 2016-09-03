<?php
session_start();
$reply_id = $_GET['reply_id'];
$screen_name = $_GET['screen_name'];
$id = $_GET['id'];
include("twitter_authenticate_session.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Twitter Reply Tweet</title>

<script type="text/javascript" language="javascript">
$(document).ready(function(){
	$("#reply_tweet").focus();
});
</script>
</head>

<body>
<form id="form_reply_tweet" name="form_reply_tweet" action="" method="post" autocomplete="off">
    <input type="hidden" id="reply_id" name="reply_id" value="<?php echo $reply_id; ?>" />
    
    <div class="dialog_label">Pesan :</div>
    <textarea id="reply_tweet" name="reply_tweet" rows="3" cols="35" style="resize:none;" class="required dialog_input" maxlength="140" onfocus="checkLength('reply_tweet', '');" onkeyup="checkLength('reply_tweet', '');">@<?php echo $screen_name; ?></textarea>
    <div class="clear_both"></div><br />
    
    <h4 id="char-left">140 karakter tersisa.</h4>
    <div class="clear_both"></div><br />
    
    <input id="submit" type="submit" value="Balas" class="dialog_button" onclick="submitReplyTweet();" />
    <input id="reset" type="reset" value="Batal" onclick="closeReplyTweetDialog();" class="dialog_button" />
</form>
</body>
</html>