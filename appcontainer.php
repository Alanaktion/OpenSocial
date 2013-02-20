<?php
	include 'inc/applist.php';
	head();
?>
<title><?=$apps[$pg[1]][0]?> - Xusix</title>
<?php top(); ?>
<div id="main">
	<iframe src="<?=$apps[$pg[1]][2]?>" id="appframe" class="appframe" frameborder="0" allowtransparency="true"></iframe>
</div>
<?php foot(); ?>