	<footer>
		<section>
			&copy; <?=date('Y')?> <a href="#">Xusix</a><?php if($u_name=='alan') echo ' <span class="light">&middot;</span> <a href="'.$a_home.'a">Administration</a>'; ?><br>
			<div class="fb-like" data-href="http://www.xusix.com/" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false" data-font="arial"></div>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script><div class="g-plusone" data-size="small" data-href="http://www.xusix.com/"></div>
		</section>
		<nav>
			<a href="http://www.alanaktion.com/legal/tos">Terms of Service</a> | <a href="http://www.alanaktion.com/legal/privacy">Privacy Policy</a> | <a href="<?=$a_home?>help">Help</a>
		</nav>
		<br class="clr">
	</footer>
<script type="text/javascript">
<?php if($_COOKIE['meebo']) echo 'Meebo("domReady");'; ?>
// INTERNET DEFENSE LEAGUE
    window._idl = {};
    _idl.variant = "modal";
    (function() {
        var idl = document.createElement('script');
        idl.type = 'text/javascript';
        idl.async = true;
        idl.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'members.internetdefenseleague.org/include/?url=' + (_idl.url || '') + '&campaign=' + (_idl.campaign || '') + '&variant=' + (_idl.variant || 'banner');
        document.getElementsByTagName('body')[0].appendChild(idl);
    })();
</script>
</body>
</html>