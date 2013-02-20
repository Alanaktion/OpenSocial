<?php
// Short URL Redirect Script
// http://u.xusix.com/UNIQID

function urlgo($path) {
	header("Location: http://www.xusix.com/".$path);
	mysql_close();
	exit();
}

if($pg[0][0]=='-') $pg[0] = ltrim($pg[0],'-');

$t = mysql_query("SELECT postid FROM posts WHERE postid = '".$pg[0]."' LIMIT 1");
if(mysql_num_rows($t)) urlgo("post/".$pg[0]);

$t = mysql_query("SELECT user FROM mbasic WHERE uid = '".$pg[0]."' LIMIT 1");
if(mysql_num_rows($t)) { $tr = mysql_fetch_array($t); urlgo($tr[0]); }

urlgo(404);

?>