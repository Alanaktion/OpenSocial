<?php // OpenSocial Configuration

// Database Variables
$db_host = 'localhost';
$db_user = 'opensocial';
$db_pass = 'password';
$db_name = 'opensocial';

// Site Name
$sitename = 'OpenSocial';

// Recaptcha API Keys
$publickey  = '';
$privatekey = '';

// Addresses
$a_home = 'http://www.example.com/'; // include trailing slash | Ex: http://www.example.com/
$a_tiny = 'http://example.com/';     // include trailing slash, should be shortest form of root path
$a_ajax = 'http://example.com/ajax.php'; // absolute static path to AJAX handler

// Cookie Domain
$dmcook = '.example.com';

// Absolute path to network installation
$a_dir  = '/var/www/html/'; // include trailing slash

// Administrator username
$u_admin = 'admin';

//
//  END OF BASIC CONFIGURATION, YOU SHOULD PROBABLY STOP HERE
//

// Load User Settings and update timeout
if(isset($_COOKIE['xsauth'])) {
	$u_id = substr($_COOKIE['xsauth'],0,16);
	$u_name = base64_decode(substr($_COOKIE['xsauth'],16));
	@setcookie('xsauth',$_COOKIE['xsauth'],time()+60*60*24*30,'/',$dmcook);
} else
	$u_name='';
if(isset($_REQUEST['hl'])){
		$u_lang=$_REQUEST['hl'];
		@setcookie('xslang',$u_lang,time()+60*60*24*30,'/',$dmcook);
} else {
	if(isset($_COOKIE['xslang'])) {
		$u_lang=$_COOKIE['xslang'];
		@setcookie("xslang",$u_lang,time()+60*60*24*30,"/",$dmcook);
	} else {
		$u_lang='en-us';
		@setcookie("xslang",$u_lang,time()+60*60*24*30,"/",$dmcook);
	}
}

if($u_lang && $u_lang!='en-us')
	@include 'lang/'.$u_lang.'.php';
else
	@include 'lang/en-us.php';

require_once 'inc/recaptchalib.php';

// Print or return text in user's current language
function s($text,$return = true) {
	if($trans = $GLOBALS['t'][$text])
		if($return) return $trans;
		else echo $trans;
	else
		if($return) return $text;
		else echo $text;
}

// Prepare X Class
require_once('inc/class.x.php');

// Detect iOS for use of advanced features
$agent = $_SERVER['HTTP_USER_AGENT'];
$is_ios = (stripos($agent,'iPod') || stripos($agent,'iPad') || stripos($agent,'iPhone'));

// Parse page request
$pg = explode('/',$_GET['p']);

// Check for logout
if($pg[0]=='logout') {
	@setcookie('xsauth','',time()-3600*24,'/',$dmcook);
	header('Location: '.$a_home);
}

mysql_connect($db_host,$db_user,$db_pass);
mysql_select_db($db_name);

// Load user info and set up time zone support
mysql_query("SET time_zone = 'US/Mountain'");
date_default_timezone_set('America/Denver');
$timeoffset = 0;
if($u_id) {
	$t_result = mysql_query("SELECT timezone,flags,fname,lname FROM mbasic WHERE uid = '{$u_id}' LIMIT 1");
	$t_array = @mysql_fetch_array($t_result);
	if(!$t_array) {
		$u_id   = false;
		$u_name = false;	
	}
	if($t_array[0]!='America/Denver' && $t_array[0]) {
		$remote_dtz = new DateTimeZone($t_array[0]);
		$remote_dt = new DateTime('now',$remote_dtz);
		$timeoffset = date('Z') - $remote_dtz->getOffset($remote_dt);
		unset($remote_dtz,$remote_dt);
	}
	$u_flags = unserialize(stripslashes($t_array[1]));
	$u_fname = $t_array[2];
	$u_lname = $t_array[3];
	mysql_free_result($t_result);
	unset($t_result,$t_array);
	
	// Load application authentication for the current user
	$q = mysql_query("SELECT * FROM appauth WHERE uid = '{$u_id}'");
	$u_apps = array();
	if(mysql_num_rows($q))
		while($r = mysql_fetch_assoc($q))
			$u_apps[$r['app']] = $r;
}

// Set up MySQL Injection Protection
function cleanQuery($string){if(get_magic_quotes_gpc()){$string=stripslashes($string);}if(phpversion()>='4.3.0'){$string=mysql_real_escape_string($string);}else{$string=mysql_escape_string($string);}return $string;}

// Include page elements
function head() {extract($GLOBALS); if($_GET['rel']!='ajax') require 'inc/head.php';}
function top()  {extract($GLOBALS); if($_GET['rel']!='ajax') require 'inc/top.php';}
function foot() {extract($GLOBALS); if($_GET['rel']!='ajax') require 'inc/foot.php';}
?>