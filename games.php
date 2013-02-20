<?php head(); ?>
<title>Games - Xusix</title>
<?php top(); ?>
<div id="main">
<?php
if($pg[1]) {
	$qry = mysql_query("SELECT * FROM games WHERE `slug` = '{$pg[1]}' LIMIT 1");
	if(mysql_num_rows($qry) && is_file('swf/'.$pg[1].'.swf')) {
		$g = mysql_fetch_assoc($qry);
		$s = @getimagesize('swf/'.$pg[1].'.swf');
		
		echo '<h1 class="frmsearch">'.stripslashes($g['name']).'</h1>';
		echo '<p><a href="'.$a_home.'games">&lsaquo; Games List</a></p>';
		
		echo '<object id="swfobj" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" '.$s[3].' id="'.$pg[1].'" style="display:block;margin:0 auto">
		<param name="movie" value="'.$a_home.'/swf/'.$pg[1].'.swf">
		<param name="quality" value="high">
		<param name="bgcolor" value="#ffffff">
		<embed id="swfemb" src="'.$a_home.'/swf/'.$pg[1].'.swf" quality="high" bgcolor="#ffffff" '.$s[3].' name="'.$pg[1].'" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
		</embed>
		</object>';
		
	} else {
?>
	<h1 class="frmsearch">Games</h1>
	<p>The game you requested is not available.</p>
	<p><a href="<?=$a_home?>games">&lsaquo; Games List</a></p>
<?php
	}
} else {
?>
	<form action="<?=$a_home?>games/search" method="get" class="frmsearch">
		<input type="text" name="q" id="q" placeholder="Search Games" value="<?php echo $_GET['q']; ?>" required>
		<input type="submit" value="Search">
		<br class="clr">
	</form>
	<p>Xusix Games is being updated, some features may be unavailable.</p>
<?php
	$qry = mysql_query('SELECT * FROM games');
	if(mysql_num_rows($qry)) {
		echo '<div class="games-container">';
		while($r = mysql_fetch_assoc($qry)) {
			echo 
			'<div class="listing">'.
			'<div class="fltlft"><a href="'.$a_home.'games/'.$r['slug'].'">'.
			'<img src="'.$a_home.'swf/img/'.$r['slug'].'_32.jpg" alt="'.$r['slug'].'">'.
			'</a></div>'.
			'<div class="fltlft">'.
			'<a href="'.$a_home.'games/'.$r['slug'].'"><strong>'.stripslashes($r['name']).'</strong></a><br>'.
			'<a href="'.$r['authorurl'].'" rel="nofollow" target="_blank"><em>'.$r['author'].'</em></a><br>'.
			'<br class="clr">'.
			'</div></div>';
		}
		echo '</div>';
	} else {
		echo '<p>No games are currently available.  Please check back later.</p>';
	}
} ?>
	<br class="clr">
</div>
<?php foot(); ?>