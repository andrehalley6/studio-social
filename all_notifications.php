<?php
session_start();

if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}

include("php_function.php");

(isset($_POST['page']) && $_POST['page'] > 0) ? $offset = $_POST['page'] : $offset = 0;
(isset($_SESSION['facebook_access_token'])) ? $access_token = $_SESSION['facebook_access_token'] : $access_token = "";
(isset($_SESSION['facebook_code'])) ? $code = $_SESSION['facebook_code'] : $code = "";

echo "Twitter tidak ada notifikasi, jadi redirect user ke facebook notifications.";
?>