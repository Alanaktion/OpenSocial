<?php
if($pg[1]=='photos' && $pg[2]=='publish') {
	$img = array_keys($_POST);
	
	if(count($img)>1) {
		$txt = 'I added <a href="'.$a_home.$pg[0].'/photos">'.count($img).' new photos';
		$postid = x::id();
	} else {
		$txt.= 'I added <a href="'.$a_home.$pg[0].'/photos/'.$img[0].'">a new photo';
		$postid = $img[0];
	}
	$txt.= '</a> to my page.<br><br>';
	
	foreach($img as $i) {
		mysql_query("UPDATE photos SET caption = '".x::filter_in($_POST[$i])."' WHERE imgid = '{$i}' AND uid = '{$u_id}' LIMIT 1");
		$txt.= '[p:'.$i.']';
	}
	mysql_query("INSERT INTO posts VALUES ('{$u_id}','{$postid}','{$u_id}','{$txt}','0',NOW())");
	header("Location: {$a_home}{$pg[0]}/photos/success");
	exit();
} elseif($pg[1]=='photos' && $pg[3]=='setprofile' && intval($pg[4])>time()-1200) {
	mysql_query("UPDATE mbasic SET picture = '{$pg[2]}' WHERE uid = '{$u_id}' LIMIT 1");
	header("Location: {$a_home}{$pg[0]}/photos/{$pg[2]}/");
	exit();
} elseif($pg[1]=='photos' && $pg[3]=='delete' && intval($pg[4])>time()-1200) {
	$arr = @mysql_fetch_assoc(@mysql_query("SELECT ext FROM photos WHERE imgid = '{$pg[2]}' AND uid = '{$u_id}' LIMIT 1"));
	mysql_query("DELETE FROM photos WHERE imgid = '{$pg[2]}' AND uid = '{$u_id}' LIMIT 1");
	foreach(array('',28,32,64,96,128) as $i)
		@rename('uc/img/'.$pg[2].$i.$arr['ext'],'uc/img-del/'.$pg[2].$i.$arr['ext']);
	header("Location: {$a_home}{$pg[0]}/photos/delsuccess");
	exit();
} elseif($pg[1]=='photos' && $_POST['comment-txt']) {
	mysql_query("INSERT INTO comments VALUES ('".x::id()."','{$u_id}','{$pg[2]}','".x::filter_in($_POST['comment-txt'])."',NOW())");
} elseif($pg[0]=='post' && $pg[2]=='delete') {
	mysql_query("DELETE FROM posts WHERE postid = '{$pg[1]}' LIMIT 1");
	mysql_query("DELETE FROM comments WHERE postid = '{$pg[1]}'");
	mysql_query("DELETE FROM plus WHERE postid = '{$pg[1]}'");
	mysql_close();
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
} elseif($pg[0]=='post') {
	if($pg[2]=='plus' || ($pg[2]=='like' && (time() - intval($pg[3]) < 3600 * 12)))
		x::post_like($pg[1]);
	if($pg[2]=='unlike' && (time() - intval($pg[3]) < 3600 * 12))
		x::post_unlike($pg[1]);
}

if($pg_f)
	$udb_basic = mysql_fetch_array(mysql_query("SELECT * FROM mbasic WHERE user = '{$pg[0]}' LIMIT 1"));
else
	$udb_basic = mysql_fetch_array(mysql_query("SELECT * FROM mbasic WHERE user = '{$u_name}' LIMIT 1"));
$udb_details = mysql_fetch_array(mysql_query("SELECT * FROM mdetails WHERE uid = '{$udb_basic[0]}' LIMIT 1"));
$udb_privacy = mysql_fetch_array(mysql_query("SELECT * FROM privacy WHERE uid = '{$udb_basic[0]}' LIMIT 1"));

if($_POST['action']=="post" && trim($_POST['txt'])!='') {
	$postid = x::id();
	mysql_query("INSERT INTO posts VALUES ('{$u_id}','{$postid}','{$udb_basic[0]}','".x::filter_in($_POST['txt'])."','".mysql_escape_string($_POST['vis'])."',NOW())");
	x::notify($postid,0);
}
if($_POST['action']=='addfriend') mysql_query("INSERT INTO friends VALUES ('{$u_id}','{$udb_basic[0]}')");
if($_POST['action']=='delfriend') mysql_query("DELETE FROM friends WHERE uid = '{$u_id}' AND friend = '{$udb_basic[0]}' LIMIT 1");

$isfriend = mysql_num_rows(mysql_query("SELECT uid FROM friends WHERE uid = '{$u_id}' AND friend = '{$udb_basic[0]}' LIMIT 1"));

head();

if($pg[0]=='post') {
	echo '<title>'.x::excerpt('TITLE').' - Xusix</title>';

	top();
	echo '<div class="sidebar1">';
	include 'inc/nav.php';
	echo '</div><div class="content">';

	x::show_post($pg[1]);

	echo '</div><div class="sidebar2">';
	include 'inc/user-sidebar.php';
	echo '</div>';

} else switch($pg[1]) {
	case "photos":
		include "user-photos.php";
		break;
	default:
		include "user-main.php";
}

foot();
?>