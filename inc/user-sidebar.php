<div>
	<h3><?=s('Likes')?></h3>
	<?php
if($db_plus!="NONE") {
	foreach($db_plus as $plus) {
		echo '<p class="small">';
		if($plus['postid']) {
			$pui = x::pui($plus['postid']);
			if(!$db_pu[$pui]) $db_pu[$pui] = x::pu($pui);
			echo '<a href="'.$a_home.$db_pu[$pui]['user'].'" rel="user" data-uid="'.$pui.'">';
			echo $db_pu[$pui]['fname'];
			echo "</a>&#39;s <a href=\"".$a_home."post/".$plus['postid']."\">post</a>: ";
			unset($pid);
			echo $plus['title'];
		} else echo '<a href="'.$plus['url'].'" target="_blank">'.$plus['title'].'</a>';
		echo "</p>";
	}
} else {
	if($pg[0]==$u_name || $pg[0]=="me") echo '<p class="light">'.s('You have not liked anything yet.').'</p>';
	else echo '<p class="light">'.$udb_basic['fname'].s(' has not liked anything yet.').'</p>';
}
?>
</div>
<div style="margin-top:5px;">
	<h3><?=s('Comments')?></h3>
	<?php
if($db_comments!="NONE") {
	foreach($db_comments as $comment) {
		echo '<p class="small">&ldquo;';
		echo x::excerpt($comment['text']);
		echo "&rdquo; on ";
		$pui = x::pui($comment['postid']);
		if(!$db_pu[$pui]) $db_pu[$pui] = x::pu($pui);
		echo '<a href="'.$a_home.$db_pu[$pui]['user'].'" rel="user" data-uid="'.$pui.'">';
		echo $db_pu[$pui]['fname'];
		echo "</a>&#39;s <a href=\"".$a_home."post/".$comment['postid']."\">post</a>";
		echo "</p>";
	}
} else {
	if($pg[0]==$u_name || $pg[0]=="me") echo '<p class="light">'.s('You have not written any comments yet.').'</p>';
	else echo '<p class="light">'.$udb_basic[3].s(' has not written any comments yet.').'</p>';
}
?>
</div>