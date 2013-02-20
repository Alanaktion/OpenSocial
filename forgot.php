<?php
require_once('inc/recaptchalib.php');
$publickey="6Le0o74SAAAAAMZWITscavedv_oRgxZy3IQhuxJq";
$privatekey="6Le0o74SAAAAAB6FFu18mW4WWi0xfAZDQz3e3IAL";

head();
?>
<title><?=s('Reset Your Password')?> - Xusix</title>
<style type="text/css">.content{min-height:400px}</style>
<?php top(); ?>
  <div class="sidebar1 no-nav" style="padding-top:10px;">
    <h3><?=s('Log In')?></h3>
    <form action="<?php echo $a_home; ?>login" method="post">
      <noscript><?=s('Username')?>:<br></noscript>
      <span id="lblu" style="display:none;"><?php s('username'); ?></span>
      <input type="text" name="user" placeholder="<?=s('Username')?>" autofocus>
      <br>
      <noscript><?=s('Password')?>:<br></noscript>
      <span id="lblp" style="display:none;"><?php s('password'); ?></span>
      <input type="password" name="pass" id="lpass" placeholder="<?=s('Password')?>">
      <br>
      <input type="submit" value="<?=s('Log In')?>">
    </form>
    <p>
    	<br>
    	<a href="<?=$a_home?>"><?=s('Sign Up')?></a>
    </p>
  </div>
  <div class="content">
<?php
if($pg[1]=="new") {
	mysql_query("UPDATE mbasic SET pass = '".md5($_POST['pass'])."' WHERE uid = '".$_POST['uid']."' LIMIT 1");
?>
		<h2><?=s('Reset Your Password')?></h2>
		<p><?=s('You password has been changed.')?></p>
		<p><a href="<?=$a_home?>"><?=s('Log In')?></a> &middot; <a href="javascript:alert('<?php echo $_POST['pass']; ?>');"><?=s('See Password')?></a></p>
	</div>
<?php
} elseif($pg[1] && $pg[2]) {
	if(hexdec($pg[2])>13e8 && time()-hexdec($pg[2])<86400) {
		$db_u = mysql_fetch_array(mysql_query('SELECT fname,user,pass FROM mbasic WHERE uid = \''.$pg[1].'\' LIMIT 1'));
?>
		<h2><?=s('Reset Your Password')?></h2>
		<p><?=s('Enter a new password for your account.')?></p>
		<form action="<?=$a_home?>pr/new" method="post">
			<input type="hidden" name="uid" value="<?php echo $pg[1]; ?>" />
			<input type="password" name="pass" autocomplete="off" />
			<input type="submit" value="<?=s('Continue')?>" />
		</form>
	</div>
<?php
	} else {
?>
		<h2><?=s('Reset Your Password')?></h2>
		<p><?=s('Sorry, the link you entered has expired.')?></p>
		<p><a href="<?=$a_home?>"><?=s('Log In')?></a> &middot; <a href="<?=$a_home?>pr"><?=s('Get a New Link')?></a></p>
	</div>
<?php
	}
} elseif(isset($_POST['email'])) {
	$t_result = mysql_query("SELECT uid,fname,lname FROM mbasic WHERE email = '".trim(strtolower($_POST['email']))."' LIMIT 1");
	if(mysql_num_rows($t_result)) {
		$resp=recaptcha_check_answer($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		if($resp->is_valid) {
			$keys = mysql_fetch_array($t_result);
			$url = $a_home.'pr/'.$keys['uid'].'/'.dechex(time());
			$message = '<p>'.s('You&#39;ve requested to reset your password for Xusix. Click the link below or enter the URL into your browser to set a new password.').'</p>
<p><a href="'.$url.'">'.$url.'</a></p>
<p>'.s('This link expires after 24 hours, if you did not request to reset your password, delete this email.').'</p>';
			x::htmlmail(trim(strtolower($_POST['email'])),s('Password Reset Instructions'),$message);
			mysql_free_result($t_result);
			unset($url,$headers,$keys,$t_result);
		} else {
			
		}
?>
		<h2><?=s('Reset Your Password')?></h2>
		<p><?=s('Please check your email for instructions on resetting your password.')?></p>
	</div>
<?php	
	} else {
		mysql_free_result($t_result);
?>
		<h2><?=s('Reset Your Password')?></h2>
		<p><?=s('The email ').'&quot;'.$_POST['email'].'&quot;'.s(' is not currently registered. Please try again.').'</p>';?>
		<form action="<?=$a_home?>pr" method="post">
			<label><?=s('Email Address')?> <input type="email" name="email" required></label><br>
<?php echo recaptcha_get_html($publickey); ?>
			<input type="submit" class="btnbig" value="<?=s('Continue')?>">
		</form>
	</div>
	<div class="sidebar2">
		<p><?=s('To reset your password, enter your email address and complete the CAPTCHA.  We will send you an email with a link to set a new password for your account.')?></p>
	</div>
<?php
	}
} else {
?>
    <h1><?=s('Reset Your Password')?></h1>
    <form action="<?=$a_home?>pr" method="post">
    	<label><?=s('Email Address')?> <input type="email" name="email" required></label><br>
<?php echo recaptcha_get_html($publickey); ?>
		<input type="submit" value="<?=s('Continue')?>">
	</form>
	</div>
	<div class="sidebar2">
		<p><?=s('To reset your password, enter your email address and complete the CAPTCHA.  We will send you an email with a link to set a new password for your account.')?></p>
	</div>
<?php } foot();  ?>