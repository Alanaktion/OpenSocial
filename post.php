<?php

if($pg[2]=="delete") {
	if(mysql_num_rows(mysql_query("SELECT postid FROM posts WHERE postid = '{$pg[1]}' AND (pageid = '{$u_uid}' OR uid = '{$u_id}') LIMIT 1"))) {
		mysql_query("DELETE FROM posts WHERE postid = '{$pg[1]}' LIMIT 1");
		mysql_query("DELETE FROM comments WHERE postid = '{$pg[1]}')");
		mysql_query("DELETE FROM plus WHERE postid = '{$pg[1]}'");
	}
	mysql_close();
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}

$post = mysql_query("SELECT * FROM posts WHERE postid = '{$pg[1]}' LIMIT 1");

if(!mysql_num_rows($post)) {
	include "404.php";
	mysql_close();
	exit();
}

$db_post = mysql_fetch_assoc($post);
$db_pu[$db_post['uid']] = mysql_fetch_array(mysql_query("SELECT * FROM mbasic WHERE uid = '{$db_post['uid']}' LIMIT 1"));

if(strlen(trim($_POST['comment-txt']))) {
	mysql_query("INSERT INTO comments VALUES ('".uniqid()."','{$u_id}','{$pg[1]}','".x::filter_in($_POST['txt'])."',NOW())");
	x::notify($pg[1],1,stripslashes(x::filter_in($_REQUEST['txt'])));
}

if($pg[2]=='plus' || ($pg[2]=='like' && (time() - intval($pg[3]) < 3600 * 12)))
	x::post_like($pg[1]);
if($pg[2]=='unlike' && (time() - intval($pg[3]) < 3600 * 12))
	x::post_unlike($pg[1]);

head();
?>
<title><?=x::excerpt($db_post['text']);?> - Xusix</title>
<?php top(); ?>
<div id="main">
<?php x::show_post($db_post,true,true,null,true); ?>
</div>
<?php foot(); ?>