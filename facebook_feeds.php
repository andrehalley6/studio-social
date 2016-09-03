<?php
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
?>
<div id="feeds-menu">
	<ul>
    	<li><a id="newsfeed" class="active" onclick="showFacebookContent('newsfeed', 'feeds-container'); changeActiveSelection('newsfeed', 'feeds-menu', '');">Berita</a></li>
    	<li><a id="photo" onclick="showFacebookContent('photo', 'feeds-container'); changeActiveSelection('photo', 'feeds-menu', '');">Foto</a></li>
    	<li><a id="profile" onclick="showFacebookContent('profile', 'feeds-container'); changeActiveSelection('profile', 'feeds-menu', '');">Profil</a></li>
	</ul>
</div>

<div id="feeds-container">
</div>