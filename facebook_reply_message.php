<?php
session_start();
$post_id = $_GET['post_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Balas Pesan</title>

<!--script type="text/javascript" language="javascript">
$(document).ready(function(){
	$("#submit").click(function(){
		var post_id = $("#post_id").val();
		var message = escape($("#message").val());
		$("#form_reply_message").validate({
			debug			: false, 
			submitHandler	: function(form){
				//$.post("facebook_reply_message_submit.php", {post_id: post_id, message: message}, function(data){
				$.post("facebook_reply_message_submit.php", {post_id: $("#post_id").val(), message: escape($("#message").val())}, function(data){
					closeReplyFacebookMessageDialog();
				});
			}
		});
	});
});
</script-->
</head>

<body>
<!--form id="form_reply_message" name="form_reply_message" action="facebook_reply_message_submit.php" method="post" autocomplete="off"-->
<form id="form_reply_message" name="form_reply_message" action="" method="post" autocomplete="off">
    <input type="hidden" id="post_id" name="post_id" value="<?php echo $post_id; ?>" />
    
    <div class="dialog_label">Pesan :</div>
    <textarea id="message" name="message" rows="3" cols="35" style="resize:none;" class="required dialog_input"></textarea>
    <div class="clear_both"></div><br />
    
    <input id="submit" type="submit" value="Kirim" class="dialog_button" onclick="submitFacebookReplyMessage();" />
    <input id="reset" type="reset" class="dialog_button" value="Batal" onclick="closeReplyFacebookMessageDialog();" />
</form>
</body>
</html>