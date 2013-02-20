<?php
head();
if($_POST['action']=="post" && trim($_POST['txt'])!='') {
	$postid = uniqid();
	mysql_query("INSERT INTO posts VALUES ('{$u_id}','{$postid}','{$u_id}','".x::filter_in($_POST['txt'])."','post',NOW())");
	x::notify($postid,0);
?>
<title>Share &middot; Xusix</title>
</head>
<body>
	<h3 class="pad">Share on your page</h3>
	<p>Your post has been published.</p>
	<p><a href="javascript:window.close();" class="ajaxload">Close Window</a></p>
	<script type="text/javascript">
	window.setTimeout(function(){window.close()},500);
	</script>
<div class="footer light pad">
	Copyright &copy; <?=date('Y')?> <a href="http://group.xusix.com/" target="_blank">Xusix Group</a>
</div>
</body>
</html>
<?php } else { ?>
<title>Share - Xusix</title>
</head>
<body>
	<h3 class="pad">Share on your page</h3>
	<form action="<?=$a_home?>share" method="post" class="frmpost" id="frmpost" name="frmpost">
		<input type="hidden" name="action" value="post">
		<div class="textcontain">
			<textarea name="txt" rows="4" required autofocus placeholder="Share something&hellip;"><?="\r\n".$pg[1]?></textarea>
		</div>
		<input type="submit" value="Share">
		<input type="button" value="Cancel" onclick="window.close();">
		<div class="clearfloat"></div>
	</form>
<div class="footer light pad">
	Copyright &copy; <?=date('Y')?> <a href="http://group.xusix.com/" target="_blank">Xusix Group</a>
</div>
</body>
</html>
<?php } ?>