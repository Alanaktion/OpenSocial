<?php
// Xusix User Search and Browse
// Copyright (c) Alan Hardman 2011

head();

if($pg[1]=="search" && strlen($_GET['q'])) {
	$pg_search = 1;
	$t_search = str_replace(',','',cleanQuery(trim($_GET['q'])));
	$in = '(';
	foreach(explode(' ',$t_search) as $w)
		$in.="'".trim($w)."',";
	$in = rtrim($in,',').')';
	//$t_result = mysql_query("SELECT uid,fname,lname,user,MATCH(fname,lname,user,email) AGAINST('".$t_search."') AS score FROM mbasic WHERE MATCH(fname,lname,user,email) AGAINST('".$t_search."')"); // includes score column
	if($pg[2]=='debug') echo "SELECT uid,fname,lname,user FROM mbasic WHERE MATCH(fname,lname,user,email) AGAINST('".$t_search."') OR fname IN {$in} OR lname IN {$in}";
	$t_result = mysql_query("SELECT uid,fname,lname,user FROM mbasic WHERE MATCH(fname,lname,user,email) AGAINST('".$t_search."') OR fname IN {$in} OR lname IN {$in}");
	if(mysql_num_rows($t_result)) {
		while($row=mysql_fetch_assoc($t_result)) {
			$db_search[]=$row;
		}
	} else $db_search=0;
	mysql_free_result($t_result);
	unset($t_search,$t_result);
}
?>
<title>Friends - Xusix</title>
<?php top(); ?>
<div id="main">
	<form action="<?=$a_home.$pg[0]?>/search" method="get" class="frmsearch">
		<input type="text" name="q" required="required" autofocus="autofocus" id="q" value="<?php echo $_GET['q']; ?>">
		<input type="submit" value="Search">
		<label><input type="radio" name="l" value="friends" onclick="document.getElementById('q').focus()"> Following</label>
		<label><input type="radio" name="l" value="all" checked="checked" onclick="document.getElementById('q').focus()"> Everyone</label>
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
<?php
	if($pg_search) {
		if($db_search) {
			echo '<div class="fltlft">';
			foreach($db_search as $u) {
				echo '<p><a href="'.$a_home.$u['user'].'">';
				echo '<img src="'.x::userpic($u['uid'],28).'" alt="" width="28" height="28" class="userpic">';
				echo '<strong>'.$u['fname'].' '.$u['lname'];
				echo '</strong></a><br><em class="light">';
				echo $u['user'];
				echo '</em></p>'."\r\n";
			}
			echo '<div>';
		} else {
?>
	<div class="fltlft">
		<p>No Results</p>
		<p class="light">Try entering more information on the user you&#39;re trying to find.</p>
	</div>
	<br class="clr">
<?php
		}
	} else {
?>
	<div class="fltlft">
		<p><strong>Follwing</strong></p>
		<?php x::userlist(0,0); ?>
	</div>
	<div class="fltlft">
		<p><strong>Follwed By</strong></p>
		<?php x::userlist(1,0); ?>
	</div>
	<div class="fltlft">
		<p><strong>Most Followed</strong></p>
		<?php x::userlist(4,0); ?>
	</div>
	<div class="fltlft">
		<p><strong>New Users</strong></p>
		<?php x::userlist(3,0); ?>
	</div>
	<br class="clr">
<?php } ?>
</div>
<?php foot(); ?>