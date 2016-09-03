<?php
session_start();
unset($_SESSION['ot'], $_SESSION['ots'], $_SESSION['twitter_screen_name'], $_SESSION['ov'], $_SESSION['ck'], $_SESSION['cs'], $_SESSION['twitterInit']);
echo "<script>self.location.href='index.php'</script>";
?>