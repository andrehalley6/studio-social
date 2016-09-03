<?php
ini_set("display_errors", "off");
session_start();
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
?>
<div id="profile-container">
<?php
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

include("facebook_renew_access_token.php");
if(!file_get_contents("https://graph.facebook.com/me?locale=id_ID&access_token=".$access_token))
{
	$access_token = renew_access_token($access_token, $code);
}
$profile = json_decode(file_get_contents("https://graph.facebook.com/me?locale=id_ID&access_token=".$access_token));

$birthday = new DateTime($profile->birthday);
$birthday->setTimezone(new DateTimeZone(date_default_timezone_get()));
?>
    <div class="profile-picture">
        <img src="<?php echo "https://graph.facebook.com/me/picture?type=large&access_token=".$access_token; ?>" />
    </div>
    
    <div class="profile">
        <a href="http://www.facebook.com/profile.php?id=<?php echo $profile->id; ?>" target="_new"><h3><?php echo $profile->name; ?></h3></a>
        <div class="profile-detail">
        	<table class="profile-table" cellpadding="10">
            	<tbody>
                	<tr><th>Jenis Kelamin</th><td><?php echo $profile->gender; ?></td></tr>
                	<tr class="spacer"><td colspan="2"><hr /></td></tr>
                	<tr><th>Tgl. Lahir</th><td><?php echo $birthday->format("d/m/Y"); ?></td></tr>
                	<tr class="spacer"><td colspan="2"><hr /></td></tr>
                	<tr><th>Asal</th><td><?php echo isset($profile->hometown->name) ? $profile->hometown->name : "-"; ?></td></tr>
                	<tr class="spacer"><td colspan="2"><hr /></td></tr>
                	<tr><th>Lokasi</th><td><?php echo isset($profile->location->name) ? $profile->location->name : "-"; ?></td></tr>
                	<tr class="spacer"><td colspan="2"><hr /></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    
</div>