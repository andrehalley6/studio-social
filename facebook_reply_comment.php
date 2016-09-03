<?php
session_start();
$post_id = $_GET['post_id'];
$id = $_GET['id'];
$comment_container = $_GET['comment_container'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Balas Komentar</title>

</head>

<body>
<form id="form_reply_comment" name="form_reply_comment" action="" method="post" autocomplete="off">
    <input type="hidden" id="post_id" name="post_id" value="<?php echo $post_id; ?>" />
    
    <div class="dialog_label">Komentar :</div>
    <textarea id="comment" name="comment" rows="3" cols="35" style="resize:none;" class="required dialog_input"></textarea>
    <div class="clear_both"></div><br />
    
    <input id="submit" type="submit" value="Balas" class="dialog_button" onclick="submitFacebookReplyComment('<?php echo $comment_container; ?>');" />
    <input id="reset" type="reset" value="Batal" onclick="closeReplyFacebookCommentDialog();" class="dialog_button" />
</form>

</body>
</html>