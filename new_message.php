<?php
session_start();
if($_SESSION['facebook_access_token']) $fb_checked = "checked=\"checked\"";
else $tw_checked = "checked=\"checked\"";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kirim Pesan</title>

<script type="text/javascript" language="javascript">
var fb = "<?php echo $_SESSION['facebook_name']; ?>";
var tw = "<?php echo $_SESSION['twitter_screen_name']; ?>";
$(document).ready(function(){
	if(fb != "")
		socialType('facebook', 'form-message-container');
	else
		socialType('twitter', 'form-message-container');
});
</script>
</head>

<body>
<form id="form_new_message" name="form_new_message" action="" method="post" autocomplete="off">
	<?php if($_SESSION['facebook_access_token']): ?>
    <input type="radio" name="social" onchange="socialType('facebook', 'form-message-container');" value="facebook" class="radio" <?php echo $fb_checked; ?> />Facebook
    <div class="clear_both"></div>
    <?php endif ?>
	<?php if($_SESSION['ot'] && $_SESSION['ots']): ?>
	<input type="radio" name="social" onchange="socialType('twitter', 'form-message-container');" value="twitter" class="radio" <?php echo $tw_checked; ?> />Twitter
    <div class="clear_both"></div>
    <?php endif ?>
    <br />

	<div id="form-message-container">
    </div>
</form>
</body>
</html>