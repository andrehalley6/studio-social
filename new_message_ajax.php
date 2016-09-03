<?php
session_start();

if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}

if($_POST['type'] == "facebook")
{
	//to get all friends just use https://graph.facebook.com/me/friends?access_token=
	echo "<fb:send href=\"".$_SESSION['baseurl']."\" font=\"tahoma\"></fb:send>";
	echo "<div class=\"clear_both;\"></div><br />";
	
	echo "
	<input id=\"submit\" type=\"submit\" value=\"Kirim\" class=\"dialog_button\" onclick=\"submitNewMessage();\" />
    <input id=\"reset\" type=\"reset\" value=\"Batal\" onclick=\"closeCreateNewMessage();\" class=\"dialog_button\" />
	";
}
else
{
	echo "
	<script type=\"text/javascript\" language=\"javascript\">
	$(function(){
		$('#screen_name').autocomplete({
			source		: function(req, response){
				$.ajax({
					url:	'twitter_showfriends.php', 
					dataType:	'json', 
					success:	function(data){
						var re = $.ui.autocomplete.escapeRegex(req.term);
						var matcher = new RegExp( '^' + re, 'i' );
						response($.grep(data, function(item){return matcher.test(item.value);}) );
					}
				});
			},
			minLength	: 1,
			focus		: function( event, ui ) {
				$('#screen_name').attr('value', ui.item.label);
				return false;
			},
			select		: function( event, ui ) {
				$('#screen_name').attr('value', ui.item.label);
				$('#user_id').attr('value', ui.item.id);
				$('#recipients_img').attr('src', ui.item.image);
				disabledRecipients('screen_name', 'xmark');
				$('#text_message').focus();
			}
		});
	});
	</script>
	";
	
	echo "
	<input type=\"hidden\" id=\"user_id\" name=\"user_id\" value=\"\" />
	
	<div class=\"dialog_label\">Kepada :</div>
	<img src=\"img/questionmark.jpg\" height=\"20px\" width=\"20px\" id=\"recipients_img\" align=\"top\" style=\"margin-left:5px;\" />
    <input type=\"text\" id=\"screen_name\" name=\"screen_name\" value=\"\" size=\"25\" class=\"required dialog_input\" />
	<img src=\"img/xmark.gif\" height=\"12px\" width=\"12px\" id=\"xmark\" class=\"xmark\" style=\"display:none;\" />
    <div class=\"clear_both\"></div><br />
    
    <div class=\"dialog_label\">Pesan :</div>
    <textarea id=\"text_message\" name=\"text_message\" rows=\"3\" cols=\"35\" style=\"resize:none;\" class=\"required dialog_input\" maxlength=\"140\" onkeyup=\"checkLength('text_message', '');\"></textarea>
    <div class=\"clear_both\"></div><br />
    
    <h4 id=\"char-left\">140 karakter tersisa.</h4>
    <div class=\"clear_both\"></div><br />
    
    <input id=\"submit\" type=\"submit\" value=\"Kirim\" class=\"submit_disabled\" disabled=\"disabled\" onclick=\"submitNewMessage();\" />
    <input id=\"reset\" type=\"reset\" value=\"Batal\" onclick=\"closeCreateNewMessage();\" class=\"dialog_button\" />
	";
}
?>