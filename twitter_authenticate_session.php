<?php
//config
include("config.php");

if(!$_SESSION['ot'] && !$_SESSION['ots']){
	$twitterObj = new EpiTwitter($CONSUMER_KEY, $CONSUMER_SECRET);
	echo "<script>self.location.href='".$twitterObj->getAuthorizeUrl()."'</script>";
}
?>