	<h1 class="frmsearch"><a href="<?=$a_home.$pg[0]?>" rel="user" data-uid="<?=$udb_basic[0]?>"><?=$udb_basic[3]." ".$udb_basic[4]?></a>&#39;s Photos</h1>
<?php
	if($pg[2]=='success') echo '<p class="success">Your photos were published successfully.</p>';
	if($pg[2]=='delsuccess') echo '<p class="success">Your photo was deleted.</p>';
?>
	<p><form action="<?=$a_home.$pg[0]?>/photos/upload" method="post" enctype="multipart/form-data" id="upfrm">
		<input type="hidden" name="js" id="jsinput" value="0">
		Upload Photos: <input name="uploads[]" type="file" multiple="multiple">
		<span id="upmsg" style="display:none">Uploading&hellip;</span>
		<input type="submit" value="Upload">
	</form></p>
	<div class="buttonbar">
		<span class="buttonbar-label">Import Photos: </span>
<?php
	if($_COOKIE['xs_ftoken'])
		echo '<a href="'.$a_home.$u_name.'/photos/import/facebook" class="btn btn-left">Facebook</a>';
	else
		echo '<a href="https://www.facebook.com/dialog/oauth?client_id=372837914270&redirect_uri=http%3A%2F%2Fwww.xusix.com%2Foauth%2Ffacebook&scope=email,user_location, user_photos,user_birthday,offline_access,read_stream&state=me%2Fphotos" class="btn btn-left" rel="nofollow">'.s('Facebook').'</a>';
	if($_COOKIE['xs_instoken'])
		echo '<a href="'.$a_home.$u_name.'/photos/import/instagram" class="btn btn-right">Instagram</a>';
	else
		echo '<a href="https://api.instagram.com/oauth/authorize/?client_id=bdad7967f60a4d469a7256d12d7fcd0d&redirect_uri=http%3A%2F%2Fwww.xusix.com%2Foauth%2Finstagram%3Fstate%3Dme%2Fphotos&response_type=code" class="btn btn-right" rel="nofollow">'.s('Instagram').'</a>';
?>
	</div>
	<div class="gallery">
<?php
	if(!$db_photos) {
		if($pg[0]==$u_name || $pg[0]=="me") echo '<p class="light">You don&#39;t have any photos yet.</p>';
		else echo '<p class="light">'.$udb_basic[3].' doesn&#39;t have any photos yet.</p>';
	} else {
		foreach($db_photos as $pic) {
			echo '<a href="'.$a_home.$pg[0].'/photos/'.$pic['imgid'].'">';
			echo '<img src="'.$a_home.'uc/img/'.$pic['imgid'].'96'.$pic['ext'].'" alt="'.$pic['imgid'].'" title="'.$pic['caption'].'" width="96" height="96">';
			echo '</a>';
		}
	}
	echo '</div>';
?>