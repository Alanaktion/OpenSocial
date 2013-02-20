<?php
// X Class (Xusix Social Network Master Class)
// Copyright (c) Alan Hardman 2011-2012

// Most functions utilize an active database connection to the Xusix network

// All primary functions support usage without being logged in

class x {
	public static $db_pu = array(); // Session user data storage
	
	// Verify login details and set up session
	public static function login($user,$pass,$redirect='',$fullredirect=false) {
		global $a_home;
		
		$result = mysql_query("SELECT user,uid FROM mbasic WHERE user = '".$user."' AND pass = '".md5($pass)."'");
		if(!mysql_num_rows($result)) $result = mysql_query("SELECT user,uid,locale FROM mbasic WHERE email = '{$user}' AND pass = '".md5($pass)."'");
		if(mysql_num_rows($result)) {
			$u_array = mysql_fetch_array($result);
			setcookie('xsauth',$u_array[1].base64_encode($u_array[0]),time()+60*60*24*30,'/','.xusix.com');
			if($u_array[2]) setcookie('xslang',$u_array[2],time()+60*60*24*30,'/','.xusix.com');
			mysql_query("INSERT INTO logins VALUES ('{$u_array[1]}','{$_SERVER['REMOTE_ADDR']}','".date("Y-m-d H:i:s")."')");
			mysql_close();
			if($fullredirect)
				header('Location: '.$redirect);
			else
				header('Location: '.$a_home.$redirect);
			exit($a_home.$redirect);
		} else return false;
	}

	// DEPRECATED // Mobile webapp post form
	public static function mpostform($pid) {
		global $u_id;
		if(!$pid) $pid = $u_id;
		echo '<form method="post" onSubmit="xpost(\''.$pid.'\')" class="box" action="post/'.$pid.'">';
		echo '<input type="hidden" name="action" value="post">';
		echo '<textarea name="txt" rows="2" required="required" placeholder=""></textarea>';
		echo '<input type="submit" value="Post to ';
		if($pid==$u_id)
			echo "Your";
		else {
			$udb_basic = mysql_fetch_array(mysql_query("SELECT fname,lname,user FROM mbasic WHERE uid = '".$pid."' LIMIT 1"));
			echo $udb_basic['fname']."'s";
		}
		echo ' Page">';
		echo '</form>';
	}

	// Verify registration information and create new account
	public static function signup() {
		global $a_home,$_SERVER,$_POST,$privatekey;
		
		$p_user =strtolower($_POST['user']);
		$p_pass =$_POST['pass'];
		$p_email=strtolower($_POST['email']);
		$p_fname=$_POST['fname'];
		$p_lname=$_POST['lname'];
		
		// Validate based on security requirements
		if(strlen($p_user)<5){$p_user='';$fail="Username must be at least 5 characters.";}
		if(!preg_match('/[a-z0-9\.-]+/i',$p_user)){$p_user='';$fail='Username must only contain letters, numbers, hyphens, and periods.';}
		if(!strlen($p_pass)>5){$p_pass='';if(!$fail)$fail="Password must be at least 6 characters.";}
		if(!self::check_email($p_email)){$p_email='';if(!$fail)$fail="Email is not valid.";}
		if(!$fail && (!ctype_alpha($p_fname) || !ctype_alpha($p_lname))) $fail="First and Last Name must contain only letters.";
		if(!$fail && !($_POST['gender']=="m"||$_POST['gender']=="f")) $fail="Please select your gender.";
		// Check reCAPTCHA
		$resp=recaptcha_check_answer($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		if(!$resp->is_valid) $fail = "The reCAPTCHA wasn't entered correctly.";
		// Check username and email for availablilty
		$nouser = array("admin","administrator","index","start","login","logout","leave","signup","friends","forgot","reset","games","profile","blogs","stream","create","signup","register","search","leave","remove","delete","authorize","oauth","contact","group","groups","settings","homepage","places","place","config","configuration","resources","rescache","cache","connect","query","message","messages","posts","friend","request","requests","myself","invite","invites","mobile","invitations","intro","welcome","newuser","following","follow","followers","users","sync","unfollow","link","share");
		if(!$fail && in_array($p_user,$nouser)) $fail = "The username &quot;$p_user&quot; is not allowed.";
		if(!$fail && mysql_num_rows(mysql_query("SELECT user FROM mbasic WHERE user = '".$p_user."'"))) $fail = "The username &quot;$p_user&quot; is already taken.";
		if(!$fail && mysql_num_rows(mysql_query("SELECT email FROM mbasic WHERE email = '".$p_email."'"))) $fail = "A user with the email address &quot;$p_email&quot; already exists.";
		// If everything's good, create the account
		if(!$fail) {
			$u_id = self::id();
			$u_flags = array();
			if($_POST['fbid']) {
				$u_flags['fbid'] = $_POST['fbid'];
				$u_flags['fbtoken'] = $_POST['fbtoken'];
			}
			$bdate = $_POST['bdy']."-".str_pad($_POST['bdm'],2,"0",STR_PAD_LEFT)."-".str_pad($_POST['bdd'],2,"0",STR_PAD_LEFT);
			$query = "INSERT INTO mbasic VALUES ('";
			$query.= $u_id;
			foreach(array($p_email,md5($p_pass),$p_fname,$p_lname,$_POST['gender'],$p_user,$bdate,date("Y-m-d"),$_SERVER['REMOTE_ADDR'],$_POST['country'],$_POST['timezone']) as $e) $query.= "','".$e;
			$query.= "','{$_COOKIE['xslang']}','".addslashes(serialize($u_flags))."','')";
			mysql_query($query);
			mysql_query("INSERT INTO mdetails VALUES ('".$u_id."','','','0','','','','','','','','')");
			mysql_query("INSERT INTO privacy VALUES ('".$u_id."','y','n','y','y','n','n','n')");
			mysql_query("INSERT INTO settings VALUES ('".$u_id."','','social','')");
			mysql_query("INSERT INTO logins VALUES ('".$u_id."','".$_SERVER['REMOTE_ADDR']."','".date("Y-m-d  H:i:s")."')");
			mysql_close();
			$msg = '<h3>Welcome to Xusix, '.$p_fname.'</h3>';
			$msg.= '<p>Your account has been successfully created with the username &quot;'.$p_user.'&quot;.  You can log in from any computer or internet-capable mobile device, and by adding a cell phone number to your account, you can recieve updates through text message! If you need help, visit the <a href="'.$a_home.'help/welcome">Getting Started</a> help section.</p>';
			$msg.= '<p><a href="'.$a_home.$p_user.'">View my Page</a></p>';
			self::htmlmail($p_email,'Welcome to Xusix',$msg);
			setcookie('xsauth',$u_id.base64_encode($p_user),time()+60*60*24*30,'/','.xusix.com');
			header("Location: ".$a_home."help/welcome");
			exit();
		} else return $fail;
	}
	
	public static function delacct($uid,$redir = true) {
		mysql_query("DELETE FROM mbasic WHERE uid = '{$uid}' LIMIT 1");
		mysql_query("DELETE FROM mdetails WHERE uid = '{$uid}' LIMIT 1");
		mysql_query("DELETE FROM privacy WHERE uid = '{$uid}' LIMIT 1");
		mysql_query("DELETE FROM notify WHERE uid = '{$uid}' LIMIT 1");
		mysql_query("DELETE FROM settings WHERE uid = '{$uid}' LIMIT 1");
		mysql_query("DELETE FROM posts WHERE uid = '{$uid}' OR pageid = '{$u_id}'");
		mysql_query("DELETE FROM comments WHERE uid = '{$uid}'");
		mysql_query("DELETE FROM messages WHERE to = '{$uid}@xusix.com'");
		mysql_query("DELETE FROM pages WHERE uid = '{$uid}'");
		mysql_query("DELETE FROM plus WHERE uid = '{$uid}'");
		mysql_query("DELETE FROM photos WHERE uid = '{$uid}'");
		mysql_query("DELETE FROM friends WHERE friend = '{$uid}' OR uid = '{$u_id}'");
		if($redir) {
			@setcookie('xsauth','',time()-3600*24,'/','.xusix.com');
			header("Location: {$a_home}delacct");
		}
	}

	public static function posts($pageid,$pnum = 0) {
		global $a_home,$pg,$udb_basic,$u_name,$u_apps;
		if(!$pg[0]) $pg[0] = 'stream';
		
		if($pageid) {
			$t_result = mysql_query("SELECT * FROM posts WHERE pageid = '{$pageid}' OR uid = '{$pageid}' ORDER BY datetime DESC LIMIT ".($pnum*20).", ".(21+$pnum*20));
		} else {
			global $u_id;
			if($u_id) {
				$t_result = mysql_query("SELECT friend FROM friends WHERE uid = '{$u_id}'");
				$t_query = "SELECT * FROM posts WHERE uid in (";
				if(mysql_num_rows($t_result)) while($row=mysql_fetch_assoc($t_result)) $t_query .= "'{$row['friend']}',"; // posted by friends
				unset($t_result);
				$t_query .= "'{$u_id}') OR pageid = '{$u_id}' ORDER BY datetime DESC LIMIT ".($pnum*20).', '.(21+$pnum*20);
				$t_result = mysql_query($t_query);
			} else {
				$t_result = 'SELECT * FROM POSTS ORDER BY datetime DESC LIMIT '.($pnum*20).', '.(21+$pnum*20);
			}
		}
		if($postcount = mysql_num_rows($t_result)) {
			$i = 0;
			while($row=mysql_fetch_assoc($t_result)) {
				$i++;
				if($i<20) self::show_post($row,1);
			}
			
			if($postcount>20 || $pnum)
				echo '<br class="clr">';
			if($postcount>20 && $pnum) {
				echo '<div class="fltlft"><p class="btnrow"><a class="btn" href="'.$a_home.$pg[0].'/'.($pnum+1).'">&lsaquo; Older Posts</a></p></div>';
				echo '<div class="fltrt"><p class="btnrow"><a class="btn" href="'.$a_home.$pg[0].'/'.($pnum-1).'">Newer Posts &rsaquo;</a></p></div>';
			} elseif($postcount>20) {
				echo '<div class="fltlft"><p class="btnrow"><a class="btn" href="'.$a_home.$pg[0].'/'.($pnum+1).'">&lsaquo; Older Posts</a></p></div>';
			} elseif($pnum)
				echo '<div class="fltrt"><p class="btnrow"><a class="btn" href="'.$a_home.$pg[0].'/'.($pnum-1).'">Newer Posts &rsaquo;</a></p></div>';			
		} else {
			if(!$u_name) {
				echo '<p>No stream updates are available.</p>';
				echo '<p>You may be able to view more posts if you are logged in.</p>';
			} elseif($pg[0]==$u_name || $pg[0]=="me") {
				echo '<p>You don&#39;t have any posts yet.</p>';
				echo '<p class="light">Post on your page to share your activities with friends and colleagues.</p>';
			} elseif($pg[0]=="stream" || !$pg[0]) {
				echo '<p>No stream updates are available.</p>'
				  .'<p class="light">The home page shows stream updates from you and those you follow. If you are not following anyone, you can search for people on the <a href="'.$a_home.'users">Users page</a> or <a href="'.$a_home.'invite">invite them</a> to Xusix.</p>';
			} else {
				echo '<p>'.$udb_basic['fname'].' doesn\'t have any posts yet.</p>';
				echo '<p class="light">Post on '.$udb_basic['fname'].'\'s page to start a conversation.</p>';
			}
		}
	}
	
	// Output a fully formatted post with interactive elements
	public static function show_post($post,$comments=true,$full=false,$app=null,$single=false) {
		global $u_id,$a_home,$a_tiny,$u_apps;
		
		switch($app) {
		
case 'facebook':
	echo '<div class="streampost source-facebook" data-postid="'.$post->id.'" data-app="facebook">';
	echo '<div class="post-images">';
	$pic = json_decode(@file_get_contents("https://graph.facebook.com/{$post->from->id}/?fields=picture&access_token={$u_apps['facebook']['token']}"));
	echo '<img src="'.$pic->picture.'" alt="" title="'.$post->from->name.'" class="userpic">';
	echo '</div>';
	echo '<div class="post-content"><p class="fltlft post-meta">';
	echo '<strong><a href="#">'.$post->from->name.'</a></strong>';
	if($page->to)
		echo ' &rsaquo; <strong><a href="#">'.$post->to->name.'</a></strong>';
	echo '<br><a class="light-force" href="#" title="Permalink">'.self::displaytime(strtotime($post->created_time)).'</a>';
	echo '</p><br class="clr">';
	echo nl2br($post->message);
	echo '<div class="comment">Likes: '.$post->likes->count.' Shares: '.$post->shares->count.'</div>';
	if($post->comments->count > 2)
		echo '<div class="comment"><a href="'.$post->actions[0]->link.'" target="_blank">'.s('View All Past Comments').'</a></div>';
	if($post->comments->count > 0) foreach($post->comments->data as $c) {
		echo '<div class="comment" data-fbcid="'.$c->id.'">';
		echo '<a href="#">'.$c->from->name.'</a> ';
		echo nl2br($c->message);
		echo '<br><span class="light">'.self::displaytime(strtotime($c->created_time)).'</span>';
		echo '</div>';
	}
	echo '</div></div>';
	break;

case 'twitter':
	echo '<div class="streampost source-twitter">';
	echo '<p>Twitter post unavailble.</p>';
	echo '</div>';
	break;

default:
	if($post['type']<=2) { // Standard Post
		
		// Build Main Post
		echo '<div class="streampost" data-postid="'.$post['postid'].'">';
		self::pu($post['uid']); // Fetch Poster Information
		if($post['uid']==$post['pageid']) { // Posted to own page
			echo '<div class="post-images">';
			echo '<a href="'.$a_home.self::$db_pu[$post['uid']]['user'].'" class="userpic">';
			echo '<img src="'.self::userpic($post['uid']).'" alt="" title="'.self::$db_pu[$post['uid']]['fname'].' '.self::$db_pu[$post['uid']]['lname'].'" width="32" height="32">';
			echo '</a>';
			echo '</div>';
			echo '<div class="post-content"><p class="fltlft post-meta">';
			echo '<strong><a href="'.$a_home.self::$db_pu[$post['uid']]['user'].'" rel="author" data-uid="'.$post['uid'].'">'.self::$db_pu[$post['uid']]['fname']." ".self::$db_pu[$post['uid']]['lname'].'</a></strong>';
		} else { // Posted to other user's page
			self::pu($post['pageid']); // Fetch Page Information
			echo '<div class="post-images">';
			echo '<a href="'.$a_home.self::$db_pu[$post['uid']]['user'].'" class="userpic">';
			echo '<img src="'.self::userpic($post['uid']).'" alt="" title="'.self::$db_pu[$post['uid']]['fname'].' '.self::$db_pu[$post['uid']]['lname'].'" width="32" height="32">';
			echo '</a>';
			echo '<a href="'.$a_home.self::$db_pu[$post['pageid']]['user'].'" class="userpic">';
			echo '<img src="'.self::userpic($post['pageid']).'" alt="" title="'.self::$db_pu[$post['pageid']]['fname'].' '.self::$db_pu[$post['pageid']]['lname'].'" width="32" height="32">';
			echo '</a>';
			echo '</div>';
			echo '<div class="post-content"><p class="fltlft post-meta">';
			echo '<strong><a href="'.$a_home.self::$db_pu[$post['uid']]['user'].'" rel="author" data-uid="'.$post['uid'].'">'.self::$db_pu[$post['uid']]['fname']." ".self::$db_pu[$post['uid']]['lname'].'</a></strong> &rsaquo; <strong><a href="'.$a_home.self::$db_pu[$post['pageid']]['user'].'" rel="user" data-uid="'.$post['pageid'].'">'.self::$db_pu[$post['pageid']]['fname']." ".self::$db_pu[$post['pageid']]['lname'].'</a></strong>';
		}
		
		echo '<br>';
		echo '<a class="light-force" href="'.$a_home."post/".$post['postid'].'" title="Permalink">';
		echo self::displaytime($post['datetime']);
		echo '</a></p>';
		if($post['uid']==$u_id) {
			echo '<form class="fltrt post-vis"><select name="vis">';
			echo '<option value="0">Public</option>';
			echo '<option value="1">Private</option>';
			echo '<option value="2">Only Me</option>';
			echo '</select></form>';
		}
		echo '<br class="clr">';
		if(mb_strlen($post['text'],'UTF-8')>525 && !$full) {
			echo mb_substr(self::filter_out($post['text'],$single),0,525,'UTF-8').'&hellip;';
			echo '<p><a href="'.$a_home."post/".$post['postid'].'">'.s('View Full Post').'</a></p>';
		} else echo self::filter_out($post['text'],$single);
		
		// Build Like Block
		$t_result = mysql_query("SELECT uid FROM plus WHERE postid = '".$post['postid']."'");
		if($u_id || $rows = mysql_num_rows($t_result)) {
			echo '<div class="comment">';
			echo '<span class="post-links">';
			if(mysql_num_rows(mysql_query("SELECT uid FROM plus WHERE postid = '{$post['postid']}' AND uid = '{$u_id}'")))
				echo '<a href="'.$a_home."post/".$post['postid'].'/unlike/'.time().'" rel="unlike">'.s('Unlike').'</a>';
			else
				echo '<a href="'.$a_home."post/".$post['postid'].'/like/'.time().'" rel="like">'.s('Like').'</a>';
			if($post['uid']==$u_id || $post['pageid']==$u_id) echo ' &middot; <a href="'.$a_home."post/".$post['postid'].'/delete" rel="delete">'.s('Delete').'</a>';
			echo '</span>';
		}
		if($rows) {
			echo ' &middot; ';
			if($rows==1) {
				$t_resuid = mysql_fetch_array($t_result);
				self::pu($t_resuid['uid']);
				echo '<a href="'.$a_home.self::$db_pu[$t_resuid['uid']]['user'].'" rel="user" data-uid="'.$t_resuid['uid'].'">'.self::$db_pu[$t_resuid['uid']]['fname']." ".self::$db_pu[$t_resuid['uid']]['lname'].'</a>'.s(' likes this.');
				unset($t_resuid);
			} elseif($full) {
				while($r = mysql_fetch_assoc($t_result)) {
					$lb[] = $r['uid'];
					self::pu($r['uid']);
				}
				foreach($lb as $i=>$u) {
					echo '<a href="'.$a_home.self::$db_pu[$u]['user'].'" rel="user" data-uid="'.$u.'">'.self::$db_pu[$u]['fname']." ".self::$db_pu[$u]['lname'].'</a>';
					if($i < (count($u) - 1))
						echo ', ';
					elseif($i == (count($u) - 1))
						echo s(' and ');
				}
				echo s(' like this.');
			} else
				echo '<a href="'.$a_home."post/".$post['postid'].'" rel="liked-by">'.$rows.s(' people like this.').'</a>';
		}
		echo '</div>';
		mysql_free_result($t_result);
		unset($t_result);
		
		if($comments) {
			// Build comment block
			if($full)
				$t_result = mysql_query("SELECT uid,text,datetime,cid FROM comments WHERE postid = '{$post['postid']}' ORDER BY datetime DESC");
			else
				$t_result = mysql_query("SELECT cid FROM comments WHERE postid = '{$post['postid']}' ORDER BY datetime DESC LIMIT 5");
			if($rows = mysql_num_rows($t_result)) {
				if($rows > 4 && !$full)
					echo '<div class="comment"><a href="'.$a_home."post/".$post['postid'].'">'.s('View All Past Comments').'</a></div>';
				
				while($row = mysql_fetch_assoc($t_result)) $db_cmnt[] = $row;
				$db_cmnt = array_reverse($db_cmnt);
				
				$i = 0;
				foreach($db_cmnt as $row) {
					$i++;
					if($i>0 || $full) self::show_comment($row['cid'],$full?true:false,$post['uid']==$u_id);
				}
				unset($i,$db_cmnt);
			}
			mysql_free_result($t_result);
			unset($t_result);
		}
	
		// Display "Write a comment..." form
		if($u_id) {
			echo '<div class="comment">';
			echo '<form action="'.$a_home."post/".$post['postid'].'" method="post">';
			echo '<input type="text" name="comment-txt" placeholder="'.s('Write a comment&hellip;').'" required>';
			echo '</form></div>';
		}
	
		// Close post-content and streampost
		echo '</div></div>';
		
		unset($t_uid,$t_ts,$t_dt,$t_comments);
		echo "\r\n"; // New line after each post
	} else {
		echo '    <div class="streampost update update-'.$post['type'].'">';
		echo '<p>'.self::filter_out($post['text']).'</p>';
		echo '</div>';
	}
} // switch($app)
	}
	
	public static function show_comment($cid,$full=false,$showdel=false) {
		global $u_id;
		$c = mysql_fetch_assoc(mysql_query("SELECT `uid`,`postid`,`text`,`datetime` FROM comments WHERE `cid` = '{$cid}' LIMIT 1"));
		self::pu($c['uid']); // prepare user information
		echo '<div class="comment" data-cid="'.$cid.'">';
		echo '<a href="'.$a_home.self::$db_pu[$c['uid']]['user'].'" rel="author" data-uid="'.$c['uid'].'"><img src="'.self::userpic($c['uid']).'" class="userpic" alt="" title="'.self::$db_pu[$c['uid']]['fname'].' '.self::$db_pu[$c['uid']]['lname'].'" width="32" height="32"></a>';
		echo '<div class="comment-content">';
		echo '<strong><a href="'.$a_home.self::$db_pu[$c['uid']]['user'].'" rel="author" data-uid="'.$c['uid'].'">'.self::$db_pu[$c['uid']]['fname']." ".self::$db_pu[$c['uid']]['lname'].'</a></strong>';
		echo '&nbsp;';
		if(mb_strlen($c['text'],'UTF-8')>320 && !$full) {
			echo substr(self::filter_out($row['text']),0,320).'&hellip;';
			echo '<br><a href="'.$a_home."post/".$c['postid'].'">'.s('View Full Comment').'</a>';
		} else echo self::filter_out($c['text'],$full);
		echo '<br><span class="light">'.self::displaytime($c['datetime']).'</span>';
		if($c['uid']==$u_id || $showdel)
			echo '<span class="comment-delete"> &middot; <a href="ajax.php?req=deletecomment&cid='.$cid.'&t='.time().'">Delete</a></span>';
		echo '</div></div>';
	}
	
	// Return HTML-formatted message inbox
	public static function msg_inbox($sent = false) {
		global $u_name;
		$query = mysql_query("SELECT `id`,`from`,`subject`,`timestamp`,`read` FROM messages WHERE `to` = '{$u_name}' ORDER BY `timestamp` DESC");
		if(mysql_num_rows($query)) {
			while($r = mysql_fetch_assoc($query)) {
				if(strpos($r['from'],'<'))
					$from = trim(substr($r['from'],0,strpos($r['from'],'<')));
				else
					$from = trim(str_replace(array('<','>'),'',$r['from']));
			
				echo '<label class="message ';
				echo $r['read'] ? '' : 'un';
				echo 'read">';
				echo '<input type="checkbox" name="msg[]" value="'.$r['id'].'">';
				echo '<strong><span rel="author">'.htmlentities($from).'</span></strong>';
				echo '<a href="'.$a_home.'messages/'.$r['id'].'">';
				echo '<span class="subject">'.htmlentities($r['subject']).'</span></a>';
				echo '<span class="date light">'.self::displaytime($r['timestamp'],true).'</span>';
				echo "</label>\n";
			}
		} else {
			echo '<p class="center">'.s('You do not have any messages in your inbox.').'</p>';
			echo '<p class="center"><a href="'.$a_home.'messages/new">'.s('Send a New Message').'</a></p>';
		}
	}
	
	// View a message with included HTML meta-data
	public static function msg_view($id) {
		$query = mysql_query("SELECT `from`,`subject`,`timestamp`,`read` FROM messages WHERE `id` = '{$id}'");
		if(mysql_num_rows($query)) {
			$msg = mysql_fetch_assoc($query);
			if($msg['read']=='0') mysql_query("UPDATE messages SET `read` = '1' WHERE `id` = '{$id}' LIMIT 1");
			echo '<p><strong>From:</strong> '.htmlentities($msg['from']).'</p>';
			echo '<p><strong>Subject:</strong> '.htmlentities($msg['subject']).'</p>';
			echo '<iframe src="/ajax.php?msg='.$id.'" border="0" frameborder="0" class="appframe"></iframe>';
			echo '<p>Message received '.self::displaytime($msg['timestamp'],true).'</p>';
		} else {
			echo '<p>'.s('The message you requested is not available.').'</p>';
		}
	}
	
	// Check number of unread messages for user
	public static function msg_count_unread() {
		global $u_name;
		return mysql_num_rows(mysql_query("SELECT `id` FROM messages WHERE `read` = '0' AND `to` = '{$u_name}@xusix.com'"));
	}
	
	// Send an internal/external message through email or cross-user messaging
	public static function msg_send($to,$subject,$txt) {
		global $u_name;
		// split TO into individual recipients
		$to = explode(',',$to);
		$xto = array();
		
		foreach($to as $i=>&$r) {
			$r = strtolower(trim($r));
			$id = self::id();
			$html = nl2br(htmlentities($txt));
			mysql_query("INSERT INTO `messages` VALUES ('{$id}','{$u_name}','{$r}','{$subject}','{$txt}','{$html}','".time()."','0')");
		}
		return true;
	}
	
	// Like/unlike a post, and queue ::notify
	public static function post_like($postid) {
		global $u_id;
		if(!mysql_num_rows(mysql_query("SELECT uid FROM plus WHERE uid = '{$u_id}' AND postid = '{$postid}' LIMIT 1"))) {
			$dbt = mysql_fetch_assoc(mysql_query("SELECT text FROM posts WHERE postid = '{$postid}'"));
			mysql_query("INSERT INTO plus VALUES('{$u_id}','{$postid}','&ldquo;".self::excerpt($dbt['text'])."&rdquo;','')");
		}
		self::notify($postid,2);
	}
	public static function post_unlike($postid) {
		global $u_id;
		mysql_query("DELETE FROM plus WHERE uid = '{$u_id}' AND postid = '{$postid}' LIMIT 1");
	}
	
	// Send a notification of an action if user is subscribed to that type
	public static function notify($postid,$action=0,$comment='') { // Action 0: Post, 1: Comment, 2: Like
		global $u_id,$a_home,$a_tiny,$sitename;
		
		// Determine notification recipient
		if($action==0)
			$oid = self::pui($postid,true);
		else
			$oid = self::pui($postid);
		
		if($u_id == $oid) // User is recipient - don't notify
			return false;
		
		// Get recipient's notification preferences
		$nq = mysql_query("SELECT email,sms FROM notify WHERE uid = '{$oid}' LIMIT 1");
		if(mysql_num_rows($nq))
			$na = mysql_fetch_assoc($nq);
		else
			$na = array('email'=>'110','sms'=>'100');
		
		// Verify user wants this particular type of notification
		if(!$na['email'][$action] && !$na['sms'][$action]) // user doesn't want these notifications
			return false;
		
		// Get recipient's contact information based on their preferences for this Action
		if($na['email'][$action])
			$o_e = mysql_fetch_assoc(mysql_query("SELECT email FROM mbasic WHERE uid = '{$oid}' LIMIT 1"));
		if($na['sms'][$action]) {
			$o_c = mysql_fetch_assoc(mysql_query("SELECT cell,cellpvdr FROM mdetails WHERE uid = '{$oid}' LIMIT 1"));
			$o_c['cell'] = preg_replace('/\D/','',$o_c['cell']);
		}
		
		// Get user's basic information for display in message
		$udb = self::pu($u_id);
		
		switch($action) {
			case 0: // New post on recipient's page
				$post = mysql_fetch_assoc(mysql_query("SELECT text FROM posts WHERE postid = '{$postid}' LIMIT 1"));
				if($na['email'][$action]) {
					$msg = '<p><a href="'.$a_home.$udb['user'].'">'.$udb['fname'].' '.$udb['lname'].'</a>';
					$msg.= ' posted on your page:</p>';
					$msg.= '<p><blockquote style="font-size:16px;word-wrap:break-word;margin:1em">'.$post['txt'].'</blockquote></p>';
					$msg.= '<p><a href="'.$a_tiny.'-'.$postid.'">View Post on Xusix</a></p>';
					$msg.= '<p style="color:#777">This message was sent as a notification of activity on your Xusix page.  To disable these messages, go to the <a href="'.$a_home.'settings/#notifications">Notifications settings</a> on Xusix.</p>';
					self::htmlmail($o_e['email'],$udb['fname'].' posted on your Page',$msg);
				}
				if($na['sms'][$action]) {
					self::sms($o_c['cell'],$o_c['cellpvdr'],self::trunc($udb['fname'].' posted: '.$post['text']));
				}
				break;
			case 1: // New comment on recipient's post
				if($na['email'][$action]) {
					$msg = '<p><a href="'.$a_home.$udb['user'].'">'.$udb['fname'].' '.$udb['lname'].'</a>';
					$msg.= ' commented on your <a href="'.$a_tiny.'-'.$postid.'">post</a>:</p>';
					$msg.= '<p><blockquote style="font-size:16px;word-wrap:break-word;margin:1em">'.$comment.'</blockquote></p>';
					$msg.= '<p><a href="'.$a_tiny.'-'.$postid.'">View Post on Xusix</a></p>';
					$msg.= '<p style="color:#777">This message was sent as a notification of activity on your Xusix page.  To disable these messages, go to the <a href="'.$a_home.'settings/#notifications">Notifications settings</a> on Xusix.</p>';
					self::htmlmail($o_e['email'],$udb['fname'].' Left a Comment',$msg);
				}
				if($na['sms'][$action]) {
					self::sms($o_c['cell'],$o_c['cellpvdr'],self::trunc($udb['fname'].' commented: '.$comment));
				}
				break;
			case 2: // Liked recipient's post
				$post = mysql_fetch_assoc(mysql_query("SELECT text FROM posts WHERE postid = '{$postid}' LIMIT 1"));
				if($na['email'][$action]) {
					$msg = '<p><a href="'.$a_home.$udb['user'].'">'.$udb['fname'].' '.$udb['lname'].'</a>';
					$msg.= ' posted on your page:</p>';
					$msg.= '<p><blockquote style="font-size:16px;word-wrap:break-word;margin:1em">'.$post['txt'].'</blockquote></p>';
					$msg.= '<p><a href="'.$a_tiny.'-'.$postid.'">View Post on Xusix</a></p>';
					$msg.= '<p style="color:#777">This message was sent as a notification of activity on your Xusix page.  To disable these messages, go to the <a href="'.$a_home.'settings/#notifications">Notifications settings</a> on Xusix.</p>';
					self::htmlmail($o_e['email'],$udb['fname'].' Liked your Post',$msg);
				}
				if($na['sms'][$action]) {
					self::sms($o_c['cell'],$o_c['cellpvdr'],self::trunc($udb['fname'].' liked your post: '.$post['text']));
				}
		}
	}

	// Send a fully-formatted HTML email with Xusix branding
	public static function htmlmail($to,$subject,$msg,$from='Xusix <contact@xusix.com>') {
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'To: '.trim(strtolower($to))."\r\n";
		$headers .= 'From: '.$from."\r\n";
		
		$alt = strip_tags($msg);
		
		$message = '<body style="margin:0;padding:0">
<div style="font:12px Verdana,Arial,Helvetica,sans-serif;margin:0;padding:0">
<div style="padding:4px;color:white;background:#000;border-radius:2px">
<p><a href="'.$a_home.'">'.$sitename.'</a></p>
</div><div style="padding:4px">
'.$msg.'
<br><p style="color:#777">Copyright &copy; '.$sitename.' '.date('y').'</p></div></div></body>';
		
		file_put_contents('sender.log','HTMLMAIL: {$to}',FILE_APPEND);
		
		return mail(trim(strtolower($to)),$subject,$message,$headers);
	}

	// Send an SMS message through email or applicable API
	public static function sms($num,$carrier,$msg) {
		global $sitename;
		
		file_put_contents('sender.log','TXT: {$num} CARRIER: {$sms_cli[$carrier]} MESSAGE: {$msg}',FILE_APPEND);
		
		$num = preg_replace('/\D/','',$num); // strip non-numeric
		
		if(!$carrier) {
			return false;
		}/* If you want to use Google Voice text messages, fill in login details below and uncomment this part
		elseif($carrier=="txt.voice.google.com") {
			require_once('sms/class.xhttp.php');

			$data = array();
			$data['post'] = array(
				'accountType'=>'GOOGLE',
				'Email'  => 'EMAILADDRESS',
				'Passwd' => 'PASSWORD',
				'service'=> 'grandcentral',
				'source' => 'xusix.com-sms-0.2'
			);
			
			$response = xhttp::fetch('https://www.google.com/accounts/ClientLogin', $data);
			
			preg_match('/Auth=(.+)/', $response['body'], $matches);
			$auth = $matches[1];
			
			$data['post'] = null;
			$data['headers'] = array('Authorization' => 'GoogleLogin auth='.$auth);
			
			$response = xhttp::fetch('https://www.google.com/voice/b/0', $data);
			if(!$response['successful']) return false;
			
			preg_match("/'_rnr_se': '([^']+)'/", $response['body'], $matches);
			$rnrse = $matches[1];
	
			$data['post'] = array (
				'_rnr_se'     => $rnrse, //'aelI1OV/nE6lMqwJvkzIwrnscrg=', // alanaktion@gmail.com
				'phoneNumber' => $num, // country code + area code + phone number (international notation)
				'text'        => $msg,
				'id'          => ''
			);
			
			$response = xhttp::fetch('https://www.google.com/voice/sms/send/',$data);
			$value = json_decode($response['body']);
			return $value->ok;
		}*/
		else {
			return mail(str_replace(';',$num,$carrier),$sitename,$msg,"From: sms@xusix.com");
		}
	}
	
	// Show an Alert if the user has not closed it previously
	public static function alert($flag,$html) {
		global $u_flags,$u_id;
		$flag = 'a-'.$flag;
		if(!$u_flags[$flag] && $u_id) {
			echo '<div class="alert js" data-alert="'.$flag.'"><p>';
			echo $html;
			echo '</p><p><a href="#" class="btn" rel="alert-dismiss" data-flag="'.$flag.'">Dismiss</a></p>';
			echo '</div>';
		}
	}
	
	// Get user ID from post
	public static function pui($pid,$pageid=false) {
		if($pageid) $tmp = mysql_fetch_array(mysql_query("SELECT pageid FROM posts WHERE postid = '{$pid}' LIMIT 1"));
		else $tmp = mysql_fetch_array(mysql_query("SELECT uid FROM posts WHERE postid = '{$pid}' LIMIT 1"));
		return $tmp[0];
	}
	
	// Get user ID from username
	public static function puu($user) {
		$tmp = @mysql_fetch_assoc(mysql_query("SELECT uid FROM mbasic WHERE user = '{$user}' LIMIT 1"));
		return $tmp['uid'];
	}
	
	// Load user information into session
	public static function pu($uid) {
		if(!self::$db_pu[$uid])
			self::$db_pu[$uid] = mysql_fetch_array(mysql_query("SELECT fname,lname,user,email FROM mbasic WHERE uid = '{$uid}' LIMIT 1"));
		return self::$db_pu[$uid];
	}
	
	// Verify an email address (syntax-based)
	public static function check_email($email) {
		if(!ereg("^[^@]{1,64}@[^@]{1,255}$",$email)) return false;
		$email_array=explode("@",$email);
		$local_array=explode(".",$email_array[0]);
		for($i=0;$i<sizeof($local_array);$i++)if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",$local_array[$i]))return false;
		if(!ereg("^\[?[0-9\.]+\]?$",$email_array[1])){
			$domain_array = explode(".",$email_array[1]);
			if(sizeof($domain_array)<2) return false;
			for($i=0;$i<sizeof($domain_array);$i++)if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$",$domain_array[$i]))return false;
		}
		return true;
	}
	
	// Format a time based on how long ago it was
	public static function displaytime($t_dt,$is_stamp = false) {
		global $timeoffset;
		
		if($is_stamp)
			$t_ts = $t_dt;
		else
			$t_ts = strtotime($t_dt)-$timeoffset;
		$t_tz = time()-$timeoffset;
		
		if(date("y",$t_tz)==date("y",$t_ts)) {
			if(date("dmy",$t_tz)==date("dmy",$t_ts)) {
				if(date("dmyHi",$t_tz)==date("dmyHi",$t_ts))
					return s('Just Now');
				else
					return date("g:ia",$t_ts).' Today';
			} else {
				if(date("W",$t_tz)==date("W",$t_ts)) {
					return date("l \a\\t g:ia",$t_ts);
				} else
					return date("F jS \a\\t g:ia",$t_ts);
			}
		} else return date("F jS, Y \a\\t g:ia",$t_ts);
	}
	public static function excerpt($text) { // Create a logical short form of a body of text
		$text = strip_tags($text);
		if(str_word_count($text)>5) {
			$tarr = explode(' ',$text);
			$post_excerpt = '';
			for($i=0;$i<=4;$i++) {
				$post_excerpt.= $tarr[$i];
				if($i!=4) $post_excerpt.=' ';
			}
			return $post_excerpt.'&hellip;';
		} elseif(mb_strlen($text,'UTF-8')>65) {
			return mb_substr($text,0,65,'UTF-8').'&hellip;';
		} else return $text;
	}
	public static function trunc($txt,$len=140) { // Force a shortened form of a body of text (Generally for SMS)
		$txt = strip_tags($txt);
		$parts = preg_split('/([\s\n\r]+)/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);
		$parts_count = count($parts);
		
		$length = 0;
		$last_part=0;
		for(;$last_part<$parts_count;$last_part++) {
			$length += strlen($parts[$last_part]);
			if($length > $len) break;
		}
		
		return implode(array_slice($parts,0,$last_part));
	}
	
	// Save current user's flags array to the database
	public static function saveflags() {
		global $u_flags, $u_id;
		mysql_query("UPDATE mbasic SET flags = '".addslashes(serialize($u_flags))."' WHERE `uid` = '{$u_id}' LIMIT 1");
	}
	
	// Generate ID and save image, queue thumbnail generation
	public static function img_save($source,$thmbs = false) {
		$dir = 'uc/img/';
		$id = self::id();
		
		$size = getimagesize($source);
		$ext = image_type_to_extension($size[2]);
		if($ext=='.jpeg') $ext = '.jpg';
		move_uploaded_file($source,$dir.$id.$ext);
		
		/* Save JPEG of image
		$ir = imagecreatefromstring(file_get_contents($source));
		imagejpeg($ir,$dir.$id.'.jpg',90);
		*/
		
		// generate thumbnails based on moved uploaded file
		if($thmbs)
			foreach(array(28,32,64,96,128) as $dim)
				self::img_thumb($dir.$id.$ext,$dir.$id.$dim.'.jpg',$dim);
				// self::img_thumb($dir.$id.$ext,$dir.$id.$dim.$ext,$dim);
		return array($id,$ext);
	}
	
	// Generate cross-format image thumbnails
	public static function img_thumb($source,$dest,$dim = 96) {
		$img = imagecreatefromstring(file_get_contents($source)); // load image file
		$res = imagecreatetruecolor($dim,$dim);  // create empty canvas for thumbnail
		$w = imagecolorallocate($res,255,255,255); // allocate white background color
		imagefill($res,0,0,$w);                    // fill image with white
		
		// get smaller of image's dimensions
		$d = (imagesx($img)>imagesy($img)) ? imagesy($img) : imagesx($img);
		
		// crop, resize, and copy from source image
		imagecopyresampled($res,$img,0,0,(imagesx($img)-$d)/2,(imagesy($img)-$d)/2,$dim,$dim,$d,$d);
		
		imagejpeg($res,$dest);
		
		/*
		$convertdir = '/usr/local/bin/';
		
		$size = getimagesize($source);
		$ext = image_type_to_extension($size[2]);
		if($ext=='.jpeg') $ext = '.jpg';
		
		// use ImageMagick to generate thumbnails
		//exec($convertdir."convert $source -coalesce $dest");
		
		// size based on orientation
		if($size[0]>$size[1]) {
			exec($convertdir."convert {$source} -resize x{$dim} -gravity center  -crop {$dim}x{$dim}+0+0 +repage {$dest}");
		} elseif($size[1]>$size[0]) {
			exec($convertdir."convert {$source} -resize {$dim}x -gravity center  -crop {$dim}x{$dim}+0+0 +repage {$dest}");
		} else {
			exec($convertdir."convert {$source} -resize {$dim}x -gravity center  +repage {$dest}");
		}
		*/
		
		/*
		$source = imagecreatefromstring(file_get_contents($source));
		$resized = imagecreatetruecolor($dim,$dim);
		if(imagesx($source)>imagesy($source))
			imagecopyresampled($resized,$source,0,0,(imagesx($source)-imagesy($source)) / 2,0,$dim,$dim,imagesy($source),imagesy($source));
		else
			imagecopyresampled($resized,$source,0,0,0,(imagesy($source)-imagesx($source)) / 2,$dim,$dim,imagesx($source),imagesx($source));
		imagejpeg($resized,$dest,90);
		*/
		
		return $dest;
	}
	
	// Fetch address of user profile picture based on UID
	public static function userpic($uid,$size=32) {
		global $a_home;
		$arr = @mysql_fetch_assoc(mysql_query("SELECT picture FROM mbasic WHERE uid = '{$uid}' LIMIT 1"));
		$arr = @mysql_fetch_assoc(mysql_query("SELECT imgid,ext FROM photos WHERE imgid = '{$arr['picture']}'"));
		if($arr['imgid'])
			//return $a_home.'uc/img/'.$arr['imgid'].$size.'.jpg';
			return $a_home.'uc/img/'.$arr['imgid'].$size.$arr['ext'];
		else
			return $a_home.'img/profile'.$size.'.png';
	}
	
	// ImageMagick Version
	public static function imagic_ver() {
		$convert_path = '/usr/local/bin/convert --version';
		$return =  shell_exec($convert_path);
		$x = preg_match('/Version: ImageMagick ([0-9]*\.[0-9]*\.[0-9]*)/', $return, $arr_return);
		return $arr_return[1];
	}
	
	// User content filters (cross-compatability and security)
	public static function filter_in($txt) {
		global $a_home;
		
		// Strip HTML, convert newlines, and encode
		$txt = nl2br(htmlentities(trim($txt),ENT_QUOTES,'UTF-8'));
		
		// Convert BBCode to secure HTML
		$bb1 = array('/\[b\](.*?)\[\/b\]/is','/\[i\](.*?)\[\/i\]/is','/\[u\](.*?)\[\/u\]/is','/\[url\=(.*?)\](.*?)\[\/url\]/is','/\[url\](.*?)\[\/url\]/is','/\[align\=(left|center|right)\](.*?)\[\/align\]/is','/\[img\](.*?)\[\/img\]/is','/\[mail\=(.*?)\](.*?)\[\/mail\]/is','/\[mail\](.*?)\[\/mail\]/is','/\[font\=(.*?)\](.*?)\[\/font\]/is','/\[size\=(.*?)\](.*?)\[\/size\]/is','/\[color\=(.*?)\](.*?)\[\/color\]/is','/\[codearea\](.*?)\[\/codearea\]/is','/\[code\](.*?)\[\/code\]/is','/\[p\](.*?)\[\/p\]/is');
		$bb2 = array('<strong>$1</strong>','<em>$1</em>','<u>$1</u>','<a href="$1" rel="nofollow" target="_blank">$2</a>','<a href="$1" rel="nofollow" target="_blank">$1</a>','<div style="text-align: $1;">$2</div>','<img src="$1" alt="">','<a href="mailto:$1">$2</a>','<a href="mailto:$1">$1</a>','<span style="font-family:$1">$2</span>','<span style="font-size:$1">$2</span>','<span style="color:$1">$2</span>','<textarea class="code_container" rows="30" cols="70">$1</textarea>','<pre class="code">$1</pre>','<p>$1</p>');
		$txt = preg_replace($bb1,$bb2,$txt);
		
		// Unify Emoji
		include_once 'emoji.php';
		$txt = emoji_docomo_to_unified($txt);   // DoCoMo devices
		$txt = emoji_kddi_to_unified($txt);     // KDDI & Au devices
		$txt = emoji_softbank_to_unified($txt); // Softbank & Apple devices
		$txt = emoji_google_to_unified($txt);   // Google Android devices
		
		return $txt;
	}
	public static function filter_out($txt,$fullwidth=false) {
		global $a_home,$is_ios,$isuserpage;
		
		// Cross-platform Emoji
		include_once 'emoji.php';
		if($is_ios)
			$txt = emoji_unified_to_softbank($txt);
		if(strpos($_SERVER['HTTP_USER_AGENT'],'Android'))
			$txt = emoji_unified_to_google($txt);
		else
			$txt = emoji_unified_to_html($txt);
		
		// @username links
		//$txt = preg_replace('/(?<=^|\s)@([a-z0-9_]+)/i','<a href="'.$a_home.'$1">@$1</a>',$txt);
		
		// @user replace with Full Name and hyperlink
		preg_match_all('/(?<=^|\s)@([a-z0-9_]+)/i',$txt,$mentions);
		foreach($mentions[0] as $u) {
			// get full name from username, then replace username with hyperlink in each instance.
			if($id = self::puu(strtolower(trim($u,'@')))) {
				$nfo = self::pu($id);
				$txt = str_replace($u,'<a href="'.$a_home.trim($u,'@').'" title="'.$u.'" rel="user" data-uid="'.$id.'">'.$nfo['fname'].' '.$nfo['lname'].'</a>',$txt);
			}
		}
		
		// replace [p:ID] with in-stream photo or thumbnail gallery
		preg_match_all('[\[p\:([A-Fa-f0-9]+)\]]',$txt,$ptags);
		if($ptags[1]) $txt .= '<p>';
		foreach($ptags[1] as $i=>$p) {
			if(strlen($p)==13)
				$p = self::id($p);			
			$qry = mysql_query("SELECT `uid`,`ext`,`caption` FROM photos WHERE `imgid` = '{$p}' LIMIT 1");
			if(mysql_num_rows($qry)) {
				$img = mysql_fetch_assoc($qry);
				self::pu($img['uid']);
			} else {
				$p = 'error';
				$img = array(
					'uid'    =>'404',
					'ext'    =>'.png',
					'caption'=>'Image Not Available'
				);
			}
			$txt = str_replace($ptags[0][$i],'',$txt);
			if(count($ptags[1])>2)
				$txt .= '<a href="'.$a_home.self::$db_pu[$img['uid']]['user'].'/photos/'.$p.'"><img src="'.$a_home.'uc/img/'.$p.'96.jpg'.'" alt="'.$p.'" title="'.$img['caption'].'" width="96" height="96">';
			elseif(count($ptags[1])>1) // 237px
				$txt .= '<a href="'.$a_home.self::$db_pu[$img['uid']]['user'].'/photos/'.$p.'"><img src="'.$a_home.'imgload.php?id='.$p.'&ext='.$img['ext'].'&w=237&h=280" alt="'.$p.'" class="half-width" title="'.$img['caption'].'">';
			else // 484px
				$txt .= '<a href="'.$a_home.self::$db_pu[$img['uid']]['user'].'/photos/'.$p.'"><img src="'.$a_home.'imgload.php?id='.$p.'&ext='.$img['ext'].'&w=484&h=280" alt="'.$p.'" class="full-width" title="'.$img['caption'].'">';
		}
		if($ptags[1]) $txt .= '</p>';
		
		// add YouTube embeds
		/*preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $txt, $ytids);
		if($ytids) foreach($ytids as $i=>$v) {
			$txt.= '<p><iframe class="youtube-player" type="text/html" width="'.($fullwidth?912:416).'" height="'.($fullwidth?513:234).'" src="http://www.youtube.com/embed/'.$v.'" frameborder="0"></iframe></p>';
		}*/
		
		return make_clickable($txt);
	}
	
	public static function userlist($users = 0,$type = 0,$value = 'user') {
		// $users:       $type:
		// 0: following  0: listing
		// 1: followers  1: single-select (radio)
		// 2: both       2: multi-select (checkbox)
		// 3: new users (10 last registered users)
		// 4: popular   (10 most followed users)
		
		// $value: column to use for <input> value attribute
		
		global $a_home,$u_id;
		
		// Get requested user list
		$i = ''; $ul = array();
		switch($users) {
			case 0:
				$uq = mysql_query("SELECT friend FROM friends WHERE uid = '{$u_id}'");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_array($uq))
						$i .= "'".$r[0]."',";
				$i = rtrim($i,',');
				$uq = mysql_query("SELECT uid,fname,lname,user FROM mbasic WHERE uid in ({$i}) ORDER BY fname ASC, lname ASC");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_assoc($uq))
						$ul[] = $r;
				break;
				
			case 1:
				$uq = mysql_query("SELECT uid FROM friends WHERE friend = '{$u_id}'");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_array($uq))
						$i .= "'".$r[0]."',";
				$i = rtrim($i,',');
				$uq = mysql_query("SELECT uid,fname,lname,user FROM mbasic WHERE uid in ({$i}) ORDER BY fname ASC, lname ASC");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_assoc($uq))
						$ul[] = $r;
				break;
				
			case 2:
				$uq = mysql_query("SELECT friend FROM friends WHERE uid = '{$u_id}'");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_array($uq))
						$i .= "'".$r[0]."',";
				$uq = mysql_query("SELECT uid FROM friends WHERE friend = '{$u_id}'");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_array($uq))
						$i .= "'".$r[0]."',";
				$i = rtrim($i,',');
				$uq = mysql_query("SELECT uid,fname,lname,user FROM mbasic WHERE uid in ({$i}) ORDER BY fname ASC, lname ASC");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_assoc($uq))
						$ul[] = $r;
				break;
				
			case 3:
				$uq = mysql_query("SELECT uid,fname,lname,user FROM mbasic ORDER BY joindate DESC LIMIT 10");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_array($uq))
						$ul[] = $r;
				break;
			case 4:
				$uq = mysql_query("SELECT `friend`,COUNT(*) as count FROM `friends` GROUP BY `friend` ORDER BY count DESC LIMIT 10");
				if(@mysql_num_rows($uq))
					while($r = mysql_fetch_array($uq))
						$ul[] = @mysql_fetch_array(mysql_query("SELECT uid,fname,lname,user FROM mbasic WHERE uid = '{$r[0]}' LIMIT 1"));
		}
		unset($uq);
		
		echo '<div class="ulist">';
		if($type) echo '<form action="#" method="post">';
		if($ul) foreach($ul as $u) {
			echo '<p class="ulist-user" data-uid="'.$u['uid'].'">';
			switch($type) {
				case 0:
					echo '<a href="'.$a_home.$u['user'].'">';
					break;
				case 1:
					echo '<label><input type="radio" name="user" value="'.$u[$value].'"><span class="a">';
					break;
				case 2:
					echo '<label><input type="checkbox" name="user" value="'.$u[$value].'"><span class="a">';
					break;
			}
			echo '<img src="'.self::userpic($u['uid'],28).'" alt="" width="28" height="28" class="userpic">';
			echo '<strong>'.$u['fname'].' '.$u['lname'].'</strong>';
			switch($type) {
				case 0:
					echo '</a><br>';
					break;
				case 1:
				case 2:
					echo '</span><br>';
					break;
			}
			echo '<em class="light">'.$u['user'].'</em>';
			if($type) echo '</label>';
			echo '</p>';
		} else switch($users) {
			case 0:
			case 2:
				echo '<p class="light">'.s('You are not following anyone on Xusix').'</p>';
				break;
			case 1:
				echo '<p class="light">'.s('You do not have any followers.').'</p>';
				break;
			case 3:
				echo '<p class="light">'.s('No new users are available.').'</p>';
				break;
		}
		if($type) echo '</form>';
		echo '</div>';
	}
	
	// Execute an HTTP POST request with cURL and return the response
	public static function http_post($url,$params) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	// Generate a 16-digit unique integer
	public static function id($hex='') {
		return number_format(hexdec($hex ? $hex : uniqid()),0,'','');
	}
	
	// DEBUGGING: Generate JPEG thumbnails for other image types
	public static function debug_thumbs() {
		$q = mysql_query("SELECT imgid,ext FROM photos");
		if(mysql_num_rows($q)) while($r = mysql_fetch_array($q))
			foreach(array(28,32,64,96,128) as $dim) {
				self::img_thumb('uc/img/'.$r[0].$r[1],'uc/img/'.$r[0].$dim.'.jpg',$dim);
				echo $r[0].$dim.'<br>';
			}
	}
	
	// DEBUGGING: Delete thumbnails
	public static function debug_del_thumbs() {
		$h = opendir('uc/img/');
		while($f = readdir($h)) {
			if(strlen($f)>strlen('1405268460367764.jpg'))
				unlink('uc/img/'.$f);
		}
		closedir($h);
	}
	
	// DEBUGGING: Unify emoji in all existing posts and comments
	public static function debug_emoji() {
		include_once 'emoji.php';
		
		$q = mysql_query("SELECT text,postid FROM posts");
		while($r = mysql_fetch_assoc($q)) $p[] = $r;
		unset($q,$r);
		
		foreach($p as &$r) {
			$r['text'] = emoji_docomo_to_unified($r['text']);   // DoCoMo devices
			$r['text'] = emoji_kddi_to_unified($r['text']);     // KDDI & Au devices
			$r['text'] = emoji_softbank_to_unified($r['text']); // Softbank & (iPhone) Apple devices
			$r['text'] = emoji_google_to_unified($r['text']);   // Google Android devices
			mysql_query("UPDATE posts SET text = '{$r['text']}' WHERE postid = '{$r['postid']}' LIMIT 1");
		}
		unset($p);
		
		
		$q = mysql_query("SELECT text,datetime FROM comments");
		while($r = mysql_fetch_assoc($q)) $c[] = $r;
		unset($q,$r);
		
		foreach($c as &$r) {
			$r['text'] = emoji_docomo_to_unified($r['text']);   // DoCoMo devices
			$r['text'] = emoji_kddi_to_unified($r['text']);     // KDDI & Au devices
			$r['text'] = emoji_softbank_to_unified($r['text']); // Softbank & (iPhone) Apple devices
			$r['text'] = emoji_google_to_unified($r['text']);   // Google Android devices
			mysql_query("UPDATE comments SET text = '{$r['text']}' WHERE datetime = '{$r['datetime']}' LIMIT 1");
		}
		unset($c);
	}
	
	// DEBUGGING: Generate comment IDs for any comments without one
	public static function debug_commentid() {
		$query = mysql_query("SELECT `cid`,`datetime`,`text` FROM comments WHERE `cid` = ''");
		while($r = mysql_fetch_assoc($query))
			if(!$r['cid'])
				mysql_query("UPDATE comments SET `cid` = '".uniqid()."' WHERE `datetime` = '{$r['datetime']}' AND `text` = '".addslashes($r['text'])."' LIMIT 1");
		mysql_free_result($query);
		unset($query);
	}
	
	// DEBUGGING: Generate null array serialized Flags column
	public static function debug_flags() {
		mysql_query("UPDATE mbasic SET `flags` = 'a:0:{}' WHERE `flags` = ''");
	}
	
	// DEBUGGING: Optimize all MySQL Tables
	public static function debug_optimize($output = false) {
		$stat = mysql_query('SHOW TABLE STATUS');
		if(mysql_num_rows($stat))
			while($r = mysql_fetch_array($stat)) {
				mysql_query('OPTIMIZE TABLE '.$r[0]);
				if($output) echo $r[0].'<br>';
			}
		mysql_free_result($stat);
		unset($stat);
	}
	
	// Advertising handlers (cross-platform/device)
	public static function showad($small=false,$client='ca-pub-8924338699009980') {
		if($_COOKIE['ismobile']) echo
'<div class="ga-m">
<script type="text/javascript"><!--
google_ad_client = "'.$client.'";
/* Xusix Mobile */
google_ad_slot = "0100873914";
google_ad_width = 320;
google_ad_height = 50;
//--></script>';
		elseif($small) echo
'<div class="ga-sm">
<script type="text/javascript"><!--
google_ad_client = "'.$client.'";
/* Xusix Small Ads */
google_ad_slot = "2518333369";
google_ad_width = 180;
google_ad_height = 150;
//--></script>';
		else echo
'<div class="ga">
<script type="text/javascript"><!--
google_ad_client = "'.$client.'";
/* Xusix Nav Ads */
google_ad_slot = "8067917263";
google_ad_width = 160;
google_ad_height = 600;
//--></script>';
		echo '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script></div>';
	}
}

// Additional global functions outside of class scope

function _make_url_clickable_cb($matches) {
	$ret = '';
	$url = $matches[2];

	if(empty($url))
		return $matches[0];
	// removed trailing [.,;:] from URL
	if(in_array(substr($url,-1),array('.',',',';',':')) === true) {
		$ret = substr($url,-1);
		$url = substr($url,0,strlen($url)-1);
	}
	return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target=\"_blank\">$url</a>".$ret;
}

function _make_web_ftp_clickable_cb($matches) {
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://'.$dest;
 
	if (empty($dest))
		return $matches[0];
	// removed trailing [,;:] from URL
	if (in_array(substr($dest,-1),array('.',',',';',':')) === true) {
		$ret = substr($dest,-1);
		$dest = substr($dest,0,strlen($dest)-1);
	}
	return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" target=\"_blank\">$dest</a>".$ret;
}

function _make_email_clickable_cb($matches) {
	$email = $matches[2].'@'.$matches[3];
	return $matches[1]."<a href=\"mailto:$email\">$email</a>";
}

function make_clickable($ret) {
	$ret = ' '.$ret;
	// in testing, using arrays here was found to be faster
	$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#!$%&~/.\-;:=,?@\[\]+]*)#is','_make_url_clickable_cb',$ret);
	$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#!$%&~/.\-;:=,?@\[\]+]*)#is','_make_web_ftp_clickable_cb',$ret);
	$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i','_make_email_clickable_cb',$ret);
 
	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>",$ret);
	$ret = trim($ret);
	return $ret;
}

//$order has to be either asc or desc
function sortmulti($array, $index, $order, $natsort=FALSE, $case_sensitive=FALSE) {
	if(is_array($array) && count($array)>0) {
		foreach(array_keys($array) as $key) 
			$temp[$key] = $array[$key][$index];
		if(!$natsort) {
			if($order=='asc')
				asort($temp);
			else
				arsort($temp);
		} else  {
			if($case_sensitive===true)
				natsort($temp);
			else
				natcasesort($temp);
			if($order!='asc') 
				$temp=array_reverse($temp,TRUE);
		}
		foreach(array_keys($temp) as $key) 
			if(is_numeric($key))
				$sorted[]=$array[$key];
			else
				$sorted[$key]=$array[$key];
		return $sorted;
	}
	return $sorted;
}


?>