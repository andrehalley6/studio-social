<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Ubah Status</title>

<script type="text/javascript" language="javascript">
$(document).ready(function(){
	changeType('status', 'form-container');
});
</script>

</head>

<body>
<form id="form_update_status" name="form_update_status" action="" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="radio" name="type" onclick="changeType('status', 'form-container');" checked="checked" value="status" class="radio rd" />Ubah Status
    <?php if($_SESSION['facebook_access_token']): ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="radio" name="type" onclick="changeType('photo', 'form-container');" value="photo" class="radio rd" />Tambah Foto (Facebook)
    <div class="clear_both"></div>
    <?php endif ?>
    
    <div id="form-container">	
    </div>
</form>

</body>
</html>