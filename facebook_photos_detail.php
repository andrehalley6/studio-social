<?php
ini_set("display_errors", "off");
session_start();
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
?>
<div id="photos-container">
<?php
(isset($_POST['offset']) && $_POST['offset'] > 0) ? $offset = $_POST['offset'] : $offset = 0;
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";
(isset($_POST['album_id'])) ? $album_id = $_POST['album_id'] : $album_id = "";

//limit
$limit = 8;

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/".$album_id."/photos?locale=id_ID&limit=1&offset=".$offset."&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$photos = json_decode(file_get_contents("https://graph.facebook.com/".$album_id."/photos?locale=id_ID&limit=".$limit."&offset=".$offset."&access_token=".$access_token));
if(empty($photos->data))	//if empty data, return to first page
{
	$offset = 0;
	$photos = json_decode(file_get_contents("https://graph.facebook.com/".$album_id."/photos?locale=id_ID&limit=".$limit."&offset=".$offset."&access_token=".$access_token));
}
$total_photos = count($photos->data);

for($i = 0; $i < $total_photos; $i++)
{
	if ($i%4 == 3) echo "<div class=\"fb_photos last\">";
	else echo "<div class=\"fb_photos\">";
	echo "
		<a href=\"".$photos->data[$i]->link."\" target=\"_new\">
			<img src=\"".$photos->data[$i]->picture."\" title=\"".$photos->data[$i]->name."\" alt=\"".$photos->data[$i]->name."\" />
		</a>
	</div>
	";
}
?>
</div>

<div id="paging_box">
    <a id="first" class="link" onclick="changePhotosPage('0', '<?php echo $album_id; ?>', 'feeds-container');"></a>
    <div id="paging_button">
        <ul>
            <li><a id="prev" class="link" onclick="changePhotosPage('<?php echo ($offset-$limit); ?>', '<?php echo $album_id; ?>', 'feeds-container');"></a></li>
            <li><a id="next" class="link" onclick="changePhotosPage('<?php echo ($offset+$limit); ?>', '<?php echo $album_id; ?>', 'feeds-container');"></a></li>
        </ul>
    </div>
</div>