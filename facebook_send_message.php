<?php
session_start();
$id = $_GET['id'];					//destination id
$name = urldecode($_GET['name']);	//destination name
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kirim Pesan</title>

<!--script type="text/javascript" language="javascript">
$(document).ready(function(){
	$("#submit").click(function(){
		var id = $("#id").val();
		var message = escape($("#message").val());
		$("#form_send_message").validate({
			debug			: false, 
			submitHandler	: function(form){
				//$.post("facebook_send_message_submit.php", {id: id, message: message}, function(data){
				$.post("facebook_send_message_submit.php", {id: $("#id").val(), message: escape($("#message").val())}, function(data){
					closeSendFacebookMessageDialog();
				});
			}
		});
	});
});
</script-->
</head>

<body>
<!--form id="form_send_message" name="form_send_message" action="facebook_send_message_submit.php" method="post" autocomplete="off"-->
<form id="form_send_message" name="form_send_message" action="" method="post" autocomplete="off">
    <input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
    
    <div class="dialog_label">Kepada :</div>
    <input type="text" id="name" name="name" value="<?php echo $name; ?>" readonly="readonly" size="<?php echo strlen($name) + 3; ?>" style="background-color:#ccc;" class="dialog_input" />
    <div class="clear_both"></div><br />
    
    <div class="dialog_label">Pesan :</div>
    <textarea id="message" name="message" rows="3" cols="35" style="resize:none;" class="required dialog_input"></textarea>
    <div class="clear_both"></div><br />
    
    <input id="submit" type="submit" value="Kirim" class="dialog_button" onclick="submitFacebookSendMessage();" />
    <input id="reset" type="reset" class="dialog_button" value="Batal" onclick="closeSendFacebookMessageDialog();" />
</form>
</body>
</html>