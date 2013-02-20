<?php head(); ?>
<title>Help - Xusix</title>
<?php top(); ?>
<div id="main">
<?php if($pg[1]=="welcome") { ?>
	<h1>Welcome to Xusix</h1>
	<p>Thank you for joining Xusix! Xusix allows you to communicate with your friends and colleagues across the world from any device, anywhere.</p>
	<p>You can search for friends, contacts, and colleagues on the <a href="<?=$a_home?>users">Users page</a> or <a href="<?=$a_home?>invite">invite them</a> to Xusix.</p>
	<p class="light">Note: Xusix is still under development. New features will be introduced continuously, and some features may be temporarily unavailable as we update them.</p>
	<p>
		<a href="<?=$a_home.$u_name?>" class="btn">Go to My Page</a>
		<a href="<?=$a_home?>settings/profile" class="btn">Fill out my Profile</a>
		<a href="https://www.facebook.com/dialog/oauth?client_id=372837914270&redirect_uri=http%3A%2F%2Ffb.xusix.com%2Ffb%2Fauth.php&scope=user_location,&state=%2Fsettings%2Fprofile%23fb" class="btn-fb" rel="nofollow">Import Facebook Profile</a>
	</p>
<?php } elseif($pg[1]=="contact") { ?>
		<h1 class="frmsearch">Contact Xusix</h1>
<?php
	if($_POST['action']=="contact"){
		$t_mbasic = mysql_fetch_assoc(mysql_query("SELECT email FROM mbasic WHERE uid = '".$u_id."'"));
		include "inc/mailer.php";
		emailsend($_POST['subject'],"contact@xusix.com",$_POST['body'],$t_mbasic['email']);
		echo "		<h2>Your message has been sent.</h2>";
	}
?>
	<p>
		Please contact us through our <a href="http://group.xusix.com/contacts.php">Xusix Group page</a> or <a href="http://www.alanaktion.com/contact">contact page on Alanaktion</a>.
		<!--<form action="<?=$a_home?>help/contact" method="post">
			<input type="hidden" name="action" value="contact" />
			<input type="text" name="subject" placeholder="Subject" required>
			<textarea name="body" placeholder="Text" required></textarea>
			<input type="submit" value="Send">
		</form>-->
	</p>
	<p>We will have an inline comment form up when our network is officially opened.</p>
<?php } elseif($pg[1]=="bbcode") { ?>
	<h1>Post Formatting</h1>
	<p>You can add custom formatting to any posts, comments, and photo captions using a simplified BB-code syntax.  All allowed formatting is demonstrated below:</p>
	<p>[b]<strong>this is bold</strong>[/b]</p>
	<p>[i]<em>this is italicized</em>[/i]</p>
	<p>[u]<span style="text-decoration: underline;">this is underlined</span>[/u]</p>
	<p>[color=red]<span style="color: red;">this is red</span>[/color]</p>
	<br>
	<p>Any addresses of outside web pages will automaticaly become links, as shown below:</p>
	<p><a href="http://www.alanaktion.com" target="_blank">http://www.alanaktion.com</a></p>
	<br>
	<p>You can attach photos to posts and comments by including its ID inside a tag, like this:</p>
	<p>[p:1397623404677279]</p>
	<p>To find a photo&#39;s ID, go to the photo on Xusix.  The ID is the number at the end of the URL, like "1397623404677279" from "www.xusix.com/alan/photos/1397623404677279".<p>
<?php } elseif($pg[1]=="future") { 
$t_memcount = mysql_num_rows(mysql_query("SELECT uid FROM mbasic"));
?>
	<h1>Upcoming Features</h1>
	<p>Xusix currently has <?=$t_memcount?> Members.<br>
	As the number of members grows, we will add new features to the site.</p>
	<p>10 Members:<br>
	Flash Games [Added]</p>
	<p>25 Members:<br>
	Mobile Site [Added]</p>
	<p>50 Members:<br>
	Facebook Connect [In Progress]</p>
	<p>75 Members:<br>
	Full Photo Galleries</p>
	<p>100 Members:<br>
	Something Awesome [<a href="/alan">Tell me what you&#39;d like to see</a>]</p>
	<p>150 Members:<br>
	Multiplayer Games</p>
	<p>200 Members:<br>
	Native mobile apps</p>
	<hr>
	<p>Since 2012, Xusix is ad-supported.  You will soon be able to select the type of advertisements you would like in your <a href="<?=$a_home?>settings">Settings</a>.</p>
<?php } else { ?>
	<h1>Help Center</h1>
	<p>Our network is still under development, so some features may change or be removed without notice. Your account will automatically be updated as we continue the site. Thank you for testing with us.</p>
	<p>
		<a href="<?=$a_home?>help/welcome">Getting Started</a><br>
		<a href="<?=$a_home?>help/bbcode">Post Formatting</a><br>
		<a href="<?=$a_home?>help/contact">Contact Us</a><br>
		<a href="<?=$a_home?>help/future">Upcoming Features</a>
	</p>
<?php } ?>
</div>
<?php foot(); ?>