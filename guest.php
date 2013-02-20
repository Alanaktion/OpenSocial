<?php
if($pg[0]=="join" && isset($_POST['user'])) $fail = x::signup();
if($pg[0]=="login" && isset($_POST['user'])) x::login($_POST['user'],$_POST['pass']);

// User authorized Facebook connection
if($_COOKIE['xs_ftoken']) {
	// Fetch user data via Facebook Graph, using the token from the cookie
	$user = json_decode(file_get_contents("https://graph.facebook.com/me?access_token=".$_COOKIE['xs_ftoken']));
	
	// Prepare form variables
	$p_user  = $user->username;
	$p_email = $user->email;
	$p_g     = $user->gender[0];
	
	//$p_timezone = timezone_name_from_abbr('',$user->timezone,0);
	
	$p_bday = explode('/',$user->birthday);
	
	// Parse name into first/last
	$name = explode(' ',$user->name);
	$p_lname = '';
	foreach($name as $i=>$n) if($i==0) $p_fname = $n; else $p_lname .= $n;
	$p_lname = trim($p_lname);
}
head();
?>
<title><?=s('Xusix &middot; Social connections online at a whole new level')?></title>
<style type="text/css">.content{min-height:400px}</style>
<meta name="description" content="Social connections online at a whole new level! Connect to everything, everywhere! Meet new friends and connect with ones you already know. Chat with friends through SMS, without the need for a phone!" />
<?php top(); ?>
<div id="main">
	<div class="fltlft guest-col0">
		<h3><?php s('Log In'); ?></h3>
		<?php if($pg[0]=="login" && isset($_POST['user'])) echo '<p>'.s('Incorrect Username/Password').'</p>'; ?>
		<form action="<?=$a_home?>login" method="post">
			<input type="hidden" name="goto" value="<?php echo $_GET['p']; ?>">
			<?=s('Username')?><br>
			<input type="text" name="user" placeholder="<?=s('Username')?>" required>
			<?=s('Password')?><br>
			<input type="password" name="pass" id="lpass" placeholder="<?=s('Password')?>" required>
			<div class="buttonbar">
				<input type="submit" value="<?=s('Log In')?>">
				<a class="buttonbar-label" href="<?=$a_home?>pr"><?=s('Forgot?')?></a>
			</div>
		</form>
		<div class="ga" style="padding:10px;">
		<script type="text/javascript"><!--
		google_ad_client = "ca-pub-8924338699009980";
		/* Xusix Nav Ads */
		google_ad_slot = "8067917263";
		google_ad_width = 160;
		google_ad_height = 600;
		//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
		</div>
	</div>
	<div class="fltlft guest-col1">
	    	<h3><?php s('Sign Up'); ?></h3>
<?php if($user) { ?>
		<p><?=s('Please verify your information from Facebook and complete the Captcha to sign up for Xusix.')?></p>
<?php } else { ?>
		<p><a href="https://www.facebook.com/dialog/oauth?client_id=372837914270&redirect_uri=http%3A%2F%2Fww.xusix.com%2Foauth%2Ffacebook&scope=email,user_location, user_photos,user_birthday,offline_access&state=%23fb" class="btn-fb" rel="nofollow"><?=s('Sign Up with Facebook')?></a></p>
<?php } ?>
<?php if($fail) echo "<p>{$fail}</p>"; ?>
		<form action="<?php echo $a_home; ?>join" method="post">
			<input type="hidden" name="goto" value="<?php echo $_GET['p']; ?>">
<?php if($user) { ?>
			<input type="hidden" name="fbid" value="<?=$user->id?>">
			<input type="hidden" name="fbtoken" value="<?=$_COOKIE['fbtoken']?>">
<?php } ?>
			<table>
				<tr>
					<td><label for="user"><?=s('Username')?></label></td>
					<td><input type="text" name="user" id="user" autocomplete="off" value="<?=$p_user?>" maxlength="50" /></td>
				</tr>
				<tr>
					<td><label for="pass"><?=s('Password')?></label></td>
					<td><input type="password" name="pass" id="pass" autocomplete="off"></td>
				</tr>
				<tr>
					<td><label for="email"><?=s('Email Address')?></label></td>
					<td><input type="email" name="email" id="email" autocomplete="off" value="<?=$p_email?>"></td>
				</tr>
				<tr>
					<td><label for="fname"><?=s('First Name')?></label></td>
					<td><input type="text" name="fname" id="fname" autocomplete="off" value="<?=$p_fname?>"></td>
				</tr>
				<tr>
					<td><label for="lname"><?=s('Last Name')?></label></td>
					<td><input type="text" name="lname" id="lname" autocomplete="off" value="<?=$p_lname?>"></td>
				</tr>
				<tr>
					<td><?=s('Gender')?></td>
					<td>
						<label style="display:inline-block"><input type="radio" name="gender" id="genm" value="m"<?php if($p_g=='m') echo ' checked="checked"'; ?>>&nbsp;<?=s('Male')?></label>
						<label style="display:inline-block"><input type="radio" name="gender" id="genf" value="f"<?php if($p_g=='f') echo ' checked="checked"'; ?>>&nbsp;<?=s('Female')?></label>
					</td>
				</tr>
				<tr>
					<td><?=s('Birthdate')?></td>
					<td>
<?php if($p_bday) { echo $user->birthday; ?>
	<input type="hidden" name="bdm" value="<?=$p_bday[0]?>">
	<input type="hidden" name="bdd" value="<?=$p_bday[1]?>">
	<input type="hidden" name="bdy" value="<?=$p_bday[2]?>">
<?php } else { ?>
						<select name="bdm">
							<option value="1"><?=s('January')?></option>
							<option value="2"><?=s('February')?></option>
							<option value="3"><?=s('March')?></option>
							<option value="4"><?=s('April')?></option>
							<option value="5"><?=s('May')?></option>
							<option value="6"><?=s('June')?></option>
							<option value="7"><?=s('July')?></option>
							<option value="8"><?=s('August')?></option>
							<option value="9"><?=s('September')?></option>
							<option value="10"><?=s('October')?></option>
							<option value="11"><?=s('November')?></option>
							<option value="12"><?=s('December')?></option>
						</select>
						<select name="bdd">
<?php for($d=1;$d<=31;$d++) echo '<option value="'.$d.'">'.$d.'</option>'; ?>
						</select>
						<select name="bdy">
<?php for($y=intval(date("Y"))-12;$y>1900;$y--) echo '<option value="'.$y.'">'.$y.'</option>'; ?>
						</select>
<?php } ?>
					</td>
				</tr>
				<tr>
					<td><?php s('Country'); ?></td>
					<td>
						<select name="country">
<?php $ca = file("inc/countries.txt",FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES); foreach($ca as $cs) echo '<option value="'.$cs.'">'.$cs.'</option>'; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><?php s('Time Zone'); ?></td>
					<td>
<select name="timezone">
<option value="Etc/GMT+12">S (GMT-12:00) International Date Line West</option>
<option value="Pacific/Apia">S (GMT-11:00) Midway Island, Samoa</option>
<option value="Pacific/Honolulu">S (GMT-10:00) Hawaii</option>
<option value="America/Anchorage">D (GMT-09:00) Alaska</option>
<option value="America/Los_Angeles">D (GMT-08:00) Pacific Time (US & Canada); Tijuana</option>
<option value="America/Phoenix">S (GMT-07:00) Arizona</option>
<option value="America/Denver" selected="selected">D (GMT-07:00) Mountain Time (US & Canada)</option>
<option value="America/Mazatlan">D (GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
<option value="America/Regina">S (GMT-06:00) Central America</option>
<option value="America/Swift_Current">S (GMT-06:00) Saskatchewan</option>
<option value="America/Chicago">D (GMT-06:00) Central Time (US & Canada)</option>
<option value="America/Indianapolis">S (GMT-05:00) Indiana (East)</option>
<option value="America/Bogota">S (GMT-05:00) Bogota, Lima, Quito</option>
<option value="America/New_York">D (GMT-05:00) Eastern Time (US & Canada)</option>
<option value="America/Halifax">D (GMT-04:00) Atlantic Time (Canada)</option>
<option value="America/Santiago">D (GMT-04:00) Santiago</option>
<option value="America/St_Johns">D (GMT-03:30) Newfoundland</option>
<option value="America/Buenos_Aires">S (GMT-03:00) Buenos Aires, Georgetown</option>
<option value="America/Godthab">D (GMT-03:00) Brasilia / Greenland</option>
<option value="America/Noronha">D (GMT-02:00) Mid-Atlantic</option>
<option value="Atlantic/Cape_Verde">S (GMT-01:00) Cape Verde Is.</option>
<option value="Atlantic/Azores">D (GMT-01:00) Azores</option>
<option value="Africa/Casablanca">S (GMT) Casablanca, Monrovia</option>
<option value="Europe/London">D (GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option>
<option value="Africa/Lagos">S (GMT+01:00) West Central Africa</option>
<option value="Europe/Berlin">D (GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
<option value="Europe/Paris">D (GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
<option value="Europe/Sarajevo">D (GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
<option value="Europe/Belgrade">D (GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
<option value="Africa/Johannesburg">S (GMT+02:00) Harare, Pretoria</option>
<option value="Asia/Jerusalem">S (GMT+02:00) Jerusalem</option>
<option value="Europe/Istanbul">D (GMT+02:00) Athens, Istanbul, Minsk</option>
<option value="Europe/Helsinki">D (GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
<option value="Europe/Bucharest">D (GMT+02:00) Bucharest</option>
<option value="Africa/Cairo">D (GMT+02:00) Cairo</option>
<option value="Africa/Nairobi">S (GMT+03:00) Nairobi</option>
<option value="Asia/Kuwait">S (GMT+03:00) Kuwait, Riyadh</option>
<option value="Asia/Baghdad">S (GMT+03:00) Baghdad</option>
<option value="Europe/Moscow">D (GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
<option value="Asia/Tehran">D (GMT+03:30) Tehran</option>
<option value="Asia/Muscat">S (GMT+04:00) Abu Dhabi, Muscat</option>
<option value="Asia/Tbilisi">D (GMT+04:00) Baku, Tbilisi, Yerevan</option>
<option value="Asia/Kabul">S (GMT+04:30) Kabul</option>
<option value="Asia/Karachi">S (GMT+05:00) Islamabad, Karachi, Tashkent</option>
<option value="Asia/Yekaterinburg">D (GMT+05:00) Ekaterinburg</option>
<option value="Asia/Calcutta">S (GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
<option value="Asia/Katmandu">S (GMT+05:45) Kathmandu</option>
<option value="Asia/Colombo">S (GMT+06:00) Sri Jayawardenepura</option>
<option value="Asia/Dhaka">S (GMT+06:00) Astana, Dhaka</option>
<option value="Asia/Novosibirsk">D (GMT+06:00) Almaty, Novosibirsk</option>
<option value="Asia/Rangoon">S (GMT+06:30) Rangoon</option>
<option value="Asia/Bangkok">S (GMT+07:00) Bangkok, Hanoi, Jakarta</option>
<option value="Asia/Krasnoyarsk">D (GMT+07:00) Krasnoyarsk</option>
<option value="Asia/Taipei">S (GMT+08:00) Taipei</option>
<option value="Australia/Perth">S (GMT+08:00) Perth</option>
<option value="Asia/Singapore">S (GMT+08:00) Kuala Lumpur, Singapore</option>
<option value="Asia/Hong_Kong">S (GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
<option value="Asia/Irkutsk">D (GMT+08:00) Irkutsk, Ulaan Bataar</option>
<option value="Asia/Tokyo">S (GMT+09:00) Osaka, Sapporo, Tokyo</option>
<option value="Asia/Seoul">S (GMT+09:00) Seoul</option>
<option value="Asia/Yakutsk">D (GMT+09:00) Yakutsk</option>
<option value="Australia/Darwin">S (GMT+09:30) Darwin</option>
<option value="Australia/Adelaide">D (GMT+09:30) Adelaide</option>
<option value="Pacific/Guam">S (GMT+10:00) Guam, Port Moresby</option>
<option value="Australia/Brisbane">S (GMT+10:00) Brisbane</option>
<option value="Australia/Sydney">D (GMT+10:00) Canberra, Melbourne, Sydney</option>
<option value="Australia/Hobart">D (GMT+10:00) Hobart</option>
<option value="Asia/Vladivostok">D (GMT+10:00) Vladivostok</option>
<option value="Asia/Magadan">S (GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
<option value="Pacific/Fiji">S (GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
<option value="Pacific/Auckland">D (GMT+12:00) Auckland, Wellington</option>
<option value="Pacific/Tongatapu">S (GMT+13:00) Nuku&#39;alofa</option>
</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php echo recaptcha_get_html($publickey); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="<?=s('Sign Up')?>" /></td>
				</tr>
			</table>
		</form>
	</div>
	<div class="fltrt guest-col2">
		<h3 class="margin-top:10px;"><?=s('features')?></h3>
		<p><?=s('feature 1')?></p>
		<p><?=s('feature 2')?></p>
		<p><?=s('feature 3')?></p>
	</div>
	<br class="clr">
</div>
<?php foot(); ?>