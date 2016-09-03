<?php
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
?>
<div id="feeds-menu">
	<ul>
    	<li><a id="newsfeed" class="active" onclick="showTwitterContent('newsfeed', 'feeds-container'); changeActiveSelection('newsfeed', 'feeds-menu', '');">Berita</a></li>
    	<li><a id="mention" onclick="showTwitterContent('mention', 'feeds-container'); changeActiveSelection('mention', 'feeds-menu', '');">Disebut</a></li>
    	<li><a id="profile" onclick="showTwitterContent('profile', 'feeds-container'); changeActiveSelection('profile', 'feeds-menu', '');">Profil</a></li>
	</ul>
</div>

<div id="feeds-container">
</div>