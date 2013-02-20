<?php
header('HTTP/1.0 404 Not Found');
head();
?>
<title>Not Found - Xusix</title>
<?php top(); ?>
<div id="main">
	<h2>The page you requested was not found.</h2>
	<br>
	<p>You may have clicked an expired link or mistyped the address. Some web addresses are case sensitive.</p>
	<ul>
		<li><a href="<?=$a_home?>">Return home</a></li>
		<li><a href="<?=$_SERVER['HTTP_REFERER']?>">Go back to the previous page</a></li>
	</ul>
</div>
<?php foot(); ?>