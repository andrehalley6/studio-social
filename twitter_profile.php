<?php 
//twitter lib
require("lib/twitter/EpiCurl.php");
require("lib/twitter/EpiOAuth.php");
require("lib/twitter/EpiTwitter.php");

session_start(); 

if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}

$twitterInit = new EpiTwitter($_SESSION['ck'], $_SESSION['cs'], $_SESSION['ot'], $_SESSION['ots']);

include("twitter_authenticate_session.php");

$twitterInfo = $twitterInit->get('/account/verify_credentials.json');
$twitterInfo->response;
?>
<div id="profile-container">

	<div class="profile-picture">
    	<img class="twitter_pic" src="<?php echo $twitterInfo['profile_image_url']; ?>" />
    </div>
    
    <div class="profile">
		<a href="http://www.twitter.com/<?php echo $twitterInfo['screen_name']; ?>" target="_new">
        	<h3><?php echo $twitterInfo['name']."&nbsp;&nbsp;(@".$twitterInfo['screen_name'].")"; ?></h3>
		</a>
        <div class="profile-detail">
            <table class="profile-table" cellpadding="10">
            	<tbody>
                	<tr><th>Tweet</th><td><?php echo $twitterInfo['statuses_count']; ?></td></tr>
                	<tr class="spacer"><td colspan="2"><hr /></td></tr>
                	<tr><th>Pengikut</th><td><?php echo $twitterInfo['followers_count']; ?></td></tr>
                	<tr class="spacer"><td colspan="2"><hr /></td></tr>
                	<tr><th>Mengikuti</th><td><?php echo $twitterInfo['friends_count']; ?></td></tr>
                	<tr class="spacer"><td colspan="2"><hr /></td></tr>
                	<tr><th>Lokasi</th><td><?php echo $twitterInfo['location']; ?></td></tr>
                	<tr class="spacer"><td colspan="2"><hr /></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    
</div>