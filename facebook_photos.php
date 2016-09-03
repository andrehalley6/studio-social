<?php
ini_set("display_errors", "off");
session_start();
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
?>
<div id="photoalbums-container">
<?php
(isset($_POST['offset']) && $_POST['offset'] > 0) ? $offset = $_POST['offset'] : $offset = 0;
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

//limit
$limit = 4;

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/me/albums?locale=id_ID&limit=1&offset=".$offset."&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$albums = json_decode(file_get_contents("https://graph.facebook.com/me/albums?locale=id_ID&limit=".$limit."&offset=".$offset."&access_token=".$access_token));
if(empty($albums->data))	//if empty data, return to first page
{
	$offset = 0;
	$albums = json_decode(file_get_contents("https://graph.facebook.com/me/albums?locale=id_ID&limit=".$limit."&offset=".$offset."&access_token=".$access_token));
}
$total_albums = count($albums->data);

for($i = 0; $i < $total_albums; $i++)
{
	if(isset($albums->data[$i]->cover_photo) || isset($albums->data[$i]->count))
	{
		if ($i%4 == 3 || $i == $total_albums-1) echo "<div class=\"fb_albums last\">";
		else echo "<div class=\"fb_albums\">";
		echo "
			<a><img src=\"https://graph.facebook.com/".$albums->data[$i]->cover_photo."/picture?type=album&access_token=".$access_token."\" onclick=\"detailPhotos('".$albums->data[$i]->id."', 'feeds-container');\" /></a>
			<div class=\"album_desc\">
				<h3>".$albums->data[$i]->name."</h3>
				<p>".$albums->data[$i]->count." foto</p>
				
				<ul class=\"action_box\">
					<li><a href=\"".$albums->data[$i]->link."\" target=\"_new\">Lihat di Facebook</a></li>
				</ul>
			</div>
		</div>
		";
	}
	else	//empty albums -> Albums with 0 photos, no cover photos
	{
		echo "
		<div class=\"fb_albums\">
			<img src=\"img/questionmark.jpg\" />
			<div class=\"album_desc\">
				<h3>".$albums->data[$i]->name."</h3>
				<p>0 foto</p>
				
				<ul class=\"action_box\">
					<li><a href=\"".$albums->data[$i]->link."\" target=\"_new\">Lihat di facebook</a></li>
				</ul>
			</div>
		</div>
		";
	}
}
?>
</div>

<div id="paging_box">
    <a id="first" class="link" onclick="changeAlbumsPage('0', 'feeds-container');"></a>
    <div id="paging_button" >
        <ul>
            <li><a id="prev" class="link" onclick="changeAlbumsPage('<?php echo ($offset-$limit); ?>', 'feeds-container');"></a></li>
            <li><a id="next" class="link" onclick="changeAlbumsPage('<?php echo ($offset+$limit); ?>', 'feeds-container');"></a></li>
        </ul>
    </div>
</div>