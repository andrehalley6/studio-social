<?php
session_start();
include("config.php");
$url = $_GET['url'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login Facebook</title>

<script type="text/javascript" language="javascript" src="js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function(){
	$("#twitter-loginbox").load('<?php echo $url; ?> #twitter-loginbox');
});
</script>
</head>

<body>
	<div id="twitter-loginbox"></div>
</body>
</html>