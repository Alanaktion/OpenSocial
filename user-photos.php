<?php
$t_result = mysql_query("SELECT * FROM photos WHERE uid = '".$udb_basic[0]."'");
if(mysql_num_rows($t_result)){while($row=mysql_fetch_assoc($t_result))$db_photos[]=$row;}else $db_photos=0;
unset($t_result);
?>
<title><?=$udb_basic[3]." ".$udb_basic[4]?>&#39;s Photos - Xusix</title>
<?php top(); ?>
<div id="main">
<?php
if($pg[2]=='upload') {
	$uploads = $_FILES['uploads'];
	$numfiles = count($uploads['name']);
	$img = array(); $ext = array();
	for($i=0;$i<$numfiles;$i++) {
		$size = getimagesize($uploads['tmp_name'][$i]);
		if($size['mime']=='image/gif' || $size['mime']=='image/jpeg' || $size['mime']=='image/png') {
			$tmp = x::img_save($uploads['tmp_name'][$i],$_POST['js'] ? false : true);
			$img[] = $tmp[0];
			$ext[] = $tmp[1];
			mysql_query("INSERT INTO photos VALUES ('{$u_id}','{$tmp[0]}','{$tmp[1]}','',NOW())");
			unset($tmp);
		}
	}
	if(!$img) {
		require 'user-photos-error.php';
	} elseif(!$_POST['js']) { // NO JAVASCRIPT
		$numimgs = count($img);
		if($numimgs>1) $m='s'; else $m='';
		echo '<h1 class="frmsearch">Photo Upload</h1>';
		echo '<p>'.count($img).' photo'.$m.' successfully uploaded.</p>';
		echo '<p>If you want to, you can caption your new photo'.$m.' below.</p>';
		echo '<form action="'.$a_home.$pg[0].'/photos/publish" method="post">';
		for($i=0;$i<$numimgs;$i++) {
			echo '<div class="pageitem">';
			echo '<img src="'.$a_home.'uc/img/'.$img[$i].$ext[$i].'" class="photo" alt="'.$i.'">';
			echo '<div class="comment">';
			echo '<input type="text" name="'.$img[$i].'" placeholder="Write a caption for this photo">';
			echo '</div></div>';
		}
		echo '<p><input type="submit" value="Publish"></p></form>';
	} else { /* JAVASCRIPT-BASED PROCESSING */
		require 'user-photos-process.php';
	}
} elseif($pg[2] && $pg[2]!='success' && $pg[2]!='delsuccess') {
	require 'user-photos-photo.php';
} else {
	require 'user-photos-list.php';
}
?>
<script type="text/javascript">
$('#upfrm input[type="submit"]').hide();
$('#upfrm input[type="file"]').change(function(){
	$('#upmsg').show();
	$('#upfrm').submit();
	$('#upfrm input[type="file"]').attr('disabled','disabled');
});
</script>
</div>