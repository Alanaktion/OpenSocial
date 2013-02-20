<?php head(); ?>
<title>Invite - Xusix</title>
<?php top(); ?>
<div class="container">
	<div class="sidebar1">
<?php include "inc/nav.php"; ?>
		<p>Invite your friends to Xusix to share messages, photos, and games with them, and help Xusix grow.</p>
	</div>
	<div class="content">
		<h1 class="frmsearch">Invite to Xusix</h1>
<?php
if($pg[1]=="send" && $_POST['email']) {
	$udb_basic = mysql_fetch_array(mysql_query("SELECT fname,lname,user FROM mbasic WHERE uid = '{$u_id}' LIMIT 1"));
	x::htmlmail($_POST['email'],'Invitation to Xusix',"{$udb_basic[0]} {$udb_basic[1]} has invited you to Xusix!<br>Xusix is a new way to communicate quickly with your friends and coworkers.<br><em>".str_replace(array("\r\n","\r","\n"),array("\n","\n","<br>"),$_POST['msg'])."</em><br><br><p><a href=\"http://www.xusix.com/join/{$udb_basic[2]}\"><img src=\"http://xusix.com/img/joinnow.png\" alt=\"Join Xusix\" title=\"Join Xusix\"></a></p>");
?>
		<p>Your invitation has been sent.</p>
		<p><a href="<?=$a_home?>invite">Send More</a></p>
<?php } else { ?>
		<p>
			<form action="<?=$a_home?>invite/send" method="post">
				Email Addresses:<br>
				<input type="text" name="email" required style="width:520px"><br>
				<span class="light">Separate multiple addresses with commas.</span>
				<br><br>
				Personal Message (Optional):<br>
				<textarea name="msg" style="width:520px;height:100px"></textarea><br>
				<input type="submit" value="Send Invitations">
			</form>
		</p>
<?php
	}
	if($u_flags['fbtoken']) {
		$friends = json_decode(file_get_contents("https://graph.facebook.com/me/friends?access_token={$u_flags['fbtoken']}"));
		echo '<div class="fltlft"><strong>Facebook Friends on Xusix</strong><br>';
		$qry = "SELECT * FROM `mbasic` WHERE `flags` REGEXP '";
		foreach($friends->data as $f) $qry.=$f->id.'|';
		$q = mysql_query(rtrim($qry,'|')."'");
		if(mysql_num_rows($q)) {
			$a = mysql_fetch_assoc($q);
			foreach($a as $f) {
				echo $f['fname'].' '.$f['lname'].'<br>';
			}
		} else {
			echo 'No facebook friends are on Xusix (and connected to Facebook)';
		}
		echo '</div>';
		
		echo '<div class="fltlft"><strong>All Facebook Friends</strong><br>';
		foreach($friends->data as $f) {
			echo '<img src="https://graph.facebook.com/'.$f->id.'/picture?access_token='.$u_flags['fbtoken'].'" alt="'.$f->id.'">';
			echo $f->name.'<br>';
		}
		echo '</div>';
	} else {
		echo 'connect 2 fakebookz (not here, go to Photos to do itz)';
	}
?>
	</div>
</div>
<?php foot(); ?>