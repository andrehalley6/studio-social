<?php
session_start();
include("config.php");
require("lib/facebook.php");

include("facebook_init.php");
include("facebook_function.php");

PostComment($_SESSION['facebook_access_token'], $_POST['post_id'], urldecode($_POST['comment']), $facebook);
$comment_count = $facebook->api("/".$_POST['post_id'], "GET");
echo (isset($comment_count['comments']['count'])) ? $comment_count['comments']['count'] : "0";
?>