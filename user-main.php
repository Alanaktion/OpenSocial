<?php
$t_result = mysql_query("SELECT * FROM plus WHERE uid = '{$udb_basic[0]}' LIMIT 7");
if(mysql_num_rows($t_result)){while($row=mysql_fetch_assoc($t_result))$db_plus[]=$row;}else $db_plus="NONE";
unset($t_result);

$t_result = mysql_query("SELECT * FROM comments WHERE uid = '{$udb_basic[0]}' ORDER BY datetime DESC LIMIT 5");
if(mysql_num_rows($t_result)){while($row=mysql_fetch_assoc($t_result))$db_comments[]=$row;}else $db_comments="NONE";
unset($t_result);

$t_result = mysql_query("SELECT * FROM photos WHERE uid = '{$udb_basic[0]}' ORDER BY date DESC LIMIT 6");
if(mysql_num_rows($t_result)){while($row=mysql_fetch_assoc($t_result))$db_photos[]=$row;}else $db_plus=0;
unset($t_result);
?>
<title><?=$udb_basic['fname'].' '.$udb_basic['lname']?> - Xusix</title>
<?php top(); ?>
<div id="main" class="stag-on">
	<div class="userinfo">
		<a href="<?php echo $a_home.$pg[0]; if($udb_basic['picture']) echo '/photos/'.$udb_basic['picture']; ?>" class="fltlft">
			<img src="<?=x::userpic($udb_basic['uid'],96)?>" alt="<?=$udb_basic['user']?>" class="userpic-lg">
		</a>
		<div class="fltlft">
			<h2><?=$udb_basic['fname'].' '.$udb_basic['lname']?></h2>
			<em>www.xusix.com/<?=$udb_basic['user']?></em>
<?php if(!($pg[0]==$u_name||$pg[0]=='me')){ if(!$isfriend) { ?>
			<form action="<?=$a_home.$pg[0]?>" method="post">
				<input type="hidden" name="action" value="addfriend">
				<input type="submit" value="<?=s('Follow')?>">
			</form>
<?php } else { ?>
			<form action="<?=$a_home.$pg[0]?>" method="post" class="nomargin">
				<input type="hidden" name="action" value="delfriend">
				<input type="submit" value="<?=s('Unfollow')?>">
			</form>
<?php }} ?>
		</div>
		<br class="clr">
		<br>
		<div class="fltlft">
			<h3>Photos (<?php
$q = @mysql_fetch_array(mysql_query("SELECT COUNT(imgid) FROM photos WHERE uid = '{$udb_basic['uid']}'"));
echo $q[0];	
?>)</h3>
<?php if($db_photos) {
echo '<p class="gallery-sm">';
foreach($db_photos as $pic) {
	echo '<a href="'.$a_home.$pg[0].'/photos/'.$pic['imgid'].'">';
	echo '<img src="'.$a_home.'uc/img/'.$pic['imgid'].'64'.$pic['ext'].'" alt="'.$pic['imgid'].'" title="'.strip_tags($pic['caption']).'">';
	echo '</a>';
}
echo '</p>';
echo '<p><a href="'.$a_home.$pg[0].'/photos">'.s('View All Photos').'</a></p>';
} else {
if($pg[0]==$u_name || $pg[0]=="me") {
	echo '<p class="light">'.s('You do not have any photos.').'</p>';
	echo '<p><a href="'.$a_home.$pg[0].'/photos">'.s('Upload Photos').'</a></p>';
} else echo '<p class="light">'.$udb_basic['fname'].s(' does not have any photos.').'</p>';
} ?>
		</div>
		<div class="fltrt">
			<h3>Contacts</h3>
			<div class="fltlft">
				<a href="<?=$a_home.$pg[0]?>/following" rel="following" data-uid="<?=$udb_basic['uid']?>">Following (<?php 
$q = mysql_fetch_array(mysql_query("SELECT COUNT(friend) FROM friends WHERE uid = '{$udb_basic['uid']}'"));
echo $q[0];
?>)</a><br>
<?php
$q = mysql_query("SELECT friend FROM friends WHERE uid = '{$udb_basic['uid']}' ORDER BY RAND() LIMIT 5");
if(mysql_num_rows($q))
	while($r = mysql_fetch_array($q)) {
		$qu = x::pu($r[0]);
		echo '<a href="'.$a_home.$qu['user'].'" title="'.$qu['fname'].' '.$qu['lname'].'" rel="user" data-uid="'.$r[0].'"><img src="'.x::userpic($r[0],28).'" alt=""></a>';
	}
?>
			</div>
			<div class="fltlft">
				<a href="<?=$a_home.$pg[0]?>/followers" rel="followers" data-uid="<?=$udb_basic['uid']?>">Followers (<?php 
$q = mysql_fetch_array(mysql_query("SELECT COUNT(uid) FROM friends WHERE friend = '{$udb_basic['uid']}'"));
echo $q[0];
?>)</a><br>
<?php
$q = mysql_query("SELECT uid FROM friends WHERE friend = '{$udb_basic['uid']}' ORDER BY RAND() LIMIT 5");
if(mysql_num_rows($q))
	while($r = mysql_fetch_array($q)) {
		$qu = x::pu($r[0]);
		echo '<a href="'.$a_home.$qu['user'].'" title="'.$qu['fname'].' '.$qu['lname'].'" rel="user" data-uid="'.$r[0].'"><img src="'.x::userpic($r[0],28).'" alt=""></a>';
	}
?>
			</div>
			<br class="clr">
		</div>
		<br class="clr">
	</div>
	<form action="<?=$a_home.$pg[0]?>" method="post" class="frmpost pageitem" id="frmpost" name="frmpost">
		<input type="hidden" name="action" value="post">
		<div class="textcontain">
			<textarea name="txt" rows="2" required onkeypress="tefs(event);" placeholder="What&#39;s Up?"></textarea>
		</div>
		<select name="vis" title="Visibility">
			<option value="0">Public</option>
			<option value="1">Private</option>
			<option value="2"><?php echo ($pg[0]==$u_name || $pg[0]=='me') ? s('Only Me') : $udb_basic['fname'].' and I'; ?></option>
		</select>
		<input type="submit" value="Post to <?php echo ($pg[0]==$u_name || $pg[0]=='me') ? s('Your') : $udb_basic['fname'].'&#39;s'; ?> Page">
		<br class="clr">
	</form>
	<div class="pageitem user-activity">
<?php @include 'inc/user-sidebar.php'; ?>
	</div>
<?php x::posts($udb_basic['uid'],$pg[1]); ?>
	<br class="clr">
<div class="ga">
<script type="text/javascript"><!--
google_ad_client = "ca-pub-8924338699009980";
/* Xusix Leaderboard */
google_ad_slot = "3715072068";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>