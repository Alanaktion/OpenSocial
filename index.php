<?php
require 'config.php';

// Load Requested Page Template
if($pg[0]=='ig') {
	require 'ig.php';
	mysql_close();
	exit();
} elseif($pg[0]=='sitemap.xml') {
	require 'sitemap.php';
	mysql_close();
	exit();
}
if($_SERVER['HTTP_HOST']==$a_mdns) {
	require 'index.m.php';
} elseif($_SERVER['HTTP_HOST']==$a_udns || $pg[0][0]=='-') {
	require 'url.php';
} else {
	if($u_name) {
		if($pg[0]=='a' && $u_name==$u_admin) require 'a.php';
		elseif($pg[0]=='test'||$pg[0]=='tst') require 'tst.php';
		elseif(!$pg[0]||$pg[0]=='stream') require 'home.php';
		elseif($pg[0]=='settings') require 'settings.php';
		elseif($pg[0]=='contacts') require 'friends.php';
		elseif($pg[0]=='apps') require 'apps.php';
		elseif($pg[0]=='app') require 'appcontainer.php';
		elseif($pg[0]=='messages') require 'messages.php';
		elseif($pg[0]=='post') require 'post.php';
		elseif($pg[0]=='help') require 'help.php';
		elseif($pg[0]=='invite') require 'invite.php';
		elseif($pg[0]=='games') require 'games.php';
		elseif($pg[0]=='share') require 'share.php';
		elseif($pg[0]=='logout') require 'guest.php';
		else {
			$tmp_u = mysql_query("SELECT user FROM mbasic WHERE user ='{$pg[0]}' LIMIT 1");
			$pg_f = mysql_num_rows($tmp_u);
			mysql_free_result($tmp_u);
			unset($tmp_u);
			
			if($pg[0]=='me' || $pg_f) {
				$isuserpage = true;
				require 'user.php';
			} elseif($u_name) require '404.php';
			else require 'guest.php';
		}
	} else {
		if($pg[0]=='pr') require 'forgot.php';
		else require 'guest.php';
	}
}

mysql_close();
?>