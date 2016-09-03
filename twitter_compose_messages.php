<?php
session_start();
$user_id = $_GET['user_id'];
$screen_name = $_GET['screen_name'];
$id = $_GET['id'];
include("twitter_authenticate_session.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Compose Messages</title>
</head>

<body>
<form id="form_compose_message" name="form_compose_message" action="" method="post" autocomplete="off">
    <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>" />
    
    <div class="dialog_label">Kepada :</div>
    <input type="text" id="screen_name" name="screen_name" value="<?php echo $screen_name; ?>" readonly="readonly" size="<?php echo strlen($screen_name) + 3; ?>" style="background-color:#ccc;" class="dialog_input" />
    <div class="clear_both"></div><br />
    
    <div class="dialog_label">Pesan :</div>
    <textarea id="text_message" name="text_message" rows="3" cols="35" style="resize:none;" class="required dialog_input" maxlength="140" onkeyup="checkLength('text_message', '');"></textarea>
    <div class="clear_both"></div><br />
    
    <h4 id="char-left">140 karakter tersisa.</h4>
    <div class="clear_both"></div><br />
    
    <input id="submit" type="submit" value="Kirim" class="dialog_button" onclick="submitTwitterComposeMessage();" />
    <input id="reset" type="reset" class="dialog_button" value="Batal" onclick="closeReplyMessageDialog();" />
</form>
</body>
</html>