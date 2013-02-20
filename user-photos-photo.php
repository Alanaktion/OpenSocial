	<h1 class="frmsearch"><a href="<?=$a_home.$pg[0]?>" rel="user" data-uid="<?=$udb_basic[0]?>"><?=$udb_basic[3]." ".$udb_basic[4]?></a>&#39;s <a href="<?=$a_home.$pg[0]?>/photos">Photos</a></h1>
<?php
	$query = mysql_query("SELECT * FROM photos WHERE imgid = '{$pg[2]}' LIMIT 1");
	if(mysql_num_rows($query)) {
		$arr = mysql_fetch_assoc($query);
		echo '<div class="photo-container">';
		echo '<img src="'.$a_home.'uc/img/'.$pg[2].$arr['ext'].'" alt="'.$pg[2].'" class="photo">';
		echo '</div>';
		$q_prev = mysql_query("SELECT imgid FROM photos WHERE imgid < '{$pg[2]}' AND uid = '{$udb_basic[0]}' LIMIT 1");
		if(mysql_num_rows($q_prev)) {
			$a_prev = mysql_fetch_array($q_prev);
			echo '<div class="fltlft"><p class="btnrow"><a class="btn" href="'.$a_home.$pg[0].'/photos/'.$a_prev[0].'">&lsaquo; Previous</a></p></div>';	
		}
		$q_next = mysql_query("SELECT imgid FROM photos WHERE imgid > '{$pg[2]}' AND uid = '{$udb_basic[0]}' LIMIT 1");
		if(mysql_num_rows($q_next)) {
			$a_next = mysql_fetch_array($q_next);
			echo '<div class="fltrt"><p class="btnrow"><a class="btn" href="'.$a_home.$pg[0].'/photos/'.$a_next[0].'">&rsaquo; Next</a></p></div>';	
		}
		echo '<br class="clr">';
		
		// Start streampost
		echo '<div class="streampost single" data-postid="'.$pg[2].'">';
		echo '<p>Posted '.x::displaytime($arr['date']);
		if($pg[0]==$u_name || $pg[0]=='me') {
			echo ' &middot; <a href="'.$a_home.$pg[0].'/photos/'.$pg[2].'/delete/'.time().'" rel="delete">Delete</a>';
			echo ' &middot; <a href="'.$a_home.$pg[0].'/photos/'.$pg[2].'/setprofile/'.time().'">Set as Profile Picture</a>';
		}
		echo '</p>';
		
		// Build Like block
		$t_result = mysql_query("SELECT uid FROM plus WHERE postid = '".$pg[2]."'");
		if($u_id || $rows = mysql_num_rows($t_result)) {
			echo '<div class="comment">';
			echo '<span class="post-links">';
			if(mysql_num_rows(mysql_query("SELECT uid FROM plus WHERE postid = '{$pg[2]}' AND uid = '{$u_id}'")))
				echo '<a href="'.$a_home."post/".$post['postid'].'/unlike/'.time().'" rel="unlike">'.s('Unlike').'</a>';
			else
				echo '<a href="'.$a_home."post/".$post['postid'].'/like/'.time().'" rel="like">'.s('Like').'</a>';
			echo ' &middot; <a href="'.$a_home.'share/[p:'.$pg[2].']" rel="share" target="_blank">Share</a> &middot; ';
			echo '<a href="'.$a_home.'uc/img/'.$pg[2].$arr['ext'].'" target="_blank">Download</a>';
			echo '</span>';
		}
		if($rows) {
			echo ' &middot; ';
			if($rows==1) {
				$t_resuid = mysql_fetch_array($t_result);
				x::pu($t_resuid['uid']);
				echo '<a href="'.$a_home.x::$db_pu[$t_resuid['uid']]['user'].'" rel="user" data-uid="'.$t_resuid['uid'].'">'.x::$db_pu[$t_resuid['uid']]['fname']." ".x::$db_pu[$t_resuid['uid']]['lname'].'</a>'.s(' likes this.');
				unset($t_resuid);
			} elseif($full) {
				while($r = mysql_fetch_assoc($t_result)) {
					$lb[] = $r['uid'];
					x::pu($r['uid']);
				}
				foreach($lb as $i=>$u) {
					echo '<a href="'.$a_home.x::$db_pu[$u]['user'].'" rel="user" data-uid="'.$u.'">'.x::$db_pu[$u]['fname']." ".x::$db_pu[$u]['lname'].'</a>';
					if($i < (count($u) - 1))
						echo ', ';
					elseif($i == (count($u) - 1))
						echo s(' and ');
				}
				echo s(' like this.');
			} else
				echo '<a href="'.$a_home."post/".$post['postid'].'" rel="liked-by">'.$rows.s(' people like this.').'</a>';
		}
		echo '</div>';
		mysql_free_result($t_result);
		unset($t_result);
		
		// Show comments
		$q_com = mysql_query("SELECT cid FROM comments WHERE postid = '{$pg[2]}'");
		while($r = mysql_fetch_array($q_com))
			x::show_comment($r[0],true,($u_name==$pg[0]) ? true : false);
		echo '<div class="comment">';
		echo '<form action="'.$a_home.$pg[0].'/photos/'.$pg[2].'" method="post">';
		echo '<input type="text" name="comment-txt" placeholder="'.s('Write a comment&hellip;').'" required>';
		echo '</form></div>';
		echo '</div>';
	} else {
		echo '<p>The requested photo is not available.</p>';
		echo '<p><a href="'.$a_home.$pg[0].'/photos/">&lt; Return to Photos</a></p>';
	}
?>