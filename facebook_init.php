<?php
//if (!defined('SpeedUpSocial')) { die ("Direct access not premitted"); }

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'	=> $FB_APPID,
  'secret'	=> $FB_SECRET,
  'fileUpload'	=> TRUE,
));
?>