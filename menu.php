<?php	//params = type, fb_name, fb_url, twitter_name, twitter_url
session_start();
if(!isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' ))
{
	die ("Direct access not premitted");
}
$type = $_GET['type'];
$fb_url = $_GET['fb_url'];
$twitter_url = $_GET['twitter_url'];

if($_SESSION['facebook_name'] == "")	//login twitter
{
	if($type == "m")
	{
		echo "
		<ul>
			<li>
				<a id=\"all-message\" class=\"button\" style=\"display:none;\" onclick=\"showContent('am', 'content'); changeActiveSelection('all-message', 'button-con', 'button');\">
					<h3>Pesan</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-message\" class=\"button href\" href=\"".$fb_url."\">
					<h3>Facebook</h3><span>login</span>
				</a>
			</li>
			<li>
				<a id=\"twitter-message\" class=\"button\" onclick=\"showContent('tm', 'content'); changeActiveSelection('twitter-message', 'button-con', 'button');\">
					<h3>Twitter</h3><span>".$_SESSION['twitter_screen_name']."</span>
				</a>
			</li>
		</ul>
		";
	}
	elseif($type == "n")
	{
		echo "
		<ul>
			<li>
				<a id=\"all-notification\" class=\"button\" style=\"display:none;\" onclick=\"showContent('an', 'content'); changeActiveSelection('all-notification', 'button-con', 'button');\">
					<h3>Pemberitahuan</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-notification\" class=\"button href\" href=\"".$fb_url."\">
					<h3>Facebook</h3><span>login</span>
				</a>
			</li>
			<li style=\"display:none;\">
				<a id=\"twitter-notification\" class=\"button\" onclick=\"showContent('tn', 'content'); changeActiveSelection('twitter-notification', 'button-con', 'button');\">
					<h3>Twitter</h3><span>".$_SESSION['twitter_screen_name']."</span>
				</a>
			</li>
		</ul>
		";
	}
	else
	{
		echo "
		<ul>
			<li>
				<a id=\"all-feed\" class=\"button\" style=\"display:none;\" onclick=\"showContent('af', 'content'); changeActiveSelection('all-feed', 'button-con', 'button');\">
					<h3>Berita</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-feed\" class=\"button href\" href=\"".$fb_url."\">
					<h3>Facebook</h3><span>login</span>
				</a>
			</li>
			<li>
				<a id=\"twitter-feed\" class=\"button\" onclick=\"showContent('tf', 'content'); changeActiveSelection('twitter-feed', 'button-con', 'button');\">
					<h3>Twitter</h3><span>".$_SESSION['twitter_screen_name']."</span>
				</a>
			</li>
		</ul>
		";
	}
}
elseif($_SESSION['twitter_screen_name'] == "")	//login facebook
{
	if($type == "m")
	{
		echo "
		<ul>
			<li>
				<a id=\"all-message\" class=\"button\" style=\"display:none;\" onclick=\"showContent('am', 'content'); changeActiveSelection('all-message', 'button-con', 'button');\">
					<h3>Pesan</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-message\" class=\"button\" onclick=\"showContent('fm', 'content'); changeActiveSelection('facebook-message', 'button-con', 'button');\">
					<h3>Facebook</h3><span>".$_SESSION['facebook_name']."</span>
				</a>
			</li>
			<li>
				<a id=\"twitter-message\" class=\"button href\" href=\"".$twitter_url."\">
					<h3>Twitter</h3><span>login</span>
				</a>
			</li>
		</ul>
		";
	}
	elseif($type == "n")
	{
		echo "
		<ul>
			<li>
				<a id=\"all-notification\" class=\"button\" style=\"display:none;\" onclick=\"showContent('an', 'content'); changeActiveSelection('all-notification', 'button-con', 'button');\">
					<h3>Pemberitahuan</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-notification\" class=\"button\" onclick=\"showContent('fn', 'content'); changeActiveSelection('facebook-notification', 'button-con', 'button');\">
					<h3>Facebook</h3><span>".$_SESSION['facebook_name']."</span>
				</a>
			</li>
			<li style=\"display:none;\">
				<a id=\"twitter-notification\" class=\"button href\" href=\"".$twitter_url."\">
					<h3>Twitter</h3><span>login</span>
				</a>
			</li>
		</ul>
		";
	}
	else
	{
		echo "
		<ul>
			<li>
				<a id=\"all-feed\" class=\"button\" style=\"display:none;\" onclick=\"showContent('af', 'content'); changeActiveSelection('all-feed', 'button-con', 'button');\">
					<h3>Berita</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-feed\" class=\"button\" onclick=\"showContent('ff', 'content'); changeActiveSelection('facebook-feed', 'button-con', 'button');\">
					<h3>Facebook</h3><span>".$_SESSION['facebook_name']."</span>
				</a>
			</li>
			<li>
				<a id=\"twitter-feed\" class=\"button href\" href=\"".$twitter_url."\">
					<h3>Twitter</h3><span>login</span>
				</a>
			</li>
		</ul>
		";
	}
}
else	//login facebook & twitter
{
	if($type == "m")
	{
		echo "
		<ul>
			<li>
				<a id=\"all-message\" class=\"button\" onclick=\"showContent('am', 'content'); changeActiveSelection('all-message', 'button-con', 'button');\">
					<h3>Pesan</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-message\" class=\"button\" onclick=\"showContent('fm', 'content'); changeActiveSelection('facebook-message', 'button-con', 'button');\">
					<h3>Facebook</h3><span>".$_SESSION['facebook_name']."</span>
				</a>
			</li>
			<li>
				<a id=\"twitter-message\" class=\"button\" onclick=\"showContent('tm', 'content'); changeActiveSelection('twitter-message', 'button-con', 'button');\">
					<h3>Twitter</h3><span>".$_SESSION['twitter_screen_name']."</span>
				</a>
			</li>
		</ul>
		";
	}
	elseif($type == "n")
	{
		echo "
		<ul>
			<li>
				<a id=\"all-notification\" class=\"button\" onclick=\"showContent('an', 'content'); changeActiveSelection('all-notification', 'button-con', 'button');\">
					<h3>Pemberitahuan</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-notification\" class=\"button\" onclick=\"showContent('fn', 'content'); changeActiveSelection('facebook-notification', 'button-con', 'button');\">
					<h3>Facebook</h3><span>".$_SESSION['facebook_name']."</span>
				</a>
			</li>
			<li style=\"display:none;\">
				<a id=\"twitter-notification\" class=\"button\" onclick=\"showContent('tn', 'content'); changeActiveSelection('twitter-notification', 'button-con', 'button');\">
					<h3>Twitter</h3><span>".$_SESSION['twitter_screen_name']."</span>
				</a>
			</li>
		</ul>
		";
	}
	else
	{
		echo "
		<ul>
			<li>
				<a id=\"all-feed\" class=\"button\" onclick=\"showContent('af', 'content'); changeActiveSelection('all-feed', 'button-con', 'button');\">
					<h3>Berita</h3>
				</a>
			</li>
			<li>
				<a id=\"facebook-feed\" class=\"button\" onclick=\"showContent('ff', 'content'); changeActiveSelection('facebook-feed', 'button-con', 'button');\">
					<h3>Facebook</h3><span>".$_SESSION['facebook_name']."</span>
				</a>
			</li>
			<li>
				<a id=\"twitter-feed\" class=\"button\" onclick=\"showContent('tf', 'content'); changeActiveSelection('twitter-feed', 'button-con', 'button');\">
					<h3>Twitter</h3><span>".$_SESSION['twitter_screen_name']."</span>
				</a>
			</li>
		</ul>
		";
	}
}
?>