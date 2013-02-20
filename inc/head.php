<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width,maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="apple-touch-icon" href="<?=$a_home?>img/_icon.png">
<link rel="apple-touch-startup-image" href="<?=$a_home?>img/idefault.png">
<link rel="stylesheet" type="text/css" href="<?=$a_home?>normalize.min.css" media="all">
<link rel="stylesheet" type="text/css" href="<?=$a_home?>style.css?v=304" media="all">
<link rel="stylesheet" type="text/css" href="<?=$a_home?>style-m.css" media="screen and (max-device-width: 480px),(max-device-width: 640px),(max-width: 640px)">
<!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="<?=$a_home?>ie6.css" media="all"><![endif]-->
<?php
	if($u_id) {
		foreach($u_flags as &$f)
			$f = is_numeric($f) ? floatval($f) : $f;
		echo '<script type="text/javascript">var u_flags = '.json_encode($u_flags).';</script>';
		$qry = mysql_query("SELECT ads,meebo FROM settings WHERE uid = '".$u_id."' LIMIT 1");
		$arr = mysql_fetch_array($qry);
		$ads = $arr['ads'];
		$meebo = $arr['meebo'];
		mysql_free_result($qry);
		unset($qry,$arr);
	}
	if(!$is_ios) echo '<link rel="stylesheet" type="text/css" href="'.$a_home.'emoji.css">';
?>
<link rel="shortcut icon" href="<?=$a_home?>favicon.ico">
<?php if($meebo==1) { ?>
<script type="text/javascript">
window.Meebo||function(c){function p(){return["<",i,' onload="var d=',g,";d.getElementsByTagName('head')[0].",j,"(d.",h,"('script')).",k,"='//cim.meebo.com/cim?iv=",a.v,"&",q,"=",c[q],c[l]?"&"+l+"="+c[l]:"",c[e]?"&"+e+"="+c[e]:"","'\"></",i,">"].join("")}var f=window,a=f.Meebo=f.Meebo||function(){(a._=a._||[]).push(arguments)},d=document,i="body",m=d[i],r;if(!m){r=arguments.callee;return setTimeout(function(){r(c)},100)}a.$={0:+new Date};a.T=function(u){a.$[u]=new Date-a.$[0]};a.v=5;var j="appendChild",h="createElement",k="src",l="lang",q="network",e="domain",n=d[h]("div"),v=n[j](d[h]("m")),b=d[h]("iframe"),g="document",o,s=function(){a.T("load");a("load")};f.addEventListener?f.addEventListener("load",s,false):f.attachEvent("onload",s);n.style.display="none";m.insertBefore(n,m.firstChild).id="meebo";b.frameBorder="0";b.name=b.id="meebo-iframe";b.allowTransparency="true";v[j](b);try{b.contentWindow[g].open()}catch(w){c[e]=d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";b[k]=o+"void(0);"}try{var t=b.contentWindow[g];t.write(p());t.close()}catch(x){b[k]=o+'d.write("'+p().replace(/"/g,'\\"')+'");d.close();'}a.T(1)}({network:"xusixsocial_tu66ru"});
</script>
<?php } ?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="<?=$a_home?>jq.resize.js"></script>
<script type="text/javascript" src="<?=$a_home?>jq.oembed.min.js"></script>
<script type="text/javascript" src="<?=$a_home?>jquery.hasEventListener-2.0.3.min.js"></script>
<script type="text/javascript" src="<?=$a_home?>xus.js"></script>
<?php if($u_flags['pushState']) { ?><script type="text/javascript" src="<?=$a_home?>go.js"></script><?php } ?>
<!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
