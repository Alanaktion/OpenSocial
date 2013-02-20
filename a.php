<?php
if(!$u_id)
	exit();

if($pg[3]=='delete' && $_POST['del-cancel']) {header('Location: /a/user/');exit;}

if($pg[1]=="phpinfo") {
	phpinfo(INFO_ALL);
	exit('<!-- XUSIX ADMIN PHP INFO -->');
}

head();

if($_POST['action']=='updateuser') {
	mysql_query("UPDATE mbasic SET email = '".$_POST['email']."', pass = '".$_POST['pass']."', fname = '".$_POST['fname']."', lname = '".$_POST['lname']."', gender = '".$_POST['gender']."', user = '".$_POST['user']."', country = '".$_POST['country']."', timezone = '".$_POST['timezone']."' WHERE uid = '".$_POST['uid']."' LIMIT 1");
	mysql_query("UPDATE mdetails SET state = '".$_POST['state']."', city = '".$_POST['city']."', relastatus = '".$_POST['relastatus']."', relaid = '".$_POST['relaid']."', phone = '".$_POST['phone']."', cell = '".$_POST['cell']."', cellpvdr = '".$_POST['cellpvdr']."', aboutme = '".htmlentities($_POST['aboutme'],ENT_QUOTES)."', movies = '".htmlentities($_POST['movies'],ENT_QUOTES)."', music = '".htmlentities($_POST['music'],ENT_QUOTES)."', tvshows = '".htmlentities($_POST['tvshows'],ENT_QUOTES)."' WHERE uid = '".$_POST['uid']."' LIMIT 1");
	mysql_query("UPDATE privacy SET state = '".$_POST['prstate']."', city = '".$_POST['prcity']."', age = '".$_POST['prage']."', lname = '".$_POST['prlname']."', email = '".$_POST['premail']."', phone = '".$_POST['prphone']."', cell = '".$_POST['prcell']."', WHERE uid = '".$_POST['uid']."' LIMIT 1");
	mysql_query("UPDATE settings SET meebo = '".$_POST['meebo']."', theme = '".$_POST['theme']."', ads = '".$_POST['ads']."', WHERE uid = '".$_POST['uid']."' LIMIT 1");
}

?>
<title>Administration - Xusix</title>
<?php top(); ?>
<div id="main">
<?php if($pg[1]=="user") {
if(!$pg[2]) {?>
  	<h1 class="frmsearch">User List</h1>
<?php
	$t_mbasic = mysql_query("SELECT uid,email,fname,lname,user FROM mbasic");
	echo '<table align="center">';
	while($row=mysql_fetch_assoc($t_mbasic)) {
		echo '<tr>';
		echo '<td>'.$row['fname'].' '.$row['lname'].'</td>';
		echo '<td>'.$row['email'].'</td>';
		echo '<td>'.$row['user'].'</td>';
		echo '<td><a href="'.$a_home.'a/user/'.$row['uid'].'">View/Edit</a></td>';
		echo '<td><a href="'.$a_home.'a/user/'.$row['uid'].'/delete">Delete</a></td>';
		echo '</tr>';
	}
	echo '</table>';
	mysql_free_result($t_mbasic);
	unset($t_mbasic);
} else {
	if($pg[3]=='delete') {
		if($_POST['confirm']) {
			mysql_query("DELETE FROM mbasic WHERE uid = '{$pg['2']}' LIMIT 1");
			mysql_query("DELETE FROM mdetails WHERE uid = '{$pg['2']}' LIMIT 1");
			mysql_query("DELETE FROM privacy WHERE uid = '{$pg['2']}' LIMIT 1");
			mysql_query("DELETE FROM settings WHERE uid = '{$pg['2']}' LIMIT 1");
			mysql_query("DELETE FROM comments WHERE uid = '{$pg['2']}'");
			mysql_query("DELETE FROM posts WHERE uid = '{$pg['2']}'");
			mysql_query("DELETE FROM plus WHERE uid = '{$pg['2']}'");
			mysql_query("DELETE FROM friends WHERE uid = '{$pg['2']}'");
			mysql_query("DELETE FROM friends WHERE friend = '{$pg['2']}'");
			echo '<h2>User, Followers, Comments and Posts Permanently Deleted</h2>';
			echo '<p><a href="/a/user">Back</a></p>';
		} else {?>
<form action="/a/user/<?=$pg[2].'/'.$pg[3]?>/delete" method="post">
	<h1>Delete User</h1>
	<p>This will delete the user&#39;s complete account, including all posts and comments.</p>
	<input type="submit" value="Delete Permanently" name="confirm">
	<input type="submit" value="Cancel" name="del-cancel">
</form>
<?php } } else {
		$t_mbasic = mysql_query("SELECT * FROM mbasic WHERE uid = '".$pg[2]."' LIMIT 1");
		if(!mysql_num_rows($t_mbasic)) {
			echo "<p>User does not exist with ID ".$pg[2]."</p>";
		} else {
			$t_mbasic  = mysql_fetch_assoc($t_mbasic);
			$t_mdetails= mysql_fetch_assoc(mysql_query("SELECT * FROM mdetails WHERE uid = '".$pg[2]."'"));
			$t_privacy = mysql_fetch_assoc(mysql_query("SELECT * FROM privacy WHERE uid = '".$pg[2]."'"));
			$t_settings= mysql_fetch_assoc(mysql_query("SELECT * FROM settings WHERE uid = '".$pg[2]."'"));
?>
  	<h1 class="frmsearch">Edit/View User</h1>
    <form action="<?=$a_home?>a/user" method="post">
      <input type="hidden" name="action" value="updateuser">
      <input type="hidden" name="uid" value="<?=$pg[2]?>">
      <table align="center">
        <tr>
          <td colspan="2"><h3>mbasic</h3></td>
        </tr>
        <tr>
          <td>uid</td>
          <td><?=$t_mbasic['uid']?></td>
        </tr>
        <tr class="lightbg">
          <td>email</td>
          <td><input type="text" name="email" maxlength="50" value="<?=$t_mbasic['email']?>"></td>
        </tr>
        <tr>
          <td>pass</td>
          <td><input type="text" name="pass" value="<?=$t_mbasic['pass']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>fname</td>
          <td><input type="text" name="fname" maxlength="30" value="<?=$t_mbasic['fname']?>"></td>
        </tr>
        <tr>
          <td>lname</td>
          <td><input type="text" name="lname" maxlength="30" value="<?=$t_mbasic['lname']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>gender</td>
          <td><input type="text" name="gender" maxlength="1" value="<?=$t_mbasic['gender']?>"></td>
        </tr>
        <tr>
          <td>user</td>
          <td><input type="text" name="user" maxlength="50" value="<?=$t_mbasic['user']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>bdate</td>
          <td><?=$t_mbasic['bdate']?></td>
        </tr>
        <tr>
          <td>joindate</td>
          <td><?=$t_mbasic['joindate']?></td>
        </tr>
        <tr class="lightbg">
          <td>joinip</td>
          <td><?=$t_mbasic['joinip']?></td>
        </tr>
        <tr>
          <td>country</td>
          <td><input type="text" name="country" maxlength="30" value="<?=$t_mbasic['country']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>timezone</td>
          <td><input type="text" name="timezone" value="<?=$t_mbasic['timezone']?>"></td>
        </tr>
        <tr>
          <td colspan="2"><h3>mdetails</h3></td>
        </tr>
        <tr>
          <td>uid</td>
          <td><?=$t_mdetails['uid']?></td>
        </tr>
        <tr class="lightbg">
          <td>state</td>
          <td><input type="text" name="state" maxlength="24" value="<?=$t_mdetails['state']?>"></td>
        </tr>
        <tr>
          <td>city</td>
          <td><input type="text" name="city" maxlength="24" value="<?=$t_mdetails['city']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>relastatus</td>
          <td>
            <select name="relastatus">
              <option value="0"<?php if($t_mdetails['relastatus']==0)echo' selected="selected"';?>>Single</option>
              <option value="1"<?php if($t_mdetails['relastatus']==1)echo' selected="selected"';?>>In a Relationship</option>
              <option value="2"<?php if($t_mdetails['relastatus']==2)echo' selected="selected"';?>>Engaged</option>
              <option value="3"<?php if($t_mdetails['relastatus']==3)echo' selected="selected"';?>>Married</option>
              <option value="4"<?php if($t_mdetails['relastatus']==4)echo' selected="selected"';?>>It&#39;s Complicated</option>
              <option value="5"<?php if($t_mdetails['relastatus']==5)echo' selected="selected"';?>>Open Relationship</option>
              <option value="6"<?php if($t_mdetails['relastatus']==6)echo' selected="selected"';?>>Widowed</option>
              <option value="7"<?php if($t_mdetails['relastatus']==7)echo' selected="selected"';?>>Separated</option>
              <option value="8"<?php if($t_mdetails['relastatus']==8)echo' selected="selected"';?>>Divorced</option>
            </select>
          </td>
        </tr>
        <tr>
          <td>relaid</td>
          <td><input type="text" name="relaid" maxlength="50" value="<?=$t_mdetails['relaid']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>phone</td>
          <td><input type="tel" name="phone" maxlength="11" value="<?=$t_mdetails['phone']?>"></td>
        </tr>
        <tr>
          <td>cell</td>
          <td><input type="tel" name="cell" maxlength="11" value="<?=$t_mdetails['cell']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>cellpvdr</td>
          <td>
            <select name="cellpvdr">
              <option value=""<?php if($t_mdetails['cellpvdr']=='')echo' selected="selected"';?>>None</option>
              <option value="at"<?php if($t_mdetails['cellpvdr']=='at')echo' selected="selected"';?>>AT&amp;T</option>
              <option value="nx"<?php if($t_mdetails['cellpvdr']=='nx')echo' selected="selected"';?>>Nextel</option>
              <option value="sp"<?php if($t_mdetails['cellpvdr']=='sp')echo' selected="selected"';?>>Sprint</option>
              <option value="tm"<?php if($t_mdetails['cellpvdr']=='tm')echo' selected="selected"';?>>T-Mobile</option>
              <option value="vz"<?php if($t_mdetails['cellpvdr']=='vz')echo' selected="selected"';?>>Verizon</option>
              <option value="vm"<?php if($t_mdetails['cellpvdr']=='vm')echo' selected="selected"';?>>Virgin Mobile</option>
              <option value="gv"<?php if($t_mdetails['cellpvdr']=='gv')echo' selected="selected"';?>>Google Voice</option>
            </select>
          </td>
        </tr>
        <tr>
          <td>aboutme</td>
          <td><textarea name="aboutme"><?=$t_mdetails['aboutme']?></textarea></td>
        </tr>
        <tr class="lightbg">
          <td>movies</td>
          <td><textarea name="movies"><?=$t_mdetails['movies']?></textarea></td>
        </tr>
        <tr>
          <td>music</td>
          <td><textarea name="music"><?=$t_mdetails['music']?></textarea></td>
        </tr>
        <tr class="lightbg">
          <td>tvshows</td>
          <td><textarea name="tvshows"><?=$t_mdetails['tvshows']?></textarea></td>
        </tr>
        <tr>
          <td colspan="2"><h3>privacy</h3></td>
        </tr>
        <tr>
          <td>uid</td>
          <td><?=$t_privacy['uid']?></td>
        </tr>
        <tr class="lightbg">
          <td>state</td>
          <td><input type="text" name="prstate" maxlength="1" value="<?=$t_privacy['state']?>"></td>
        </tr>
        <tr>
          <td>city</td>
          <td><input type="text" name="prcity" maxlength="1" value="<?=$t_privacy['city']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>age</td>
          <td><input type="text" name="prage" maxlength="1" value="<?=$t_privacy['age']?>"></td>
        </tr>
        <tr>
          <td>lname</td>
          <td><input type="text" name="prlname" maxlength="1" value="<?=$t_privacy['lname']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>email</td>
          <td><input type="text" name="premail" maxlength="1" value="<?=$t_privacy['email']?>"></td>
        </tr>
        <tr>
          <td>phone</td>
          <td><input type="text" name="prphone" maxlength="1" value="<?=$t_privacy['phone']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>cell</td>
          <td><input type="text" name="prcell" maxlength="1" value="<?=$t_privacy['cell']?>"></td>
        </tr>
        <tr>
          <td colspan="2"><h3>settings</h3></td>
        </tr>
        <tr>
          <td>uid</td>
          <td><?=$t_settings['uid']?></td>
        </tr>
        <tr class="lightbg">
          <td>meebo</td>
          <td><input type="text" name="meebo" maxlength="1" value="<?=$t_settings['meebo']?>"></td>
        </tr>
        <tr>
          <td>theme</td>
          <td><input type="text" name="theme" maxlength="6" value="<?=$t_settings['theme']?>"></td>
        </tr>
        <tr class="lightbg">
          <td>ads</td>
          <td><input type="text" name="ads" maxlength="2" value="<?=$t_settings['ads']?>"></td>
        </tr>
        <tr>
          <td></td>
          <td><input type="submit" value="Update User"></td>
        </tr>
      </table>
    </form>
<?php } } } ?>
<?php } elseif($pg[1]=="mod") { ?>
<?php } elseif($pg[1]=="addgame") { ?>
	<h1 class="frmsearch">Add Game to Database</h1>
<?php
	if($_POST['name']) {
		// Save SWF 
		@move_uploaded_file($_FILES['swf']['tmp_name'],'swf/'.$_POST['slug'].'.swf');
		
		// Save uploaded image (temporarily)
		move_uploaded_file($_FILES['img']['tmp_name'],'swf/img/'.$_POST['slug'].'.tmp');
		
		// Generate image thumbnails
		foreach(array(28,32,64,96,128) as $dim) {
			$source = imagecreatefromstring(file_get_contents('swf/img/'.$_POST['slug'].'.tmp'));
			$resized = imagecreatetruecolor($dim,$dim);
			if(imagesx($source)>imagesy($source))
				imagecopyresampled($resized,$source,0,0,(imagesx($source)-imagesy($source)) / 2,0,$dim,$dim,imagesy($source),imagesy($source));
			else
				imagecopyresampled($resized,$source,0,0,0,(imagesy($source)-imagesx($source)) / 2,$dim,$dim,imagesx($source),imagesx($source));
			imagejpeg($resized,'swf/img/'.$_POST['slug'].'_'.$dim.'.jpg',90);
		}
		
		// Delete uploaded image (original)
		unlink('swf/img/'.$_POST['slug'].'.tmp');
		
		// Delete existing record with given slug (For Updates)
		mysql_query("DELETE FROM games WHERE `slug` = '{$_POST['slug']}' LIMIT 1");
		
		// Write game information to database
		mysql_query("INSERT INTO games VALUES ('".addslashes($_POST['name'])."','{$_POST['slug']}','".addslashes($_POST['author'])."','{$_POST['authorurl']}')");
		
		echo '<p>'.stripslashes($_POST['name']).' has been added to the games section.</p>';
	}
?>
	<form action="/a/addgame" method="post" enctype="multipart/form-data">
		SWF File: <input type="file" name="swf"><br>
		<span class="light">Optionally, you can upload the SWF file manually to /swf/ if it exceeds the 2 MB upload file size limit.</span><br>
		Image File: <input type="file" name="img" required><br>
		<input type="text" name="name" id="name" placeholder="Name" required><br>
		<input type="text" name="slug" id="slug" placeholder="Slug" required><br>
		<input type="text" name="author" placeholder="Author" autocomplete="on" list="data-author"><br>
		<input type="text" name="authorurl" placeholder="Author URL" autocomplete="on" list="data-authorurl"><br>
		<input type="submit" value="Add Game"><br>
		<datalist id="data-author">
<?php
	$qry = mysql_query("SELECT DISTINCT author FROM games");
	if(mysql_num_rows($qry))
		while($r = mysql_fetch_array($qry))
			echo '<option value="'.$r[0].'">';
?>
		</datalist>
		<datalist id="data-authorurl">
<?php
	$qry = mysql_query("SELECT DISTINCT authorurl FROM games");
	if(mysql_num_rows($qry))
		while($r = mysql_fetch_array($qry))
			echo '<option value="'.$r[0].'">';
?>
		</datalist>
	</form>
	<script type="text/javascript">
function str2slug(str) {
  str = str.replace(/^\s+|\s+$/g,''); // trim
  str = str.toLowerCase();
  
  // remove accents, swap � for n, etc
  var from = "�����������������������/_,:;";
  var to   = "aaaaeeeeiiiioooouuuunc------";
  for(var i=0, l=from.length; i<l; i++) {
    str = str.replace(new RegExp(from.charAt(i),'g'), to.charAt(i));
  }

  str = str.replace(/[^a-z0-9 -]/g,'') // remove invalid chars
    .replace(/\s+/g,'-') // collapse whitespace and replace by -
    .replace(/-+/g,'-'); // collapse dashes

  return str;
}
		$(function(){
			$('#name').keyup(function(){
				$('#slug').val(str2slug($(this).val()));
			})
		});
	</script>
	<br>
<?php } elseif($pg[1]=='eval') { ?>
	<h1 class="frmsearch">Testing Server Evaluate</h1>
	<form action="<?=$a_home?>a/eval" method="post">
		<textarea name="c" style="width:100%;height:6em"><?php echo stripslashes($_POST['c']); ?></textarea>
		<br>
		<input type="submit" value="Evaluate">
	</form>
<?php if(isset($_POST['c'])) echo '<pre>'.eval(stripslashes($_POST['c'])).'</pre>'; ?>
<?php } elseif($pg[1]=="phinfo") { phpinfo(); ?>
<?php } elseif($pg[1]=="ex") { ?>
<iframe src="<?=$a_home?>ex/" id="appframe" class="appframe"></iframe>
<?php } elseif($pg[1]=="email") { ?>
  	<h1 class="frmsearch">Email Single User</h1>
<?php
	if(!$pg[2]) { // no email selected
		$t_mbasic = mysql_query("SELECT uid,email,fname,lname,user FROM mbasic");
		echo '<table align="center">';
		$allmails = '';
		while($row=mysql_fetch_assoc($t_mbasic)) {
			echo '<tr>';
			echo '<td>'.$row['fname'].' '.$row['lname'].'</td>';
			echo '<td>'.$row['email'].'</td>';
			echo '<td>'.$row['user'].'</td>';
			echo '<td><a href="'.$a_home.'a/email/'.urlencode($row['email']).'">Email</a></td>';
			echo '</tr>';
			$allmails.= $row['email'].',';
		}
		echo '</table>';
		mysql_free_result($t_mbasic);
		unset($t_mbasic);
		$allmails = rtrim($allmails,',');
?>
	<h1 class="frmsearch">Email All Users</h1>
	<form action="<?=$a_home?>a/email/send" method="post">
		<p><input type="text" name="to" placeholder"To" value="<?=$allmails?>" style="width:98%"></p>
		<p><input type="text" name="subject" placeholder="Subject" style="width:98%"></p>
		<p><textarea name="msg" placeholder="Message" style="width:98%"></textarea></p>
		<p><input type="submit" value="Send Message to All Users"></p>
	</form>
<?php } elseif($pg[2]=='send') { /* email submitted */

	x::htmlmail($_POST['to'],htmlentities(stripslashes($_POST['subject'])),stripslashes($_POST['msg']));
	echo '
	<p>Message successfully sent:</p>
	<p>To: '.$_POST['to'].'</p>
	<p>Subject: '.htmlentities(stripslashes($_POST['subject'])).'</p>
	<p>Message: <pre>'.htmlentities(stripslashes($_POST['msg'])).'</pre></p>';

	} else { /* single email selected */ ?>

<?php }
} else { ?>
  	<h1>Administration</h1>
    <p>Select a tool below.</p>
<?php } if($pg[1]!='ex') {?>
	<h4>Toolbox</h4>
	<p>
		<a href="<?=$a_home?>a/user">Edit Users</a><br>
		<a href="<?=$a_home?>a/mod">Moderate</a><br>
		<a href="<?=$a_home?>a/email">Email Users</a><br>
		<a href="<?=$a_home?>a/addgame">Add Game</a><br>
		<a href="<?=$a_home?>a/eval">Evaluate</a><br>
		<a href="<?=$a_home?>a/phpinfo">PHP Info</a><br>
		<a href="<?=$a_home?>a/ex">Extplorer</a>
	</p>
<?php } if(!$pg[1]) { ?>
	<h4>JavaScript Debugging:</h4>
	<p>
		<a href="javascript:$.post('/ajax.php',{req:'getflag',flag:prompt('Flag Name')},function(data){alert(data);console.log(data)});">Get Flag Value</a><br>
		<a href="javascript:userpicker(prompt('Users'),prompt('Type of Picker'),function(data){alert(data);console.log(data)});">Show User Picker</a><br>
		<a href="javascript:$.post('/ajax.php',{req:'cmsg'},function(data){alert('Email checker was run.\nFor security reasons, no data is returned by this function.')});">Run Email Checker</a><br>
		<a href="javascript:$(window).resize();">Force Layout Update</a><br>
		<a onclick="$(this).toggleClass('ajax-loader')">Test .ajax-loader</a><br>
		<span class="js">This should only show with JavaScript enabled.</span><br>
	</p>
<?php } echo '</div>'; foot(); ?>