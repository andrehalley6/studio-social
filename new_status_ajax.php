<?php
ini_set("display_errors", "off");
session_start();
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
$fb_checkbox = "";
$twitter_checkbox = "";
if(!$_SESSION['facebook_access_token']) $fb_checkbox = "style=\"display:none;\"";
if(!$_SESSION['ot'] && !$_SESSION['ots'])	$twitter_checkbox = "style=\"display:none;\"";
if($_SESSION['ot'] && $_SESSION['ots'])	//if login twitter then limit input maxlength 140 character.
{
	$twitter_length = "<h4 id=\"char-left\">140 karakter tersisa.</h4>
					<div class=\"clear_both\"></div><br />";
	$twitter_maxlength = "onkeyup=\"checkLength('status-message', 'form-container');\" maxlength=\"140\"";
}

if($_POST['type'] == "status")
{
	echo "
    <div class=\"dialog_long_label\">Status :</div>
	<textarea id=\"status-message\" name=\"message\" rows=\"5\" cols=\"35\" style=\"resize:none;\" ".$twitter_maxlength." class=\"required dialog_input\"></textarea>
    <div class=\"clear_both\"></div><br />
	
	<div id=\"checkbox-container\">
	<div class=\"dialog_long_label\">Ubah Status di :</div>
	<input type=\"checkbox\" name=\"post-feed[]\" value=\"facebook\" class=\"required checkbox db\" ".$fb_checkbox." />".((!empty($fb_checkbox)) ? "" : "&nbsp;Facebook&nbsp;&nbsp;&nbsp;")."
    <input type=\"checkbox\" name=\"post-feed[]\" value=\"twitter\" class=\"checkbox db\" ".$twitter_checkbox." />".(!empty($twitter_checkbox) ? "" : "&nbsp;Twitter")."
	</div>
	<div class=\"clear_both\"></div><br />
	
	".$twitter_length."
    <input id=\"submit\" type=\"submit\" value=\"Kirim\" class=\"dialog_button\" onclick=\"submitNewStatus();\" />
	<input id=\"reset\" type=\"reset\" value=\"Batal\" onclick=\"closeStatusDialog();\" class=\"dialog_button\" />
	";
}
else	//type = photo, using uploadify & create new album (not work on speedup browser)
{
	//get facebook albums
	$albums = json_decode(file_get_contents("https://graph.facebook.com/me/albums?locale=id_ID&access_token=".$_SESSION['facebook_access_token']));
	$total_albums = count($albums->data);
	echo "
	<script type=\"text/javascript\" language=\"javascript\">
	$(function(){
		$('#photo_upload').uploadify({
			'uploader'			: 'js/uploadify/uploadify.swf',
			'script'			: 'js/uploadify/uploadify.php',
			'cancelImg'			: 'js/uploadify/cancel.png',
			'buttonText'		: 'Pilih Foto', 
			'folder'			: 'var/temp/',
			'fileExt'			: '*.jpg; *.jpeg; *.png',
			'fileDesc'			: 'Foto (.JPG, .JPEG .PNG)',
			'method'			: 'post',
			'auto'				: true,
			'removeCompleted' 	: true,
			'queueSizeLimit'	: 1,
			'onComplete'		: function(event, ID, fileObj, response, data)
								{
									$('#temp_img').attr('src', 'var/temp/' + fileObj.name);
									$('#photo_name').attr('value', 'var/temp/' + fileObj.name);
								}
		});
	});
	</script>
	
	<img src=\"img/questionmark.jpg\" height=\"120\" width=\"120\" align=\"left\" id=\"temp_img\" />
	<div class=\"clear_both\"></div>
	<input type=\"hidden\" name=\"photo_name\" id=\"photo_name\" value=\"\" class=\"required\" />
	<input type=\"file\" name=\"photo_upload\" id=\"photo_upload\" size=\"30\" value=\"\" />
	<div class=\"clear_both\"></div><br />";
	
	// show albums
	echo "<div class=\"dialog_long_label\">Pilih Album :</div>";
	echo "<select name=\"facebook_albums\" id=\"facebook_albums\" onchange=\"enableNewAlbums();\" class=\"dialog_input\">";
	for($i = 0; $i < $total_albums; $i++)
	{
		if($albums->data[$i]->can_upload == 1)	//user can upload to certain albums
		{
			echo "<option value=\"".$albums->data[$i]->id."\">".$albums->data[$i]->name."</option>";
		}
	}
	echo "<option value=\"new_albums\">Buat Album Baru</option>";	// add options to create new albums
	echo "</select>";
	echo "<div class=\"clear_both\"></div>";
	
	echo "
    <div class=\"dialog_long_label\">Nama Album :</div>
	<input type=\"text\" name=\"album_name\" id=\"album_name\" value=\"\" class=\"required dialog_input\" size=\"30\" disabled=\"disabled\" style=\"background-color:#ccc;\" />
	<div class=\"clear_both\"></div>
	
    <div class=\"dialog_long_label\">Deskripsi :</div>
	<input type=\"text\" name=\"album_description\" id=\"album_description\" value=\"\" class=\"required dialog_input\" size=\"30\" disabled=\"disabled\" style=\"background-color:#ccc;\" />
	<div class=\"clear_both\"></div>
	
	<div class=\"dialog_long_label\">Judul Foto :</div>
	<input type=\"text\" id=\"photo_caption\" name=\"photo_caption\" value=\"\" class=\"required dialog_input\" size=\"30\" />
	<div class=\"clear_both\"></div><br />
	
    <input id=\"submit\" type=\"submit\" value=\"Unggah\" class=\"dialog_button\" onclick=\"submitNewStatus();\" />
	<input id=\"reset\" type=\"reset\" value=\"Batal\" onclick=\"closeStatusDialog();\" class=\"dialog_button\" />
	";
}
?>