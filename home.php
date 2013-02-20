<?php head(); ?>
<title>Xusix</title>
<?php top(); ?>
<div id="main" class="stag-on">
<?php
x::alert('realtime','Xusix now has realtime comments!  When other users post comments on a post, you can see the new comments right as they&#39;re added automatically, allowing a continuous conversation on a post.');
?>
	<form action="<?=$a_home.$u_name?>" method="post" class="frmpost" id="frmpost" name="frmpost">
		<input type="hidden" name="action" value="post">
		<textarea name="txt" rows="2" required onkeypress="tefs(event);" placeholder="What&#39;s Up?"></textarea>
		<select name="vis" title="Visibility">
			<option value="0">Public</option>
			<option value="1">Private</option>
			<option value="2">Only Me</option>
		</select>
		<input type="submit" value="Post to Your Page">
		<br class="clr">
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

<?php x::posts('',$pg[1]); ?>
	<br class="clr">
</div>
<?php foot(); ?>