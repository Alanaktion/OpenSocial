<?php
if($_POST['new']) {
	header("Location: {$a_home}messages/new");
	mysql_close();
	exit();
}
head();
?>
<title>Messages &middot; Xusix</title>
<?php top(); ?>
<div id="main">
<?php
if($pg[1] && $pg[1]!='new') {
	x::msg_view($pg[1]);
} elseif($pg[1]=='new') {
?>
	<form action="<?=$a_home?>messages" method="post" class="frmpost" id="frmpost" name="frmpost">
		<input type="hidden" name="action" value="send">
		<p>To: <a href="javascript:userpicker(2,2,function(data){$('input[name=to]').val(data)});" target="_blank">Add from Contacts</a></p>
		<div class="textcontain"><input type="text" name="to" placeholder="Email address or Xusix username" required></div>
		<p>Subject:</p>
		<div class="textcontain"><input type="text" name="subject"></div>
		<p>Message:</p>
		<div class="textcontain"><textarea name="txt" rows="8" required></textarea></div>
		<input type="submit" value="Send Message">
		<br class="clear">
	</form>
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
<?php
} else {
	if($_POST['action']=='send') {
		if(x::msg_send($_POST['to'],$_POST['subject'],$_POST['txt']))
			echo '<p class="success">Message sent</p>';
		else
			echo '<p class="error">Message could not be sent</p>';
	} elseif($_POST['msg']) {
		$msglist = '';
		foreach($_POST['msg'] as $m)
			$msglist.= "'".$m."',";
		$msglist = rtrim($msglist,',');
		
		if($_POST['delete']) {
			mysql_query("DELETE FROM `messages` WHERE `id` IN ({$msglist})");
			echo '<p class="success">'.count($_POST['msg']).' message'. ((count($_POST['msg'])>1) ? 's' : '') .' deleted.</p>';
		} elseif($_POST['mark-read']) {
			mysql_query("UPDATE messages SET `read` = '1' WHERE `id` IN '{$msglist}'");
			echo '<p class="success">'.count($_POST['msg']).' message'. ((count($_POST['msg'])>1) ? 's' : '') .' marked as read.</p>';
		} elseif($_POST['mark-unread']) {
			mysql_query("UPDATE messages SET `read` = '0' WHERE `id` IN '{$msglist}'");
			echo '<p class="success">'.count($_POST['msg']).' message'. ((count($_POST['msg'])>1) ? 's' : '') .' marked as unread.</p>';
		}
	}
?>
	<form action="<?=$a_home?>messages" method="post">
	<div class="buttonbar">
		<input type="submit" name="new" value="New Message">
		<span class="js buttonbar-label">Select: </span>
		<span class="js">
			<input type="button" class="btn-left" id="msg-select-all" value="All"><input type="button" class="btn-center" id="msg-select-none" value="None"><input type="button" class="btn-center" id="msg-select-read" value="Read"><input type="button" class="btn-right" id="msg-select-unread" value="Unread">
		</span>
		<script type="text/javascript"><!--
		$(function(){
		$('#msg-select-all').click(function(){
			$('.message input[type=checkbox]').attr('checked','checked');
		});
		$('#msg-select-none').click(function(){
			$('.message input[type=checkbox]').removeAttr('checked');
		});
		$('#msg-select-read').click(function(){
			$('.message input[type=checkbox]').removeAttr('checked');
			$('.message:not(.unread) input[type=checkbox]').attr('checked','checked');
		});
		$('#msg-select-unread').click(function(){
			$('.message input[type=checkbox]').removeAttr('checked');
			$('.message.unread input[type=checkbox]').attr('checked','checked');
		});
		});
		// --></script>
		<input type="submit" name="delete" value="Delete">
		<span class="buttonbar-label">Mark as: </span>
		<input type="submit" class="btn-left" name="mark-read" value="Read"><input type="submit" class="btn-right" name="mark-unread" value="Unread">
	</div>
<div class="ga">
<script type="text/javascript"><!--
google_ad_client = "ca-pub-8924338699009980";
/* Xusix Link Bar */
google_ad_slot = "2975601174";
google_ad_width = 728;
google_ad_height = 15;
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>
<?php x::msg_inbox(); ?>
    	<script type="text/javascript"><!--
	$(function(){
		if($('.message').length < 1) {
			$('.buttonbar input:not([name=new])').attr('disabled','disabled');
		}
	});
	// --></script>
	</form>
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
<?php } ?>
</div>
<?php foot(); ?>