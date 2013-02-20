<?php
// Xusix Social Network AJAX Request Handler
// Standalone/Static @ http://xusix.com/ajax.php
//
// Connections must use HTTP-POST

//$pg='ajax';
require_once "config.php";

switch($_POST['req']) {
	case 'comment':
		if(strlen(trim($_POST['txt']))) {
			$cid = x::id();
			mysql_query("INSERT INTO comments VALUES ('{$cid}','{$u_id}','{$_POST['postid']}','".x::filter_in($_POST['txt'])."',NOW())");
			if($_POST['html'])
				x::show_comment($cid,1);
			else
				echo stripslashes(trim($_POST['txt']));
		} else echo 0;
		x::notify($_POST['postid'],1,stripslashes(x::filter_in($_POST['txt'])));
		break;
	case 'like':
		x::post_like($_POST['postid']);
		break;
	case 'unlike':
		x::post_unlike($_POST['postid']);
		break;
	case 'cmsg': // check messages
		$inc = 1;
		include 'inc/addmail.php';
		break;
	case 'newcomments':
		if($_POST['cid'] && $_POST['cid']!='undefined') {
			$dt = @mysql_query("SELECT postid,datetime FROM comments WHERE cid = '".addslashes($_POST['cid'])."' LIMIT 1");
			if(@mysql_num_rows($dt)) {
				$dtr = @mysql_fetch_array($dt);
				$new = @mysql_query("SELECT cid FROM comments WHERE datetime > '{$dtr['datetime']}' AND postid = '{$dtr['postid']}' ORDER BY datetime ASC");
				if(@mysql_num_rows($new)) while($r = @mysql_fetch_assoc($new)) x::show_comment($r['cid']);
			}
		} elseif($_POST['pid']) {
			$new = @mysql_query("SELECT cid FROM comments WHERE postid = '{$_POST['pid']}' ORDER BY datetime ASC");
			if(@mysql_num_rows($new)) while($r = @mysql_fetch_assoc($new)) x::show_comment($r['cid']);
		}
		break;
	case 'setflag':
		$u_flags[$_POST['flag']] = $_POST['val'];
		x::saveflags();
		break;
	case 'getflag':
		echo $u_flags[$_POST['flag']];
		break;
	case 'deletecomment':
		@mysql_query("DELETE FROM comments WHERE `cid` = '".addslashes($_POST['cid'])."' AND `uid` = '{$u_id}' LIMIT 1");
		break;
	case 'userlist':
		x::userlist($_POST['users'],$_POST['type'],($_POST['value']) ? $_POST['value'] : 'user');
		break;
	case 'processimage':
		x::img_thumb('uc/img/'.$_POST['img'].$_POST['ext'],'uc/img/'.$_POST['img'].$_POST['dim'].$_POST['ext'],$_POST['dim']);
		echo $_POST['img'].', '.$_POST['ext'].', '.$_POST['dim'];
		break;
	case 'hovercard':
		if($u = x::pu($_POST['uid'])) {
			echo '<div><img src="'.x::userpic($_POST['uid'],96).'" alt="'.$u['user'].'" class="userpic-lg"></div>';
			echo '<div class="d">';
			echo '<p style="color:#800f02;font-size:22px;padding-bottom:4px">'.$u['fname'].' '.$u['lname'].'</p>';
			echo '<p>'.$u['email'].'<br>'.$u['user'].'</p>';
			echo '</div>';
			echo '<div class="clearfloat"></div>';
		}
		break;
	case 'chatlist':
		echo '<p class="chat-icon">Chat Unavailable</p>';
		break;
	case 'stream':
		if(!$_POST['application'])
			x::posts($_POST['user'] ? $_POST['user'] : '', $_POST['pagenum'] ? $_POST['pagenum'] : 0);
		else switch($_POST['application']) {
			case 'facebook':
				$posts_facebook = json_decode(@file_get_contents("https://graph.facebook.com/me/home?access_token={$u_apps['facebook']['token']}"));
				if($posts_facebook->data)
					foreach($posts_facebook->data as $p)
						x::show_post($p,1,0,'facebook');
				break;
			case 'twitter':
				echo '<p>Twitter Feed unavailable</p>';
		}
		break;
}

if($_REQUEST['msg']) {
	$query = mysql_query("SELECT `html` FROM `messages` WHERE `id` = '".addslashes($_REQUEST['msg'])."' AND `to` = '{$u_name}@xusix.com'");
	if(@mysql_num_rows($query)) {
		$arr = mysql_fetch_assoc($query);
		echo preg_replace('/<(?:a)([^>]+)>/i','<a target="_blank"$1>',$arr['html']);
	} else echo s('Message not available.');
}

if($_GET['req']=='deletecomment' && $_GET['cid'] && $_GET['t'] > time() - 3600) {
	@mysql_query("DELETE FROM comments WHERE `cid` = '".addslashes($_GET['cid'])."' AND `uid` = '{$u_id}' LIMIT 1");
	header("Location: ".$_SERVER['HTTP_REFERER']);
}

if($_REQUEST['optimize']) {
	x::debug_optimize(true);
}

@mysql_close();

if(isset($_POST['ismobile'])) {
	@setcookie('ismobile',$_POST['ismobile'],time()+60*60*24*30,'/','.xusix.com');
}

exit();
///// below is the old AJAX system for the discontinued mobile app ///// 

if(isset($_POST['mpage'])) {
	switch($_POST['mpage']) {
		case 'login':
			if(x::login($_POST['user'],$_POST['pass'],$_POST['redir'],true)) {
				// Good, should redirect on it's own
			} else {
				mysql_close();
				header("Location: ".$_POST['redir']."?err=1");
			}
			break;
		case 'logout':
			setcookie("xsuname","",time()-3600,"/",".xusix.com");
			header("Location: http://m.xusix.com/");
			break;
		case 'stream':
			x::mpostform($_COOKIE['xsuid']);
			x::posts('');
			break;
		case 'stream':
			x::posts('');
			break;
		default:
			$tmp_u = mysql_query("SELECT user FROM mbasic WHERE user ='".$pg[0]."' LIMIT 1");
			$pg_f = mysql_num_rows($tmp_u);
			mysql_free_result($tmp_u);
			unset($tmp_u);
			
			if($pg[0]=="me" || $pg_f) include "user.php";
			else echo '<p class="msg msgttl">Page Not Found</p>';
			
	}
}

mysql_close();

if(isset($_POST['redir'])) {
	header("Location: ".$_POST['redir']);
}

?>