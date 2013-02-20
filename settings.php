<?php

if(isset($_POST['phone']))
	$_POST['phone'] = preg_replace('/[^\d]/','',$_POST['phone']);
if(isset($_POST['cell']))
	$_POST['cell']  = preg_replace('/[^\d]/','',$_POST['cell']);

if($_POST['saveinterface'])
	mysql_query("UPDATE settings SET meebo = '{$_POST['meebo']}', ads = '{$_POST['ads']}' WHERE uid = '{$u_id}' LIMIT 1");

// Delete all user data if authorized
if($_POST['delacct'] && $_POST['action']="srslydeleteit")
	x::delacct($u_id);

head();
?>
<script type="text/javascript"><!--
// pushState control
$(function(){
	$('#pushStateOn').click(function(){
		$.post('/ajax.php',{
			req: 'setflag',
			flag: 'pushState',
			val: 1
		},function(data){
			alert('Experimental Loading has been enabled.\nThe loader will take effect the next time you load a page.');
		});
	});
	$('#pushStateOff').click(function(){
		$.post('/ajax.php',{
			req: 'setflag',
			flag: 'pushState',
			val: 0
		},function(data){
			alert('Experimental Loading has been disabled.\nThis page will refresh automatically to disable the loader.');
			self.location = '<?=$a_home?>settings';
		});
	});
	
	if(u_flags.hovercards)
		$('#hCardsOn').hide();
	else
		$('#hCardsOff').hide();
	$('#hCardsOn').click(function(){
		$(this).attr('disabled','disabled');
		$.post('/ajax.php',{
			req: 'setflag',
			flag: 'hovercards',
			val: 1
		},function(data){
			alert('Hovercards are now enabled. This change will take effect the next time you load a page.');
			$('#hCardsOn').removeAttr('disabled').hide();
			$('#hCardsOff').show();
		});
	});
	$('#hCardsOff').click(function(){
		$(this).attr('disabled','disabled');
		$.post('/ajax.php',{
			req: 'setflag',
			flag: 'hovercards',
			val: 0
		},function(data){
			$('#hCardsOff').removeAttr('disabled').hide();
			$('#hCardsOn').show();
		});
	});
});
// --></script>
<title>Settings - Xusix</title>
<?php top(); ?>
<div id="main">
	<nav class="nav-side">
		<h2>Settings</h2>
		<a href="<?=$a_home?>settings/profile"<?=($pg[1]=='profile'||!$pg[1])?' class="current"':''?>>Profile</a>
		<a href="<?=$a_home?>settings/privacy"<?=($pg[1]=='privacy')?' class="current"':''?>>Privacy</a>
		<a href="<?=$a_home?>settings/notifications"<?=($pg[1]=='notifications')?' class="current"':''?>>Notifications</a>
		<a href="<?=$a_home?>settings/sources"<?=($pg[1]=='sources')?' class="current"':''?>>Sources</a>
		<a href="<?=$a_home?>settings/experimental"<?=($pg[1]=='experimental')?' class="current"':''?>>Experimental</a>
		<a href="<?=$a_home?>settings/deleteaccount"<?=($pg[1]=='deleteaccount')?' class="current"':''?>>Delete Account</a>
	</nav>
	<div class="fltflex div-settings">
	<form action="<?=$a_home?>settings/<?=$pg[1]?>" method="post">
<?php
switch($pg[1]) {
	case 'deleteaccount':
?>
	<p>If you no longer want to keep your account on Xusix, you can delete it.</p>
	<p>
		<input type="hidden" name="action" value="srslydeleteit">
		<input type="submit" name="delacct" value="Delete Account Permanently">
		&nbsp;<a href="<?=$a_home?>settings">Cancel</a>
	</p>
	<p class="light">Deleted accounts will lose all posts, comments, photos, games, application data, followers, and likes, and cannot be restored in any way. After deleting, your username and email address can be used to register again later, but all of your existing data will be lost.</p>
<?php break; ?>
<input type="hidden" name="action" value="save">
<?php
	if($_POST['action']=="save")
		echo '<div class="success" onclick="this.style.display=\'none\';">Settings Saved</div>';
	case 'privacy':
		if(isset($_POST['p_state']))
			mysql_query("UPDATE privacy SET state = '{$_POST['p_state']}', city = '".$_POST['p_city']."', age = '".$_POST['p_age']."', email = '".$_POST['p_email']."', phone = '".$_POST['p_phone']."', cell = '".$_POST['p_cell']."' WHERE uid = '".$u_id."' LIMIT 1");
		$tmp = mysql_query("SELECT * FROM privacy WHERE uid = '{$u_id}' LIMIT 1");
		$tmp_privacy = mysql_fetch_array($tmp);
?>
	<p>Configure how others see your content</p>
	<table id="privacy" border="0" align="center" cellspacing="0" cellpadding="0">
		<tbody>
		<tr class="lightbg">
			<td></td>
			<td title="Users you are following">Private</td>
			<td>Public</td>
		</tr>
		<tr>
			<td>State/Region</td>
			<td><label><input type="radio" name="p_state" value="n"<?php if($tmp_privacy[1]=="n")echo' checked="checked"';?>></label></td>
			<td><label><input type="radio" name="p_state" value="y"<?php if($tmp_privacy[1]=="y")echo' checked="checked"';?>></label></td>
		</tr>
		<tr class="lightbg">
			<td>City</td>
			<td><label><input type="radio" name="p_city" value="n"<?php if($tmp_privacy[2]=="n")echo' checked="checked"';?>></label></td>
			<td><label><input type="radio" name="p_city" value="y"<?php if($tmp_privacy[2]=="y")echo' checked="checked"';?>></label></td>
		</tr>
		<tr>
			<td>Age</td>
			<td><label><input type="radio" name="p_age" value="n"<?php if($tmp_privacy[3]=="n")echo' checked="checked"';?>></label></td>
			<td><label><input type="radio" name="p_age" value="y"<?php if($tmp_privacy[3]=="y")echo' checked="checked"';?>></label></td>
		</tr>
		<tr class="lightbg">
			<td>Email Address</td>
			<td><label><input type="radio" name="p_email" value="n"<?php if($tmp_privacy[5]=="n")echo' checked="checked"';?>></label></td>
			<td><label><input type="radio" name="p_email" value="y"<?php if($tmp_privacy[5]=="y")echo' checked="checked"';?>></label></td>
		</tr>
		<tr>
			<td>Home Phone</td>
			<td><label><input type="radio" name="p_phone" value="n"<?php if($tmp_privacy[6]=="n")echo' checked="checked"';?>></label></td>
			<td><label><input type="radio" name="p_phone" value="y"<?php if($tmp_privacy[6]=="y")echo' checked="checked"';?>></label></td>
		</tr>
		<tr class="lightbg">
			<td>Cell Phone</td>
			<td><label><input type="radio" name="p_cell" value="n"<?php if($tmp_privacy[7]=="n")echo' checked="checked"';?>></label></td>
			<td><label><input type="radio" name="p_cell" value="y"<?php if($tmp_privacy[7]=="y")echo' checked="checked"';?>></label></td>
		</tr>
		</tbody>
	</table>
	<input type="submit" value="Save Settings">
<?php
	break;
	case 'notifications':
		if($_POST['savenotify']) {
			$n_ema = $_POST['n_ema_0'] ? '1' : '0';
			$n_ema.= $_POST['n_ema_1'] ? '1' : '0';
			$n_ema.= $_POST['n_ema_2'] ? '1' : '0';
			$n_sms = $_POST['n_sms_0'] ? '1' : '0';
			$n_sms.= $_POST['n_sms_1'] ? '1' : '0';
			$n_sms.= $_POST['n_sms_2'] ? '1' : '0';
			mysql_query("UPDATE notify SET email = '{$n_ema}', sms = '{$n_sms}' WHERE uid = '{$u_id}' LIMIT 1");
		}
		$tmp = mysql_query("SELECT * FROM notify WHERE uid = '{$u_id}' LIMIT 1");
		if(!mysql_num_rows($tmp)) {
			mysql_query("INSERT INTO notify VALUES ('{$u_id}','110','100')");
			$tmp = mysql_query("SELECT * FROM notify WHERE uid = '{$u_id}' LIMIT 1");
		}
		$db_notify = mysql_fetch_assoc($tmp);
?>
	<p>Choose how you recieve notifications from Xusix</p>
	<input type="hidden" name="savenotify" value="true">
	<table id="notify" border="0" align="center" cellspacing="0" cellpadding="0">
		<tbody>
		<tr class="lightbg">
			<td></td>
			<td>Email</td>
			<td title="Text Message">SMS</td>
		</tr>
		<tr>
			<td title="When someone posts on your page">Posts</td>
			<td><label><input type="checkbox" name="n_ema_0" value="1"<?php if($db_notify['email'][0])echo' checked="checked"';?>></label></td>
			<td><label><input type="checkbox" name="n_sms_0" value="1"<?php if($db_notify['sms'][0])echo' checked="checked"';?>></label></td>
		</tr>
		<tr class="lightbg">
			<td title="When someone comments on one of your posts">Comments</td>
			<td><label><input type="checkbox" name="n_ema_1" value="1"<?php if($db_notify['email'][1])echo' checked="checked"';?>></label></td>
			<td><label><input type="checkbox" name="n_sms_1" value="1"<?php if($db_notify['sms'][1])echo' checked="checked"';?>></label></td>
		</tr>
		<tr>
			<td title="When someone likes one of your posts">Likes</td>
			<td><label><input type="checkbox" name="n_ema_2" value="1"<?php if($db_notify['email'][2])echo' checked="checked"';?>></label></td>
			<td><label><input type="checkbox" name="n_sms_2" value="1"<?php if($db_notify['sms'][2])echo' checked="checked"';?>></label></td>
		</tr>
		</tbody>
	</table>
	<input type="submit" value="Save Settings">
<?php	
	break;
	case 'experimental':
?>
	<div class="js">
		<!-- pushState Experimental -->
		<h3 id="pushstate">Experimental Loading</h3>
		<p>
			Enable experimental AJAX-based loading with pushState to load pages around 20X faster.<br>
			Please note that this feature is known to break some pages and features.
		</p>
		<p>
			<input type="button" id="pushStateOn" value="Enable Experimental Loading">
			<input type="button" id="pushStateOff" value="Disable Experimental Loading">
		</p>
		<br><hr><br>
		<!-- Hovercards -->
		<h3 id="pushstate">Hovercards</h3>
		<p>When enabled, pointing at a user&#39;s picture or name will show their hovercard, which includes details about the user and a larger version of their profile picture.</p>
		<p>
			<input type="button" id="hCardsOn" value="Enable Hovercards">
			<input type="button" id="hCardsOff" value="Disable Hovercards">
		</p>
		<br><hr><br>
	</div>
	<input type="hidden" name="saveinterface" value="true">
	<h3 id="interface">Interface</h3>
	<p>
		<label><input type="checkbox" name="meebo" value="1"<?php if($meebo==1)echo' checked="checked"';?>>Enable Meebo Bar</label>
		<br>
		<span class="light">The Meebo bar adds connections to other social networks such as Facebook and Twitter, as well as adding easy sharing and instant messaging tools</span>
	</p>
	<p>Advertisements:
		<select name="ads"><!-- 2 chars -->
			<option value="gt"<?php if($ads=='gt')echo' selected="selected"';?>>Google Text</option>
			<option value="gb"<?php if($ads=='gb')echo' selected="selected"';?>>Google Banners</option>
		</select>
	</p>
	<p><input type="submit" value="Save Settings"></p>
	<br><hr><br>
	<p>This computer has been activated. <a href="<?=$a_home?>logout/deactivate">Deactivate</a></p>
	<p>Activation on each computer lasts for 30 days. This activation period is renewed each time you access the site.</p>
<?php	
	break;
	case 'sources':
?>
	<p>Xusix Sources is still under initial development.</p>
	<noscript><p>Note that JavaScript will be required for most Sources features.</p></noscript>
<?php
	break;
	case 'profile':
	case '':
		if(isset($_POST['relastatus']))
			mysql_query("UPDATE mdetails SET state = '{$_POST['state']}', city = '".$_POST['city']."', relastatus = '".$_POST['relastatus']."', relaid = '".$_POST['relaid']."', phone = '".$_POST['phone']."', cell = '".$_POST['cell']."', cellpvdr = '".$_POST['cellpvdr']."', aboutme = '".htmlentities($_POST['aboutme'],ENT_QUOTES)."', movies = '".htmlentities($_POST['movies'],ENT_QUOTES)."', music = '".htmlentities($_POST['music'],ENT_QUOTES)."', tvshows = '".htmlentities($_POST['tvshows'],ENT_QUOTES)."' WHERE uid = '".$u_id."' LIMIT 1");
		$tmp = mysql_query("SELECT * FROM mdetails WHERE uid = '{$u_id}' LIMIT 1");
		$tmp_profile = mysql_fetch_array($tmp);
?>
		<h3 id="profile">Profile</h3>
		<p>Add details to your user page</p>
		<table id="profset" border="0" align="center" cellspacing="0" cellpadding="0">
			<tbody>
			<tr class="lightbg">
				<td>State/Province</td>
				<td><input type="text" name="state" maxlength="24" value="<?=$tmp_profile[1]?>"></td>
			</tr>
			<tr>
				<td>City</td>
				<td><input type="text" name="city" maxlength="24" value="<?=$tmp_profile[2]?>"></td>
			</tr>
			<tr class="lightbg">
				<td>Relationship Status</td>
				<td>
					<select name="relastatus">
						<option value="0"<?php if($tmp_profile[3]==0)echo' selected="selected"';?>>Single</option>
						<option value="1"<?php if($tmp_profile[3]==1)echo' selected="selected"';?>>In a Relationship</option>
						<option value="2"<?php if($tmp_profile[3]==2)echo' selected="selected"';?>>Engaged</option>
						<option value="3"<?php if($tmp_profile[3]==3)echo' selected="selected"';?>>Married</option>
						<option value="4"<?php if($tmp_profile[3]==4)echo' selected="selected"';?>>It&#39;s Complicated</option>
						<option value="5"<?php if($tmp_profile[3]==5)echo' selected="selected"';?>>Open Relationship</option>
						<option value="6"<?php if($tmp_profile[3]==6)echo' selected="selected"';?>>Widowed</option>
						<option value="7"<?php if($tmp_profile[3]==7)echo' selected="selected"';?>>Separated</option>
						<option value="8"<?php if($tmp_profile[3]==8)echo' selected="selected"';?>>Divorced</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Relationship Partner</td>
				<td><input type="text" name="relaid" maxlength="50" value="<?=$tmp_profile[4]?>" placeholder="Name or Xusix Username"></td>
			</tr>
			<tr class="lightbg">
				<td>Home Phone</td>
				<td><input type="tel" name="phone" maxlength="16" value="<?=$tmp_profile[5]?>"></td>
			</tr>
			<tr>
				<td>Cell Phone</td>
				<td><input type="tel" name="cell" maxlength="16" value="<?=$tmp_profile[6]?>"></td>
			</tr>
			<tr class="lightbg">
				<td>Cell Provider</td>
				<td>
					<select name="cellpvdr">
<?php
	include 'inc/sms/carriers.inc.php';
	
	foreach($carriers as $r) {
		echo '<optgroup label="'.$r[0].'">';
		foreach($r as $i=>$c)
			if($i>0) {
				echo '<option value="'.$c[0].'"';
				if($c[0]==$tmp_profile[7]) echo ' selected="selected"';
				echo '>'.$c[1].'</option>';
			}
		echo '</optgroup>';
	}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>About Me</td>
				<td><textarea name="aboutme"><?=$tmp_profile[8]?></textarea></td>
			</tr>
			<tr class="lightbg">
				<td>Favorite Movies</td>
				<td><textarea name="movies"><?=$tmp_profile[10]?></textarea></td>
			</tr>
			<tr>
				<td>Favorite Music</td>
				<td><textarea name="music"><?=$tmp_profile[9]?></textarea></td>
			</tr>
			<tr class="lightbg">
				<td>Favorite TV Shows</td>
				<td><textarea name="tvshows"><?=$tmp_profile[11]?></textarea></td>
			</tr>
			</tbody>
		</table>
		<br>
		<input type="submit" value="Save Settings">
<?php 
	break;
	default:
		echo '<p>Not sure how you got here, but there&#39;s no settings here.</p>';
}
?>
	</form>
	</div>
	<br class="clr">
</div>
<?php foot(); ?>