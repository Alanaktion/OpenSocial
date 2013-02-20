<?php
$t_result = mysql_query("SELECT * FROM posts WHERE pageid = '".$udb_basic[0]."' ORDER BY datetime DESC");
if(mysql_num_rows($t_result)){while($row=mysql_fetch_assoc($t_result))$db_posts[]=$row;}else $db_posts="NONE";
unset($t_result);

$t_result = mysql_query("SELECT * FROM plus WHERE uid = '".$udb_basic[0]."' LIMIT 5");
if(mysql_num_rows($t_result)){while($row=mysql_fetch_assoc($t_result))$db_plus[]=$row;}else $db_plus="NONE";
unset($t_result);

$t_result = mysql_query("SELECT * FROM comments WHERE uid = '".$udb_basic[0]."' ORDER BY datetime DESC LIMIT 5");
if(mysql_num_rows($t_result)){while($row=mysql_fetch_assoc($t_result))$db_comments[]=$row;}else $db_comments="NONE";
unset($t_result);
?>
<title><?
if($pg[1]=="plus") echo "+Plus'd by ".$udb_basic[3];
elseif($pg[1]=="comments") echo $udb_basic[3]."'s Comments";
else echo $udb_basic[3]." ".$udb_basic[4];
?> - Xusix</title>
<script type="text/javascript">function tefs(e){var charCode=(e.which)?e.which:window.event.keyCode;if(charCode==13 && !e.shiftKey){document.getElementById("frmpost").submit();return false}}</script>
<?php top(); ?>
  <div class="sidebar1">
<?php include "inc/nav.php"; ?>
    <p>
			<strong><?=$udb_basic[3]." ".$udb_basic[4]?></strong>
      <br /><em><?=$udb_basic[6]?></em>
    	<br /><?=$udb_basic[1]?>
    </p>
<?php if(!$isfriend) { ?>
		<form action="<?=$a_home.$pg[0]?>">
    	<input type="submit" value="+Friend" class="btnbig" />
    </form>
<?php } ?>
  </div>
  <div class="content">
		<form action="<?=$a_home.$pg[0]?>" method="post" class="frmpost" id="frmpost" name="frmpost">
    	<input type="hidden" name="action" value="post" />
    	<textarea name="txt" rows="2" required="required" autofocus="autofocus" onkeypress="tefs(event);"></textarea>
      <input type="submit" value="Post to <?php if($pg[0]==$u_name || $pg[0]=="me") echo "Your"; else echo $udb_basic[3]."'s"; ?> Page" />
      <div class="clearfloat"></div>
    </form>
<?php
$db_pu = array();
if($db_posts!="NONE") {
	foreach($db_posts as $post) {
		if($post['type']=="post") { // Standard Post
			// Prepare data
			$t_uid = $post['uid'];
			$t_ts = strtotime($post['datetime']." -".$timeoffset." seconds");
			$t_tz = strtotime(date('Y-m-d H:i:s')." -".$timeoffset." seconds");
			
			// Determine Logical Time Display
			if(date("y",$t_tz)==date("y",$t_ts)) {
				if(date("dmy",$t_tz)==date("dmy",$t_ts)) {
					if(date("dmyHi",$t_tz)==date("dmyHi",$t_ts))
						$t_dt = "Just Now";
					else
						$t_dt = date("g:ia",$t_ts);
				} else {
					if(date("W",$t_tz)==date("W",$t_ts)) {
						$t_dt = date("l \a\\t g:ia",$t_ts);
					} else
						$t_dt = date("F jS \a\\t g:ia",$t_ts);
				}
			} else $t_dt = date("F jS, Y \a\\t g:ia",$t_ts);
			
			// Build Main Post
			echo '    <div class="streampost">';
			if(!$db_pu[$t_uid]) // Fetch Poster Information
				$db_pu[$t_uid] = mysql_fetch_array(mysql_query("SELECT fname,lname,user FROM mbasic WHERE uid = '".$t_uid."'"));
			echo '<strong><a href="'.$a_home.$db_pu[$t_uid][2].'">'.$db_pu[$t_uid][0]." ".$db_pu[$t_uid][1].'</a></strong>';
			echo '<p>'.$post['text'].'</p>';
			
			// Build DateTime/+Plus Block
			echo '<p class="streamlinks light">';
			echo $t_dt;
			echo '&nbsp;&middot;&nbsp;';
			echo '<a href="'.$a_home."post/".$post['postid'].'/plus"><img src="data:image/gif;base64,R0lGODlhCAAIAJEAAIAPAtqYkP///////yH5BAEAAAMALAAAAAAIAAgAAAIS3ICmILDYEhO0HYicYfp1BQ4FADs=" alt="+" border="0" />Plus</a>';
			echo '&nbsp;&middot;&nbsp;';
			echo '<a href="'.$a_home."post/".$post['postid'].'">Comment</a>';
			if($t_uid==$u_id || $post['pageid']==$t_uid) echo '&nbsp;&middot;&nbsp;<a href="'.$a_home."post/".$post['postid'].'/delete">Delete</a>';
			echo '</p>';
			
			// Build +Plus'd Block (if +Plus'd)
			$t_result = mysql_query("SELECT uid FROM plus WHERE postid = '".$post['postid']."'");
			if(mysql_num_rows($t_result)) {
				echo '<div class="comment">';
				echo '<img src="data:image/gif;base64,R0lGODlhCAAIAJEAAEFBQbW1tf///////yH5BAEAAAMALAAAAAAIAAgAAAIS3ICmILDYEhO0HYicYfp1BQ4FADs=" alt="+" border="0" />';
				if(mysql_num_rows($t_result)==1) {
					$t_resuid = mysql_fetch_array($t_result);
					if(!$db_pu[$t_resuid[0]]) // Fetch +Plus'r Name
						$db_pu[$t_resuid[0]] = mysql_fetch_array(mysql_query("SELECT fname,lname,user FROM mbasic WHERE uid = '".$t_resuid[0]."'"));
					echo 'Plus\'d by <a href="'.$a_home.$db_pu[$t_resuid[0]][2].'">'.$db_pu[$t_resuid[0]][0]." ".$db_pu[$t_resuid[0]][1].'</a>';
					unset($t_resuid);
				} else
					echo 'Plus\'d by <a href="'.$a_home."post/".$post['postid'].'">'.mysql_num_rows($t_result).' people</a>';
				echo '</div>';
			}
			mysql_free_result($t_result);
			unset($t_result);
			
			// Build comment block (if comments exist)
			$t_result = mysql_query("SELECT uid FROM comments WHERE postid = '".$post['postid']."'");
			if(mysql_num_rows($t_result)) {
				echo '<div class="comment">';
				if(mysql_num_rows($t_result)==1)
					echo '<a href="'.$a_home."post/".$post['postid'].'">View 1 Comment</a>';
				else
					echo '<a href="'.$a_home."post/".$post['postid'].'">View '.mysql_num_rows($t_result).' Comments</a>';
				echo '</div>';
			}
			mysql_free_result($t_result);
			unset($t_result);
		
			// Close the post
			echo '</div>';
			
			unset($t_uid,$t_ts,$t_dt,$t_comments);
			echo "\r\n"; // New line after each post
		} else {
			echo '    <div class="streampost update update-'.$post['type'].'">';
			echo '<p>'.$post['text'].'</p>';
			echo '</div>';
		}
	}
} else {
	if($pg[0]==$u_name || $pg[0]=="me") {
		echo '<p>You don\'t have any posts yet.</p>';
		echo '<p class="light">Post on your page to share your activities with friends.</p>';
	} else {
		echo '<p>'.$udb_basic[3].' doesn\'t have any posts yet.</p>';
		echo '<p class="light">Post on '.$udb_basic[3].'\'s page to start a conversation.</p>';
	}
}
?>
  </div>
  <div class="sidebar2">
    <p><strong>+Plus</strong></p>
<?php
foreach($db_plus as $plus) {
	echo "<p class=\"small\">&quot;";
	if(str_word_count($plus['text'])>5) {
		$t_text = explode(" ",$plus['text']);
		for($i=0;$i<=4;$i++) {
			echo $t_text[$i];
			if($i!=4) echo " ";
		}
		echo '&hellip;';
	} else {
		echo $comment['text'];
	}
	echo "&quot; on ";
	if(!$db_pu[$plus['uid']]) // Fetch Poster Information
		$db_pu[$plus['uid']] = mysql_fetch_array(mysql_query("SELECT fname,lname,user FROM mbasic WHERE uid = '".$plus['uid']."'"));
	echo "<a href=\"".$db_pu[$plus['uid']][3]."\">";
	echo $db_pu[$plus['uid']][0];
	echo "</a>'s <a href=\"".$a_home."post/".$plus['postid']."\">post</a>";
	echo "</p>";
}
?>
    <p><strong>Comments</strong></p>
<?php
foreach($db_comments as $comment) {
	echo "<p class=\"small\">&quot;";
	if(str_word_count($comment['text'])>5) {
		$t_text = explode(" ",$comment['text']);
		for($i=0;$i<=4;$i++) {
			echo $t_text[$i];
			if($i!=4) echo " ";
		}
		echo '&hellip;';
	} else {
		echo $comment['text'];
	}
	echo "&quot; on ";
	if(!$db_pu[$comment['uid']]) // Fetch Poster Information
		$db_pu[$comment['uid']] = mysql_fetch_array(mysql_query("SELECT fname,lname,user FROM mbasic WHERE uid = '".$comment['uid']."'"));
	echo "<a href=\"".$db_pu[$comment['uid']][3]."\">";
	echo $db_pu[$comment['uid']][0];
	echo "</a>'s <a href=\"".$a_home."post/".$comment['postid']."\">post</a>";
	echo "</p>";
}
?>
  </div>